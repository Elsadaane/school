<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HrEmployee;
use App\Models\HrEmployeeFinancial; 
use App\Models\HrFinancialType; 
use App\Models\HrPayroll;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HrFinancialController extends Controller
{
    // ==========================================
    // 🔥 1. عرض سجل العلاوات والخصومات 🔥
    // ==========================================
    public function index(Request $request)
    {
        abort_if(!auth()->user()->can('view_payrolls'), 403, 'غير مصرح لك بمشاهدة السجلات المالية للموظفين.');

        // الفلاتر
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));
        $employeeId = $request->input('employee_id');
        $type = $request->input('type'); // allowance / deduction
        $status = $request->input('status'); // pending / processed

        // بناء الاستعلام مع الفلاتر
        $query = HrEmployeeFinancial::with(['employee.department', 'financialType'])
            ->whereMonth('action_date', $month)
            ->whereYear('action_date', $year);

        // فلتر الموظف
        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        // فلتر النوع (علاوة/خصم)
        if ($type) {
            $query->whereHas('financialType', function($q) use ($type) {
                $q->where('type', $type);
            });
        }

        // فلتر الحالة
        if ($status) {
            $query->where('status', $status);
        }

        $transactions = $query->orderBy('action_date', 'desc')->get();

        // البيانات للنماذج
        $employees = HrEmployee::where('status', 'active')
            ->with('department')
            ->orderBy('full_name_ar')
            ->get();
        
        $financialTypes = HrFinancialType::orderBy('type', 'asc')->get();

        // إحصائيات شاملة
        $totalAllowances = $transactions->filter(function($t) {
            return $t->financialType && $t->financialType->type == 'allowance';
        })->sum('amount');
        
        $totalDeductions = $transactions->filter(function($t) {
            return $t->financialType && $t->financialType->type == 'deduction';
        })->sum('amount');

        $pendingCount = $transactions->where('status', 'pending')->count();
        $processedCount = $transactions->where('status', 'processed')->count();

        return view('backend.hr.financials.index', compact(
            'transactions', 'employees', 'financialTypes', 
            'month', 'year', 'employeeId', 'type', 'status',
            'totalAllowances', 'totalDeductions',
            'pendingCount', 'processedCount'
        ));
    }

    // ==========================================
    // 🔥 2. تسجيل حركة مالية جديدة 🔥
    // ==========================================
    public function store(Request $request)
    {
        abort_if(!auth()->user()->can('generate_payrolls'), 403);

        $validated = $request->validate([
            'employee_id'       => 'required|exists:hr_employees,id',
            'financial_type_id' => 'required|exists:hr_financial_types,id',
            'amount'            => 'required|numeric|min:0.001', 
            'action_date'       => 'required|date',
            'notes'             => 'required|string|max:255',
        ]);

        // التحقق من عدم تكرار الحركة في نفس الشهر
        $exists = HrEmployeeFinancial::where('employee_id', $validated['employee_id'])
            ->where('financial_type_id', $validated['financial_type_id'])
            ->whereMonth('action_date', Carbon::parse($validated['action_date'])->month)
            ->whereYear('action_date', Carbon::parse($validated['action_date'])->year)
            ->where('status', 'pending')
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withErrors(['error' => 'تم تسجيل هذه الحركة المالية مسبقاً لنفس الموظف في نفس الشهر!'])
                ->withInput();
        }

        // إضافة الحالة الافتراضية
        $validated['status'] = 'pending';

        HrEmployeeFinancial::create($validated);

        return redirect()->back()->with('success', 'تم تسجيل الحركة المالية بنجاح وربطها بملف الموظف!');
    }

    // ==========================================
    // 🔥 3. تعديل حركة مالية مسجلة 🔥
    // ==========================================
    public function update(Request $request, $id)
    {
        abort_if(!auth()->user()->can('generate_payrolls'), 403);

        $transaction = HrEmployeeFinancial::findOrFail($id);

        // 🔥 حماية أمنية: منع تعديل الحركات المعالجة 🔥
        if ($transaction->status == 'processed') {
            return redirect()->back()->withErrors([
                'error' => 'لا يمكن تعديل هذه الحركة لأنها رُبطت بالفعل بمسير رواتب مُعتمد! يرجى إلغاء الربط أولاً.'
            ]);
        }
        
        $validated = $request->validate([
            'employee_id'       => 'required|exists:hr_employees,id',
            'financial_type_id' => 'required|exists:hr_financial_types,id',
            'amount'            => 'required|numeric|min:0.001',
            'action_date'       => 'required|date',
            'notes'             => 'required|string|max:255',
        ]);

        $transaction->update($validated);

        return redirect()->back()->with('success', 'تم تحديث بيانات الحركة المالية بنجاح!');
    }

    // ==========================================
    // 🔥 4. حذف حركة مالية 🔥
    // ==========================================
    public function destroy($id)
    {
        abort_if(!auth()->user()->can('generate_payrolls'), 403);

        $transaction = HrEmployeeFinancial::findOrFail($id);
        
        // 🔥 حماية: منع حذف الحركات المعالجة 🔥
        if ($transaction->status == 'processed') {
            return redirect()->back()->withErrors([
                'error' => 'لا يمكن حذف هذه الحركة لأنها رُبطت بمسير رواتب مُعتمد! يرجى عمل قيد عكسي بدلاً من الحذف.'
            ]);
        }

        $transaction->delete();
        
        return redirect()->back()->with('success', 'تم شطب الحركة المالية من سجلات الموظف بنجاح!');
    }

    // ==========================================
    // 🔥 5. ربط الحركات بمسير الرواتب 🔥
    // ==========================================
    public function linkToPayroll(Request $request)
    {
        abort_if(!auth()->user()->can('generate_payrolls'), 403);

        $validated = $request->validate([
            'payroll_id' => 'required|exists:hr_payrolls,id',
            'transaction_ids' => 'required|array|min:1',
            'transaction_ids.*' => 'exists:hr_employee_financials,id',
        ]);

        $payroll = HrPayroll::findOrFail($validated['payroll_id']);

        DB::beginTransaction();
        try {
            // ربط الحركات المحددة بمسير الرواتب
            HrEmployeeFinancial::whereIn('id', $validated['transaction_ids'])
                ->where('status', 'pending')
                ->update([
                    'status' => 'processed',
                    'payroll_id' => $payroll->id,
                ]);

            DB::commit();
            return redirect()->back()->with('success', 'تم ربط الحركات المالية بمسير الرواتب بنجاح!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'حدث خطأ أثناء الربط: ' . $e->getMessage()]);
        }
    }

    // ==========================================
    // 🔥 6. إلغاء الربط بمسير الرواتب 🔥
    // ==========================================
    public function unlinkFromPayroll($id)
    {
        abort_if(!auth()->user()->can('generate_payrolls'), 403);

        $transaction = HrEmployeeFinancial::findOrFail($id);

        if ($transaction->status != 'processed') {
            return redirect()->back()->withErrors(['error' => 'هذه الحركة غير مرتبطة بأي مسير رواتب!']);
        }

        $transaction->update([
            'status' => 'pending',
            'payroll_id' => null,
        ]);

        return redirect()->back()->with('success', 'تم إلغاء ربط الحركة المالية بنجاح!');
    }
}