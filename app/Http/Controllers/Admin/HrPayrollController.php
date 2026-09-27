<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HrPayroll;
use App\Models\HrPayrollDetail;
use App\Models\HrEmployee;
use App\Models\HrLoan;
use App\Models\HrEmployeeFinancial;
use App\Models\HrFinancialType;
use App\Models\HrAttendance;
use App\Models\HrLeaveRequest;
use App\Models\HrContract;
use App\Models\HrContractAllowance;
use App\Models\FinancialAccount;
use App\Models\JournalEntry;
use App\Models\JournalItem;
use App\Models\Payment; // 🆕 إضافة موديل Payment
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class HrPayrollController extends Controller
{
    // ==========================================
    // 🔥 1. عرض مسير الرواتب 🔥
    // ==========================================
    public function index(Request $request)
    {
        abort_if(!auth()->user()->can('manage_payrolls'), 403, 'غير مصرح لك بإدارة مسيرات الرواتب.');

        $month = $request->input('month', date('m'));
        $month = is_numeric($month) ? (int)$month : (int)date('m');
        
        $year = $request->input('year', date('Y'));
        $year = is_numeric($year) ? (int)$year : (int)date('Y');
        
        $status = $request->input('status');
        $employeeId = $request->input('employee_id');

        $query = HrPayroll::with(['employee.department', 'financialAccount', 'paidBy'])
                    ->where('month', $month)
                    ->where('year', $year);

        if ($status) {
            $query->where('status', $status);
        }
        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        $payrolls = $query->orderBy('employee_id')->get();
        $employees = HrEmployee::where('status', 'active')->with('department')->orderBy('full_name_ar')->get();
        $paymentMethods = FinancialAccount::where('account_type', 'asset')->where('is_active', 1)->get();

        // إحصائيات
        $totalPayrolls = $payrolls->count();
        $totalNetSalary = $payrolls->sum('net_salary');
        $paidCount = $payrolls->where('status', 'paid')->count();
        $draftCount = $payrolls->where('status', 'draft')->count();
        $totalPaid = $payrolls->where('status', 'paid')->sum('net_salary');
        $totalPending = $payrolls->where('status', '!=', 'paid')->sum('net_salary');
        
        $unpostedCount = HrPayroll::where('month', $month)
            ->where('year', $year)
            ->where('status', 'paid')
            ->whereNull('transaction_id')
            ->count();

        return view('backend.hr.payroll.index', compact(
            'payrolls', 'month', 'year', 'status', 'employeeId',
            'employees', 'paymentMethods',
            'totalPayrolls', 'totalNetSalary', 'paidCount', 'draftCount',
            'totalPaid', 'totalPending', 'unpostedCount'
        ));
    }

    // ==========================================
    // 🔥 2. توليد الرواتب أوتوماتيكياً 🔥
    // ==========================================
    public function generate(Request $request)
    {
        abort_if(!auth()->user()->can('manage_payrolls'), 403);

        $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020|max:2100',
        ]);

        $month = $request->month;
        $year = $request->year;
        $startDate = Carbon::create($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();
        $workingDaysInMonth = $startDate->diffInDays($endDate) + 1;

        $employees = HrEmployee::where('status', 'active')
            ->with(['contractAllowances.financialType', 'department'])
            ->get();

        $generatedCount = 0;
        $skippedCount = 0;

        DB::beginTransaction();
        try {
            $loanFinancialType = HrFinancialType::firstOrCreate(
                ['name_ar' => 'سداد سلفة موظف', 'type' => 'deduction'],
                ['value_type' => 'fixed']
            );

            foreach ($employees as $emp) {
                if (HrPayroll::where('employee_id', $emp->id)
                    ->where('month', $month)
                    ->where('year', $year)
                    ->exists()) {
                    $skippedCount++;
                    continue;
                }

                $dailyRate = $emp->basic_salary / 30;

                // 🟢 حساب العلاوات
                $contractAllowances = HrContractAllowance::where('hr_contract_id', function($q) use ($emp) {
                        $q->select('id')->from('hr_contracts')
                          ->where('employee_id', $emp->id)
                          ->where('status', 'valid')
                          ->orderBy('start_date', 'desc')
                          ->limit(1);
                    })
                    ->whereHas('financialType', fn($q) => $q->where('type', 'allowance'))
                    ->sum('amount');

                $variableAllowances = HrEmployeeFinancial::where('employee_id', $emp->id)
                    ->where('status', 'pending')
                    ->whereNull('payroll_id')
                    ->whereMonth('action_date', $month)
                    ->whereYear('action_date', $year)
                    ->whereHas('financialType', fn($q) => $q->where('type', 'allowance'))
                    ->sum('amount');

                $totalAllowances = $contractAllowances + $variableAllowances;

                // 🔴 حساب الخصومات
                $absentDays = HrAttendance::where('employee_id', $emp->id)
                    ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                    ->where('status', 'absent')
                    ->count();

                $unpaidLeaves = HrLeaveRequest::where('employee_id', $emp->id)
                    ->where('status', 'approved')
                    ->whereHas('leaveType', fn($q) => $q->where('is_paid', false))
                    ->where(function($q) use ($startDate, $endDate) {
                        $q->whereBetween('start_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                          ->orWhereBetween('end_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
                    })->sum('total_days');

                $netAbsentDays = $absentDays + $unpaidLeaves;
                $absentDeduction = $netAbsentDays * $dailyRate;

                $totalDelayMinutes = HrAttendance::where('employee_id', $emp->id)
                    ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                    ->sum('delay_minutes');
                
                $delayDeductionDays = floor($totalDelayMinutes / 30);
                $delayDeduction = $delayDeductionDays * $dailyRate;

                $adminDeductions = HrEmployeeFinancial::where('employee_id', $emp->id)
                    ->where('status', 'pending')
                    ->whereNull('payroll_id')
                    ->whereMonth('action_date', $month)
                    ->whereYear('action_date', $year)
                    ->whereHas('financialType', fn($q) => $q->where('type', 'deduction'))
                    ->where('financial_type_id', '!=', $loanFinancialType->id)
                    ->sum('amount');

                $pasiDeduction = 0;
                if ($emp->nationality === 'عماني' && $emp->is_insured) {
                    $pasiDeduction = round($emp->basic_salary * 0.07, 3);
                }

                // 🔵 أقساط السلف
                $loanInstallment = 0;
                $activeLoans = HrLoan::where('employee_id', $emp->id)
                    ->where('status', 'active')
                    ->where('start_deduction_month', '<=', $endDate->format('Y-m-d'))
                    ->get();

                foreach ($activeLoans as $loan) {
                    $remaining = $loan->loan_amount - $loan->paid_amount;
                    $deduct = min($loan->monthly_installment, $remaining);
                    
                    if ($deduct > 0) {
                        $loanInstallment += $deduct;

                        HrEmployeeFinancial::create([
                            'employee_id' => $emp->id,
                            'financial_type_id' => $loanFinancialType->id,
                            'amount' => $deduct,
                            'action_date' => $endDate->format('Y-m-d'),
                            'notes' => "قسط سلفة #{$loan->id} لشهر {$month}/{$year}",
                            'status' => 'pending',
                        ]);
                    }
                }

                $totalDeductions = $absentDeduction + $delayDeduction + $adminDeductions + $pasiDeduction + $loanInstallment;

                // 🟡 العمل الإضافي
                $totalOvertimeMinutes = HrAttendance::where('employee_id', $emp->id)
                    ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                    ->sum('overtime_minutes');
                
                $hourlyRate = $dailyRate / 8;
                $overtimePay = round(($totalOvertimeMinutes / 60) * $hourlyRate * 1.25, 3);

                // 🟣 حساب الصافي
                $grossSalary = $emp->basic_salary + $totalAllowances + $overtimePay;
                $netSalary = max(0, $grossSalary - $totalDeductions);

                // 📝 إنشاء سجل الراتب
                $payroll = HrPayroll::create([
                    'employee_id'       => $emp->id,
                    'month'             => $month,
                    'year'              => $year,
                    'working_days'      => $workingDaysInMonth - $netAbsentDays,
                    'absent_days'       => $netAbsentDays,
                    'pasi_deduction'    => round($pasiDeduction, 3),
                    'basic_salary'      => $emp->basic_salary,
                    'total_allowances'  => round($totalAllowances, 3),
                    'total_deductions'  => round($totalDeductions, 3),
                    'loan_deduction'    => round($loanInstallment, 3),
                    'net_salary'        => round($netSalary, 3),
                    'status'            => 'draft',
                    'transaction_id'    => null,
                ]);

                // ربط الحركات المعلقة
                HrEmployeeFinancial::where('employee_id', $emp->id)
                    ->where('status', 'pending')
                    ->whereNull('payroll_id')
                    ->whereMonth('action_date', $month)
                    ->whereYear('action_date', $year)
                    ->update(['payroll_id' => $payroll->id]);

                // 📋 تفاصيل الراتب
                $details = [];

                $details[] = [
                    'hr_payroll_id' => $payroll->id,
                    'item_name' => 'الراتب الأساسي',
                    'type' => 'allowance',
                    'amount' => $emp->basic_salary,
                ];

                if ($contractAllowances > 0) {
                    $details[] = [
                        'hr_payroll_id' => $payroll->id,
                        'item_name' => 'علاوات ثابتة (من العقد)',
                        'type' => 'allowance',
                        'amount' => round($contractAllowances, 3),
                    ];
                }

                if ($variableAllowances > 0) {
                    $details[] = [
                        'hr_payroll_id' => $payroll->id,
                        'item_name' => 'علاوات متغيرة',
                        'type' => 'allowance',
                        'amount' => round($variableAllowances, 3),
                    ];
                }

                if ($overtimePay > 0) {
                    $details[] = [
                        'hr_payroll_id' => $payroll->id,
                        'item_name' => "عمل إضافي ({$totalOvertimeMinutes} دقيقة)",
                        'type' => 'allowance',
                        'amount' => $overtimePay,
                    ];
                }

                if ($absentDeduction > 0) {
                    $details[] = [
                        'hr_payroll_id' => $payroll->id,
                        'item_name' => "خصم غياب ({$netAbsentDays} يوم)",
                        'type' => 'deduction',
                        'amount' => round($absentDeduction, 3),
                    ];
                }

                if ($delayDeduction > 0) {
                    $details[] = [
                        'hr_payroll_id' => $payroll->id,
                        'item_name' => "خصم تأخير ({$totalDelayMinutes} دقيقة)",
                        'type' => 'deduction',
                        'amount' => round($delayDeduction, 3),
                    ];
                }

                if ($pasiDeduction > 0) {
                    $details[] = [
                        'hr_payroll_id' => $payroll->id,
                        'item_name' => 'التأمينات الاجتماعية (7%)',
                        'type' => 'deduction',
                        'amount' => round($pasiDeduction, 3),
                    ];
                }

                if ($adminDeductions > 0) {
                    $details[] = [
                        'hr_payroll_id' => $payroll->id,
                        'item_name' => 'خصومات إدارية (جزاءات)',
                        'type' => 'deduction',
                        'amount' => round($adminDeductions, 3),
                    ];
                }

                if ($loanInstallment > 0) {
                    $details[] = [
                        'hr_payroll_id' => $payroll->id,
                        'item_name' => 'أقساط سلف',
                        'type' => 'deduction',
                        'amount' => round($loanInstallment, 3),
                    ];
                }

                HrPayrollDetail::insert($details);
                $generatedCount++;
            }

            DB::commit();
            
            $message = "تم توليد {$generatedCount} راتب بنجاح!";
            if ($skippedCount > 0) {
                $message .= " (تم تخطي {$skippedCount} راتب موجود مسبقاً)";
            }
            
            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'خطأ في التوليد: ' . $e->getMessage());
        }
    }

    // ==========================================
    // 🔥 3. صرف راتب فردي 🔥
    // ==========================================
    public function paySingle(Request $request, $id)
    {
        abort_if(!auth()->user()->can('manage_payrolls'), 403);

        $request->validate([
            'financial_account_id' => 'required|exists:financial_accounts,id',
            'signature_base64' => 'nullable|string',
        ]);

        $payroll = HrPayroll::with('employee')->findOrFail($id);

        if ($payroll->status == 'paid') {
            return redirect()->back()->with('error', 'تم صرف هذا الراتب مسبقاً!');
        }

        $account = FinancialAccount::lockForUpdate()->find($request->financial_account_id);
        if (!$account || $account->balance < $payroll->net_salary) {
            return redirect()->back()->with('error', 'رصيد الحساب غير كافي للصرف!');
        }

        DB::beginTransaction();
        try {
            $signaturePath = null;
            if ($request->filled('signature_base64')) {
                $imageData = $request->signature_base64;
                if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $type)) {
                    $imageData = substr($imageData, strpos($imageData, ',') + 1);
                    $type = strtolower($type[1]);
                    $imageData = base64_decode($imageData);
                    if ($imageData === false) {
                        throw new \Exception('فشل فك تشفير التوقيع');
                    }
                    $fileName = 'payroll_' . $payroll->id . '_' . time() . '.' . $type;
                    $path = 'payroll_signatures/' . $fileName;
                    Storage::disk('public')->put($path, $imageData);
                    $signaturePath = $path;
                }
            }

            $payroll->update([
                'status' => 'paid',
                'payment_date' => Carbon::now()->format('Y-m-d'),
                'financial_account_id' => $request->financial_account_id,
                'signature_path' => $signaturePath,
                'paid_by' => auth()->id(),
                'transaction_id' => null,
            ]);

            // خصم من الحساب
            $account->decrement('balance', $payroll->net_salary);

            // 🔥 تسجيل الحركة في جدول payments (عشان تظهر في كشف الحساب)
            Payment::create([
                'payable_type'         => HrPayroll::class,
                'payable_id'           => $payroll->id,
                'financial_account_id' => $account->id,
                'user_id'              => auth()->id(),
                'payment_type'         => 'payroll_payment',
                'amount'               => -$payroll->net_salary, // سالب لأنه خصم
                'payment_date'         => Carbon::now()->format('Y-m-d'),
                'transaction_details'  => "صرف راتب: {$payroll->employee->full_name_ar} - شهر {$payroll->month}/{$payroll->year}",
                'receipt_number'       => 'SAL-' . $payroll->id . '-' . time(),
                'notes'                => $payroll->employee->full_name_ar,
            ]);

            HrEmployeeFinancial::where('payroll_id', $payroll->id)
                ->where('status', 'pending')
                ->update(['status' => 'processed']);

            if ($payroll->loan_deduction > 0) {
                $this->processLoanDeduction($payroll);
            }

            DB::commit();
            return redirect()->back()->with('success', 'تم صرف الراتب بنجاح! (القيد المحاسبي جاهز للترحيل)');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'خطأ في الصرف: ' . $e->getMessage());
        }
    }

    // ==========================================
    // 🔥 4. صرف جماعي للرواتب 🔥
    // ==========================================
    public function payBulk(Request $request)
    {
        abort_if(!auth()->user()->can('manage_payrolls'), 403);

        $request->validate([
            'month' => 'required|integer',
            'year' => 'required|integer',
            'financial_account_id' => 'required|exists:financial_accounts,id',
            'payroll_ids' => 'required|array|min:1',
        ]);

        $account = FinancialAccount::lockForUpdate()->find($request->financial_account_id);
        $payrolls = HrPayroll::whereIn('id', $request->payroll_ids)
            ->where('status', 'draft')
            ->where('month', $request->month)
            ->where('year', $request->year)
            ->with('employee')
            ->get();

        $totalAmount = $payrolls->sum('net_salary');

        if ($account->balance < $totalAmount) {
            return redirect()->back()->with('error', 'رصيد الحساب غير كافي! المطلوب: ' . number_format($totalAmount, 3) . ' ر.ع');
        }

        DB::beginTransaction();
        try {
            foreach ($payrolls as $payroll) {
                $payroll->update([
                    'status' => 'paid',
                    'payment_date' => Carbon::now()->format('Y-m-d'),
                    'financial_account_id' => $request->financial_account_id,
                    'paid_by' => auth()->id(),
                    'transaction_id' => null,
                ]);

                HrEmployeeFinancial::where('payroll_id', $payroll->id)
                    ->where('status', 'pending')
                    ->update(['status' => 'processed']);

                // 🔥 تسجيل الحركة في جدول payments لكل راتب
                Payment::create([
                    'payable_type'         => HrPayroll::class,
                    'payable_id'           => $payroll->id,
                    'financial_account_id' => $account->id,
                    'user_id'              => auth()->id(),
                    'payment_type'         => 'payroll_payment',
                    'amount'               => -$payroll->net_salary,
                    'payment_date'         => Carbon::now()->format('Y-m-d'),
                    'transaction_details'  => "صرف راتب: {$payroll->employee->full_name_ar} - شهر {$payroll->month}/{$payroll->year}",
                    'receipt_number'       => 'SAL-' . $payroll->id . '-' . time(),
                    'notes'                => $payroll->employee->full_name_ar,
                ]);

                if ($payroll->loan_deduction > 0) {
                    $this->processLoanDeduction($payroll);
                }
            }

            $account->decrement('balance', $totalAmount);

            DB::commit();
            return redirect()->back()->with('success', 'تم صرف ' . $payrolls->count() . ' راتب بنجاح! (القيود جاهزة للترحيل)');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'خطأ: ' . $e->getMessage());
        }
    }

    // ==========================================
    // 🔥 5. ترحيل القيود المحاسبية 🔥
    // ==========================================
    public function postEntries(Request $request)
    {
        abort_if(!auth()->user()->can('manage_payrolls'), 403);

        $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020|max:2100',
        ]);

        $month = $request->month;
        $year = $request->year;

        if (!class_exists('\App\Models\JournalEntry')) {
            return redirect()->back()->with('error', 'جداول القيود المحاسبية غير موجودة في النظام!');
        }

        $payrolls = HrPayroll::where('month', $month)
            ->where('year', $year)
            ->where('status', 'paid')
            ->whereNull('transaction_id')
            ->with('employee')
            ->get();

        if ($payrolls->isEmpty()) {
            return redirect()->back()->with('warning', 'لا توجد رواتب جديدة لترحيلها! جميع الرواتب المصروفة مرحّلة بالفعل.');
        }

        DB::beginTransaction();
        try {
            $postedCount = 0;

            foreach ($payrolls as $payroll) {
                $this->createPayrollJournalEntry($payroll);
                $postedCount++;
            }

            DB::commit();
            return redirect()->back()->with('success', "تم ترحيل {$postedCount} قيد محاسبي بنجاح!");

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'خطأ في الترحيل: ' . $e->getMessage());
        }
    }

    // ==========================================
    // 🔥 6. طباعة كشف الراتب 🔥
    // ==========================================
    public function printPayslip($id)
    {
        abort_if(!auth()->user()->can('view_payrolls'), 403);

        $payroll = HrPayroll::with([
            'employee.department',
            'details',
            'financialAccount',
            'paidBy'
        ])->findOrFail($id);

        $globalSettings = \App\Models\Setting::first();

        return view('backend.hr.payroll.payslip', compact('payroll', 'globalSettings'));
    }

    // ==========================================
    // 🔥 7. إلغاء الصرف (عكس العملية) 🔥
    // ==========================================
    public function reversePayment($id)
    {
        abort_if(!auth()->user()->can('manage_payrolls'), 403);

        $payroll = HrPayroll::with('employee')->findOrFail($id);

        if ($payroll->status != 'paid') {
            return redirect()->back()->with('error', 'هذا الراتب لم يُصرف بعد!');
        }

        DB::beginTransaction();
        try {
            if ($payroll->financial_account_id) {
                $account = FinancialAccount::lockForUpdate()->find($payroll->financial_account_id);
                if ($account) {
                    $account->increment('balance', $payroll->net_salary);
                    
                    // 🔥 تسجيل حركة عكسية (إيداع) في جدول payments
                    Payment::create([
                        'payable_type'         => HrPayroll::class,
                        'payable_id'           => $payroll->id,
                        'financial_account_id' => $account->id,
                        'user_id'              => auth()->id(),
                        'payment_type'         => 'payroll_reversal',
                        'amount'               => $payroll->net_salary, // موجب لأنه إيداع
                        'payment_date'         => Carbon::now()->format('Y-m-d'),
                        'transaction_details'  => "إلغاء صرف راتب: {$payroll->employee->full_name_ar} - شهر {$payroll->month}/{$payroll->year}",
                        'receipt_number'       => 'REV-SAL-' . $payroll->id . '-' . time(),
                        'notes'                => 'إلغاء صرف راتب',
                    ]);
                }
            }

            HrEmployeeFinancial::where('payroll_id', $payroll->id)
                ->update(['status' => 'pending']);

            if ($payroll->loan_deduction > 0) {
                $this->reverseLoanDeduction($payroll);
            }

            if ($payroll->transaction_id) {
                if (class_exists('\App\Models\JournalEntry')) {
                    JournalEntry::where('id', $payroll->transaction_id)->delete();
                    JournalItem::where('journal_entry_id', $payroll->transaction_id)->delete();
                }
            }

            $payroll->update([
                'status' => 'draft',
                'payment_date' => null,
                'paid_by' => null,
                'signature_path' => null,
                'transaction_id' => null,
                'financial_account_id' => null,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'تم إلغاء الصرف بنجاح!');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'خطأ: ' . $e->getMessage());
        }
    }

    // ==========================================
    // 🔥 دالة مساعدة: خصم السلف 🔥
    // ==========================================
    private function processLoanDeduction($payroll)
    {
        $amountToDeduct = $payroll->loan_deduction;
        $employeeId = $payroll->employee_id;

        $loans = HrLoan::where('employee_id', $employeeId)
            ->where('status', 'active')
            ->lockForUpdate()
            ->get();

        $financialRecords = HrEmployeeFinancial::where('payroll_id', $payroll->id)
            ->where('status', 'processed')
            ->whereHas('financialType', fn($q) => $q->where('name_ar', 'like', '%سداد سلفة%'))
            ->get();

        foreach ($loans as $loan) {
            if ($amountToDeduct <= 0) break;

            $remainingLoan = $loan->loan_amount - $loan->paid_amount;
            $deduct = min($remainingLoan, $amountToDeduct);

            if ($deduct > 0) {
                $loan->paid_amount += $deduct;
                $amountToDeduct -= $deduct;

                if ($loan->paid_amount >= $loan->loan_amount) {
                    $loan->status = 'completed';
                    $loan->completed_at = $payroll->payment_date;
                }

                $relatedRecord = $financialRecords->first(function($record) use ($loan) {
                    return str_contains($record->notes, "#{$loan->id} ");
                });

                if ($relatedRecord) {
                    $loan->financial_record_id = $relatedRecord->id;
                }

                $loan->save();
            }
        }
    }

    // ==========================================
    // 🔥 دالة مساعدة: تسجيل القيد المحاسبي 🔥
    // ==========================================
    private function createPayrollJournalEntry($payroll)
    {
        if (!class_exists('\App\Models\JournalEntry') || !class_exists('\App\Models\JournalItem')) {
            return;
        }

        $journalEntry = JournalEntry::create([
            'branch_id' => $payroll->employee->branch_id ?? 1,
            'entry_number' => 'JE-PR-' . $payroll->id . '-' . time(),
            'entry_date' => $payroll->payment_date,
            'description' => "راتب شهر {$payroll->month}/{$payroll->year} - {$payroll->employee->full_name_ar}",
            'reference_type' => HrPayroll::class,
            'reference_id' => $payroll->id,
            'created_by' => auth()->id(),
        ]);

        $expenseAccount = FinancialAccount::where('account_type', 'expense')
            ->where('name_ar', 'like', '%رواتب%')
            ->first();

        if ($expenseAccount) {
            JournalItem::create([
                'journal_entry_id' => $journalEntry->id,
                'financial_account_id' => $expenseAccount->id,
                'debit' => $payroll->net_salary,
                'credit' => 0,
                'line_description' => 'مصروف راتب',
            ]);
        }

        if ($payroll->financial_account_id) {
            JournalItem::create([
                'journal_entry_id' => $journalEntry->id,
                'financial_account_id' => $payroll->financial_account_id,
                'debit' => 0,
                'credit' => $payroll->net_salary,
                'line_description' => 'صرف من الحساب',
            ]);
        }

        $payroll->update(['transaction_id' => $journalEntry->id]);
    }

    // ==========================================
    // 🔥 دالة مساعدة: عكس خصم السلف 🔥
    // ==========================================
    private function reverseLoanDeduction($payroll)
    {
        $financialRecords = HrEmployeeFinancial::where('payroll_id', $payroll->id)
            ->whereHas('financialType', fn($q) => $q->where('name_ar', 'like', '%سداد سلفة%'))
            ->get();

        foreach ($financialRecords as $record) {
            $loan = HrLoan::where('financial_record_id', $record->id)->first();
            
            if ($loan) {
                $loan->paid_amount -= $record->amount;
                $loan->status = 'active';
                $loan->completed_at = null;
                $loan->financial_record_id = null;
                $loan->save();
            }

            $record->update(['status' => 'pending']);
        }
    }
}