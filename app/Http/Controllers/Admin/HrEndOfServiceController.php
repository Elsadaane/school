<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HrEndOfService;
use App\Models\HrEmployee;
use App\Models\HrLoan;
use App\Models\HrLeaveBalance;
use App\Models\FinancialAccount;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class HrEndOfServiceController extends Controller
{
    // ==========================================
    // 🔥 عرض سجل المخالصات 🔥
    // ==========================================
    public function index()
    {
        abort_if(!auth()->user()->can('manage_end_of_service'), 403);

        $eos_records = HrEndOfService::with('employee.department')
            ->orderBy('termination_date', 'desc')
            ->get();
        
        $employees = HrEmployee::whereIn('status', ['active', 'on_leave', 'suspended'])
            ->with('department')
            ->orderBy('full_name_ar')
            ->get(); 
        
        $paymentMethods = FinancialAccount::where('account_type', 'asset')
            ->where('is_active', 1)
            ->orderBy('name_ar')
            ->get();

        // إحصائيات
        $totalEOS = $eos_records->count();
        $totalPayout = $eos_records->sum('net_payout');
        $thisMonth = $eos_records->where('termination_date', '>=', now()->startOfMonth())->count();

        return view('backend.hr.eos.index', compact(
            'eos_records', 'employees', 'paymentMethods',
            'totalEOS', 'totalPayout', 'thisMonth'
        ));
    }

    // ==========================================
    // 🔥 دالة الحساب الآلي (AJAX) 🔥
    // ==========================================
    public function calculateDues($id)
    {
        abort_if(!auth()->user()->can('manage_end_of_service'), 403);

        $employee = HrEmployee::with(['loans' => function($q) {
            $q->where('status', 'active');
        }])->findOrFail($id);

        $dailyRate = $employee->basic_salary / 30;

        // 1. حساب سنوات الخدمة
        $joinDate = Carbon::parse($employee->join_date);
        $terminationDate = Carbon::now();
        $yearsOfService = $joinDate->diffInYears($terminationDate);
        $remainingMonths = $joinDate->diffInMonths($terminationDate) % 12;

        // 2. مكافأة نهاية الخدمة (قانون العمل العُماني)
        $gratuity = 0;
        if ($yearsOfService > 0) {
            if ($yearsOfService <= 3) {
                $gratuity = $yearsOfService * 15 * $dailyRate;
                $gratuity += ($remainingMonths / 12) * 15 * $dailyRate;
            } else {
                $gratuity = (3 * 15 * $dailyRate);
                $gratuity += (($yearsOfService - 3) * 30 * $dailyRate);
                $gratuity += ($remainingMonths / 12) * 30 * $dailyRate;
            }
        }

        // 3. بدل رصيد الإجازات المتبقي
        $remainingLeaves = HrLeaveBalance::where('employee_id', $employee->id)
            ->where('year', date('Y'))
            ->sum('remaining_days');
        
        $leaveEncashment = $remainingLeaves * $dailyRate;

        // 4. خصم السلف المتبقية
        $totalLoans = $employee->loans->sum(function($loan) {
            return $loan->loan_amount - $loan->paid_amount;
        });

        // 5. الصافي النهائي
        $netPayout = ($gratuity + $leaveEncashment) - $totalLoans;
        $netPayout = max(0, $netPayout);

        return response()->json([
            'success' => true,
            'data' => [
                'years_of_service' => round($yearsOfService + ($remainingMonths / 12), 2),
                'daily_rate' => round($dailyRate, 3),
                'gratuity' => round($gratuity, 3),
                'leave_encashment' => round($leaveEncashment, 3),
                'remaining_leaves' => $remainingLeaves,
                'loans' => round($totalLoans, 3),
                'net_payout' => round($netPayout, 3),
            ]
        ]);
    }

    // ==========================================
    // 🔥 حفظ المخالصة وإنهاء خدمة الموظف 🔥
    // ==========================================
    public function store(Request $request)
    {
        abort_if(!auth()->user()->can('manage_end_of_service'), 403);

        $validated = $request->validate([
            'employee_id'          => 'required|exists:hr_employees,id',
            'termination_date'     => 'required|date|before_or_equal:today',
            'reason'               => 'required|in:resignation,termination,contract_end,other',
            'gratuity_amount'      => 'required|numeric|min:0',
            'leave_encashment'     => 'required|numeric|min:0',
            'loan_deductions'      => 'required|numeric|min:0',
            'net_payout'           => 'required|numeric|min:0',
            'financial_account_id' => 'required|exists:financial_accounts,id', 
            'signature_base64'     => 'nullable|string',
            'notes'                => 'nullable|string|max:1000',
        ]);

        $account = FinancialAccount::lockForUpdate()->findOrFail($request->financial_account_id);
        
        // 🔥 الحماية المالية
        if ($account->balance < $request->net_payout) {
            return redirect()->back()
                ->withErrors(['error' => 'رصيد الحساب المالي لا يكفي! المتاح: ' . number_format($account->balance, 3) . ' ر.ع'])
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $employee = HrEmployee::findOrFail($request->employee_id);
            $signaturePath = null;

            // حفظ التوقيع الإلكتروني
            if ($request->filled('signature_base64')) {
                $imageData = $request->signature_base64;
                if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $type)) {
                    $imageData = substr($imageData, strpos($imageData, ',') + 1);
                    $type = strtolower($type[1]);
                    $imageData = base64_decode($imageData);
                    
                    if ($imageData === false) {
                        throw new \Exception('فشل فك تشفير التوقيع');
                    }
                    
                    $filename = 'eos_' . $employee->id . '_' . time() . '.' . $type;
                    $path = 'hr_eos_signatures/' . $filename;
                    Storage::disk('public')->put($path, $imageData);
                    $signaturePath = $path;
                }
            }

            // 1. إنشاء المصروف المحاسبي
            $expenseCategory = ExpenseCategory::firstOrCreate(
                ['name_ar' => 'مكافآت وتصفيات نهاية الخدمة'],
                ['name_en' => 'End of Service & Gratuities']
            );

            $expense = Expense::create([
                'expense_code'         => 'EXP-EOS-' . time(),
                'branch_id'            => $employee->branch_id, 
                'expense_category_id'  => $expenseCategory->id,
                'financial_account_id' => $request->financial_account_id,
                'user_id'              => auth()->id(),
                'amount'               => $request->net_payout,
                'total_amount'         => $request->net_payout,
                'expense_date'         => $request->termination_date,
                'transaction_details'  => "تصفية نهاية الخدمة: {$employee->full_name_ar} ({$employee->employee_code})",
            ]);

            // 2. خصم المبلغ من الحساب المالي
            $account->decrement('balance', $request->net_payout);

            // 🔥 3. حفظ سجل نهاية الخدمة أولاً (عشان نحصل على ID)
            $eos = HrEndOfService::create([
                'employee_id'       => $employee->id,
                'termination_date'  => $request->termination_date,
                'reason'            => $request->reason,
                'gratuity_amount'   => $request->gratuity_amount,
                'leave_encashment'  => $request->leave_encashment,
                'loan_deductions'   => $request->loan_deductions,
                'net_payout'        => $request->net_payout,
                'signature_path'    => $signaturePath,
                'transaction_id'    => $expense->id,
                'notes'             => $request->notes ?? null,
            ]);

            // 🔥 4. تسجيل الحركة المالية (بعد ما حصلنا على EOS ID)
            Payment::create([
                'payable_type'         => HrEndOfService::class,
                'payable_id'           => $eos->id,
                'financial_account_id' => $account->id,
                'user_id'              => auth()->id(),
                'payment_type'         => 'eos_payout',
                'amount'               => -$request->net_payout,
                'payment_date'         => $request->termination_date,
                'transaction_details'  => "صرف نهاية خدمة: {$employee->full_name_ar}",
                'receipt_number'       => 'EOS-' . $eos->id,
                'notes'                => $request->notes ?? null,
            ]);

            // 5. تحويل حالة الموظف
            $employee->update(['status' => 'terminated']);

            // 6. تقفيل السلف النشطة
            HrLoan::where('employee_id', $employee->id)
                ->where('status', 'active')
                ->update([
                    'status' => 'completed',
                    'paid_amount' => DB::raw('loan_amount'),
                    'completed_at' => $request->termination_date,
                ]);

            // 7. تقفيل العقد الساري
            $activeContract = $employee->contracts()->where('status', 'valid')->first();
            if ($activeContract) {
                $activeContract->update(['status' => 'terminated']);
            }

            DB::commit();
            return redirect()->back()->with('success', 'تم تصفية حساب الموظف وإنهاء خدمته بنجاح!');
            
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withErrors(['error' => 'خطأ في عملية نهاية الخدمة: ' . $e->getMessage()])
                ->withInput();
        }
    }

    // ==========================================
    // 🔥 طباعة المخالصة 🔥
    // ==========================================
    public function printClearance($id)
    {
        abort_if(!auth()->user()->can('manage_end_of_service'), 403);

        $eos = HrEndOfService::with([
            'employee.department',
            'employee.contracts',
            'employee.loans'
        ])->findOrFail($id);

        return view('backend.hr.eos.clearance', compact('eos'));
    }

    // ==========================================
    // 🔥 إلغاء المخالصة (عكس العملية) 🔥
    // ==========================================
    public function reverse($id)
    {
        abort_if(!auth()->user()->can('manage_end_of_service'), 403);

        $eos = HrEndOfService::with('employee')->findOrFail($id);

        DB::beginTransaction();
        try {
            // 1. إعادة المبلغ للحساب المالي
            if ($eos->transaction_id) {
                $expense = Expense::find($eos->transaction_id);
                if ($expense && $expense->financial_account_id) {
                    $account = FinancialAccount::lockForUpdate()->find($expense->financial_account_id);
                    if ($account) {
                        $account->increment('balance', $eos->net_payout);
                    }
                    $expense->delete();
                }
            }

            // 2. حذف الحركة المالية
            Payment::where('payable_type', HrEndOfService::class)
                ->where('payable_id', $eos->id)
                ->delete();

            // 3. إعادة حالة الموظف
            $eos->employee->update(['status' => 'active']);

            // 4. إعادة تفعيل السلف
            HrLoan::where('employee_id', $eos->employee_id)
                ->where('completed_at', $eos->termination_date)
                ->update([
                    'status' => 'active',
                    'completed_at' => null,
                    'paid_amount' => DB::raw('paid_amount - loan_amount + paid_amount'),
                ]);

            // 5. حذف سجل المخالصة
            $eos->delete();

            DB::commit();
            return redirect()->back()->with('success', 'تم إلغاء المخالصة وإعادة الموظف للعمل بنجاح!');
            
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'خطأ في إلغاء المخالصة: ' . $e->getMessage());
        }
    }
}