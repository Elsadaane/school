<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HrPenalty;
use App\Models\HrEmployee;
use App\Models\HrEmployeeFinancial;
use App\Models\HrFinancialType;
use App\Models\HrPayroll;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HrPenaltyController extends Controller
{
    // ==========================================
    // 🔥 عرض سجل المخالفات والجزاءات 🔥
    // ==========================================
    public function index(Request $request)
    {
        abort_if(!auth()->user()->can('edit_employees'), 403, 'غير مصرح لك بإدارة جزاءات الموظفين.');

        // الفلاتر
        $month = $request->input('month');
        $year = $request->input('year', date('Y'));
        $employeeId = $request->input('employee_id');
        $actionType = $request->input('action_type');

        // بناء الاستعلام
        $query = HrPenalty::with(['employee.department', 'financialRecord']);

        if ($month) {
            $query->whereMonth('incident_date', $month);
        }
        if ($year) {
            $query->whereYear('incident_date', $year);
        }
        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }
        if ($actionType) {
            $query->where('penalty_action', $actionType);
        }

        $penalties = $query->orderBy('incident_date', 'desc')->get();
        $employees = HrEmployee::where('status', 'active')->with('department')->orderBy('full_name_ar')->get();

        // إحصائيات شاملة
        $totalPenalties = $penalties->count();
        $deductionCount = $penalties->where('penalty_action', 'salary_deduction')->count();
        $warningCount = $penalties->whereIn('penalty_action', ['verbal_warning', 'written_warning'])->count();
        $appliedCount = $penalties->where('applied_to_payroll', true)->count();
        $totalDeductionAmount = $penalties->sum('deduction_amount');

        return view('backend.hr.penalties.index', compact(
            'penalties', 'employees', 'month', 'year', 'employeeId', 'actionType',
            'totalPenalties', 'deductionCount', 'warningCount', 'appliedCount', 'totalDeductionAmount'
        ));
    }

    // ==========================================
    // 🔥 تسجيل مخالفة جديدة 🔥
    // ==========================================
    public function store(Request $request)
    {
        abort_if(!auth()->user()->can('edit_employees'), 403);

        $validated = $request->validate([
            'employee_id'    => 'required|exists:hr_employees,id',
            'violation_type' => 'required|string|max:255',
            'penalty_action' => 'required|in:verbal_warning,written_warning,salary_deduction,suspension',
            'incident_date'  => 'required|date',
            'deduction_days' => 'required_if:penalty_action,salary_deduction|nullable|numeric|min:0|max:999',
            'details'        => 'nullable|string|max:1000',
            'notes'          => 'nullable|string|max:500',
        ], [
            'deduction_days.required_if' => 'يجب تحديد عدد أيام الخصم عند اختيار خصم من الراتب',
        ]);

        // التحقق من عدم تكرار المخالفة في نفس اليوم
        $exists = HrPenalty::where('employee_id', $validated['employee_id'])
            ->where('incident_date', $validated['incident_date'])
            ->where('violation_type', $validated['violation_type'])
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withErrors(['error' => 'تم تسجيل هذه المخالفة مسبقاً لنفس الموظف في نفس اليوم!'])
                ->withInput();
        }

        DB::beginTransaction();
        try {
            // 1. تسجيل المخالفة الإدارية
            $penalty = HrPenalty::create($validated);

            // 2. تحويل أيام الخصم إلى قيمة مالية
            if ($validated['penalty_action'] == 'salary_deduction' && ($validated['deduction_days'] ?? 0) > 0) {
                
                $employee = HrEmployee::findOrFail($validated['employee_id']);
                $dailyRate = $employee->basic_salary / 30;
                $deductionAmount = round($dailyRate * $validated['deduction_days'], 3);

                // جلب أو إنشاء نوع مالي "جزاءات إدارية"
                $financialType = HrFinancialType::firstOrCreate(
                    ['name_ar' => 'جزاءات ومخالفات إدارية', 'type' => 'deduction'],
                    ['value_type' => 'fixed']
                );

                // تسجيل الخصم المالي
                $financialRecord = HrEmployeeFinancial::create([
                    'employee_id'       => $employee->id,
                    'financial_type_id' => $financialType->id,
                    'amount'            => $deductionAmount,
                    'action_date'       => $validated['incident_date'],
                    'notes'             => 'خصم ' . $validated['deduction_days'] . ' يوم بسبب: ' . $validated['violation_type'] . ' (مخالفة #' . $penalty->id . ')',
                    'status'            => 'pending',
                ]);

                // ربط المخالفة بالحركة المالية
                $penalty->update([
                    'applied_to_payroll' => true,
                    'financial_record_id' => $financialRecord->id,
                ]);
            }

            DB::commit();
            return redirect()->back()->with('success', 'تم تسجيل المخالفة وتطبيق الإجراء اللازم بنجاح!');
            
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'حدث خطأ أثناء التطبيق: ' . $e->getMessage())->withInput();
        }
    }

    // ==========================================
    // 🔥 تعديل مخالفة موجودة 🔥
    // ==========================================
    public function update(Request $request, $id)
    {
        abort_if(!auth()->user()->can('edit_employees'), 403);

        $validated = $request->validate([
            'employee_id'    => 'required|exists:hr_employees,id',
            'violation_type' => 'required|string|max:255',
            'penalty_action' => 'required|in:verbal_warning,written_warning,salary_deduction,suspension',
            'incident_date'  => 'required|date',
            'deduction_days' => 'required_if:penalty_action,salary_deduction|nullable|numeric|min:0|max:999',
            'details'        => 'nullable|string|max:1000',
            'notes'          => 'nullable|string|max:500',
        ]);

        $penalty = HrPenalty::findOrFail($id);

        // حماية محاسبية: التأكد من أن الراتب لم يُصرف
        $month = Carbon::parse($penalty->incident_date)->format('m');
        $year = Carbon::parse($penalty->incident_date)->format('Y');
        
        $payrollExists = HrPayroll::where('employee_id', $penalty->employee_id)
            ->where('month', $month)
            ->where('year', $year)
            ->where('status', 'paid')
            ->exists();

        if ($payrollExists && $penalty->applied_to_payroll) {
            return redirect()->back()->with('error', 'مرفوض أمنياً: لا يمكن تعديل هذه المخالفة لأنه تم صرف راتب هذا الشهر وتطبيق الخصم فعلياً.');
        }

        // التحقق من أن الحركة المالية لم تُعالج
        if ($penalty->financialRecord && $penalty->financialRecord->status === 'processed') {
            return redirect()->back()->with('error', 'لا يمكن تعديل هذه المخالفة لأن الحركة المالية المرتبطة بها تم ربطها بمسير رواتب مُعتمد!');
        }

        DB::beginTransaction();
        try {
            // 1. حذف الخصم المالي القديم
            if ($penalty->financial_record_id) {
                HrEmployeeFinancial::find($penalty->financial_record_id)?->delete();
                $penalty->update(['applied_to_payroll' => false, 'financial_record_id' => null]);
            }

            // 2. تحديث بيانات المخالفة
            $penalty->update($validated);

            // 3. إعادة إنشاء الخصم المالي
            if ($validated['penalty_action'] == 'salary_deduction' && ($validated['deduction_days'] ?? 0) > 0) {
                $employee = HrEmployee::findOrFail($validated['employee_id']);
                $dailyRate = $employee->basic_salary / 30;
                $deductionAmount = round($dailyRate * $validated['deduction_days'], 3);

                $financialType = HrFinancialType::firstOrCreate(
                    ['name_ar' => 'جزاءات ومخالفات إدارية', 'type' => 'deduction'],
                    ['value_type' => 'fixed']
                );

                $financialRecord = HrEmployeeFinancial::create([
                    'employee_id'       => $employee->id,
                    'financial_type_id' => $financialType->id,
                    'amount'            => $deductionAmount,
                    'action_date'       => $validated['incident_date'],
                    'notes'             => 'خصم ' . $validated['deduction_days'] . ' يوم بسبب: ' . $validated['violation_type'] . ' (مخالفة #' . $penalty->id . ') - مُعدّل',
                    'status'            => 'pending',
                ]);

                $penalty->update([
                    'applied_to_payroll' => true,
                    'financial_record_id' => $financialRecord->id,
                ]);
            }

            DB::commit();
            return redirect()->back()->with('success', 'تم تعديل المخالفة وتحديث الحسابات المالية بنجاح!');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'حدث خطأ أثناء التعديل: ' . $e->getMessage())->withInput();
        }
    }

    // ==========================================
    // 🔥 حذف المخالفة 🔥
    // ==========================================
    public function destroy($id)
    {
        abort_if(!auth()->user()->can('edit_employees'), 403);

        $penalty = HrPenalty::findOrFail($id);

        // حماية محاسبية
        $month = Carbon::parse($penalty->incident_date)->format('m');
        $year = Carbon::parse($penalty->incident_date)->format('Y');
        
        $payrollExists = HrPayroll::where('employee_id', $penalty->employee_id)
            ->where('month', $month)
            ->where('year', $year)
            ->where('status', 'paid')
            ->exists();

        if ($payrollExists && $penalty->applied_to_payroll) {
            return redirect()->back()->withErrors(['error' => 'مرفوض أمنياً: لا يمكن حذف هذه المخالفة لأنه تم إدراجها وخصمها في راتب مصروف بالفعل.']);
        }

        // التحقق من أن الحركة المالية لم تُعالج
        if ($penalty->financialRecord && $penalty->financialRecord->status === 'processed') {
            return redirect()->back()->withErrors(['error' => 'لا يمكن حذف هذه المخالفة لأن الحركة المالية المرتبطة بها تم ربطها بمسير رواتب مُعتمد!']);
        }

        DB::beginTransaction();
        try {
            // حذف الحركة المالية المرتبطة
            if ($penalty->financial_record_id) {
                HrEmployeeFinancial::find($penalty->financial_record_id)?->delete();
            }

            $penalty->delete();

            DB::commit();
            return redirect()->back()->with('success', 'تم حذف المخالفة وإلغاء أي خصومات مالية مرتبطة بها!');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors(['error' => 'حدث خطأ أثناء الحذف: ' . $e->getMessage()]);
        }
    }
}