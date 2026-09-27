<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HrLoan;
use App\Models\HrEmployee;
use App\Models\HrEmployeeFinancial;
use App\Models\HrFinancialType;
use App\Models\HrPayroll;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HrLoanController extends Controller
{
    // ==========================================
    // 🔥 1. عرض قائمة السلف 🔥
    // ==========================================
    public function index(Request $request)
    {
        abort_if(!auth()->user()->can('manage_hr_loans_penalties'), 403, 'غير مصرح لك بإدارة سلف الموظفين.');

        // الفلاتر
        $employeeId = $request->input('employee_id');
        $status = $request->input('status');
        $startMonth = $request->input('start_month');
        $startYear = $request->input('start_year', date('Y'));

        // بناء الاستعلام
        $query = HrLoan::with(['employee.department', 'financialRecord']);

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }
        if ($status) {
            $query->where('status', $status);
        }
        if ($startMonth) {
            $query->whereMonth('start_deduction_month', $startMonth);
        }
        if ($startYear) {
            $query->whereYear('start_deduction_month', $startYear);
        }

        $loans = $query->orderBy('id', 'desc')->get();
        $employees = HrEmployee::where('status', 'active')->with('department')->orderBy('full_name_ar')->get();

        // إحصائيات شاملة
        $totalLoans = $loans->count();
        $activeLoans = $loans->where('status', 'active')->count();
        $completedLoans = $loans->where('status', 'completed')->count();
        $totalLoanAmount = $loans->sum('loan_amount');
        $totalPaidAmount = $loans->sum('paid_amount');
        $totalRemainingAmount = $loans->sum('remaining_amount');

        return view('backend.hr.loans.index', compact(
            'loans', 'employees', 'employeeId', 'status', 'startMonth', 'startYear',
            'totalLoans', 'activeLoans', 'completedLoans',
            'totalLoanAmount', 'totalPaidAmount', 'totalRemainingAmount'
        ));
    }

    // ==========================================
    // 🔥 2. إضافة سلفة جديدة 🔥
    // ==========================================
    public function store(Request $request)
    {
        abort_if(!auth()->user()->can('manage_hr_loans_penalties'), 403);

        $validated = $request->validate([
            'employee_id'           => 'required|exists:hr_employees,id',
            'loan_amount'           => 'required|numeric|min:1',
            'months_to_pay'         => 'required|integer|min:1|max:60',
            'start_deduction_month' => 'required|date|after_or_equal:today',
            'notes'                 => 'nullable|string|max:500',
        ], [
            'start_deduction_month.after_or_equal' => 'يجب أن يكون تاريخ بدء الخصم اليوم أو بعد اليوم',
            'months_to_pay.max' => 'الحد الأقصى لعدد الأشهر هو 60 شهراً',
        ]);

        // حماية مالية: منع السلف المتعددة
        $hasActiveLoan = HrLoan::where('employee_id', $validated['employee_id'])
                               ->whereIn('status', ['active', 'pending'])
                               ->exists();

        if ($hasActiveLoan) {
            return redirect()->back()
                ->with('error', 'رفض أمني: هذا الموظف لديه سلفة نشطة حالياً. يجب سدادها أولاً قبل طلب سلفة جديدة.')
                ->withInput();
        }

        // حساب القسط الشهري
        $monthly_installment = round($validated['loan_amount'] / $validated['months_to_pay'], 3);

        DB::beginTransaction();
        try {
            $loan = HrLoan::create([
                'employee_id'           => $validated['employee_id'],
                'loan_amount'           => $validated['loan_amount'],
                'months_to_pay'         => $validated['months_to_pay'],
                'monthly_installment'   => $monthly_installment,
                'start_deduction_month' => Carbon::parse($validated['start_deduction_month'])->startOfMonth()->format('Y-m-d'),
                'paid_amount'           => 0,
                'status'                => 'active',
                'notes'                 => $validated['notes'] ?? null,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'تم تسجيل السلفة وتحديد القسط الشهري (' . number_format($monthly_installment, 3) . ' ر.ع) بنجاح!');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'حدث خطأ: ' . $e->getMessage())->withInput();
        }
    }

    // ==========================================
    // 🔥 3. تعديل السلفة (إعادة الجدولة) 🔥
    // ==========================================
    public function update(Request $request, $id)
    {
        abort_if(!auth()->user()->can('manage_hr_loans_penalties'), 403);

        $validated = $request->validate([
            'months_to_pay'         => 'required|integer|min:1|max:60',
            'start_deduction_month' => 'required|date',
            'notes'                 => 'nullable|string|max:500',
        ]);

        $loan = HrLoan::findOrFail($id);

        // حماية محاسبية
        $remaining_amount = $loan->loan_amount - $loan->paid_amount;
        
        if ($remaining_amount <= 0 || $loan->status != 'active') {
            return redirect()->back()->with('error', 'هذه السلفة مسددة أو مغلقة ولا يمكن إعادة جدولتها.');
        }

        // التحقق من أن السلفة لم تُطبق في رواتب سابقة
        $hasProcessedPayments = HrPayroll::where('employee_id', $loan->employee_id)
            ->where('status', 'paid')
            ->whereMonth('month', '>=', Carbon::parse($loan->start_deduction_month)->month)
            ->whereYear('year', '>=', Carbon::parse($loan->start_deduction_month)->year)
            ->exists();

        if ($hasProcessedPayments && $loan->paid_amount > 0) {
            return redirect()->back()->with('error', 'لا يمكن تعديل السلفة لأنه تم خصم دفعات منها في مسيرات رواتب سابقة. يمكنك إعادة الجدولة للأقساط المستقبلية فقط.');
        }

        DB::beginTransaction();
        try {
            // حساب القسط الجديد
            $monthly_installment = round($remaining_amount / $validated['months_to_pay'], 3);

            $loan->update([
                'months_to_pay'         => $validated['months_to_pay'],
                'monthly_installment'   => $monthly_installment,
                'start_deduction_month' => Carbon::parse($validated['start_deduction_month'])->startOfMonth()->format('Y-m-d'),
                'notes'                 => $validated['notes'] ?? $loan->notes,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'تم تعديل خطة السداد وإعادة حساب القسط (' . number_format($monthly_installment, 3) . ' ر.ع) بناءً على المبلغ المتبقي!');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'حدث خطأ: ' . $e->getMessage())->withInput();
        }
    }

    // ==========================================
    // 🔥 4. حذف السلفة 🔥
    // ==========================================
    public function destroy($id)
    {
        abort_if(!auth()->user()->can('manage_hr_loans_penalties'), 403);

        $loan = HrLoan::findOrFail($id);

        // حماية قاتلة
        if ($loan->paid_amount > 0) {
            return redirect()->back()->with('error', 'رفض أمني: لا يمكن حذف السلفة لأنه تم سداد جزء منها بالفعل. يمكنك إعادة جدولتها بدلاً من الحذف.');
        }

        $loan->delete();

        return redirect()->back()->with('success', 'تم إلغاء السلفة وحذفها من سجل الموظف بنجاح.');
    }

    // ==========================================
    // 🔥 5. تسجيل دفعة سداد يدوية 🔥
    // ==========================================
    public function recordPayment(Request $request, $id)
    {
        abort_if(!auth()->user()->can('manage_hr_loans_penalties'), 403);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.001',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string|max:500',
        ]);

        $loan = HrLoan::findOrFail($id);

        $remaining = $loan->loan_amount - $loan->paid_amount;
        if ($validated['amount'] > $remaining) {
            return redirect()->back()->with('error', 'المبلغ المدفوع أكبر من المتبقي (' . number_format($remaining, 3) . ' ر.ع)');
        }

        DB::beginTransaction();
        try {
            // تحديث المبلغ المدفوع
            $newPaidAmount = $loan->paid_amount + $validated['amount'];
            $loan->update(['paid_amount' => $newPaidAmount]);

            // التحقق من اكتمال السداد
            if ($newPaidAmount >= $loan->loan_amount) {
                $loan->update([
                    'status' => 'completed',
                    'completed_at' => $validated['payment_date'],
                ]);
            }

            // تسجيل الحركة المالية
            $financialType = HrFinancialType::firstOrCreate(
                ['name_ar' => 'سداد سلفة موظف', 'type' => 'deduction'],
                ['value_type' => 'fixed']
            );

            HrEmployeeFinancial::create([
                'employee_id'       => $loan->employee_id,
                'financial_type_id' => $financialType->id,
                'amount'            => $validated['amount'],
                'action_date'       => $validated['payment_date'],
                'notes'             => 'سداد سلفة #' . $loan->id . ($validated['notes'] ? ' - ' . $validated['notes'] : ''),
                'status'            => 'pending',
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'تم تسجيل الدفعة بنجاح! المتبقي: ' . number_format($loan->loan_amount - $newPaidAmount, 3) . ' ر.ع');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }
}