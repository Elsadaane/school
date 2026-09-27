<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HrContract;
use App\Models\HrEmployee;
use App\Models\HrDepartment;
use App\Models\Teacher;
use App\Models\HrContractAllowance;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class HrContractController extends Controller
{
    // ==========================================
    // 🔥 1. عرض سجل العقود والفلاتر الذكية 🔥
    // ==========================================
    public function index(Request $request)
    {
        abort_if(!auth()->user()->can('manage_hr_contracts'), 403, 'غير مصرح لك بإدارة عقود الموظفين.');

        $filter = $request->input('filter');
        $search = $request->input('search');
        $departmentId = $request->input('department_id');

        // 🔥 استخدام Scopes الجديدة من الموديل 🔥
        $activeCount = HrContract::whereHas('employee')->active()->count();
        $expiredCount = HrContract::whereHas('employee')->expired()->count();
        $expiringSoon = HrContract::whereHas('employee')->expiringSoon(30)->count();
        $totalContracts = HrContract::whereHas('employee')->count();

        // بناء الاستعلام الرئيسي
        $query = HrContract::with([
            'employee.department',
            'employee.jobGrade',
            'allowances.financialType'
        ])->orderBy('end_date', 'asc');

        // 🔥 تطبيق الفلاتر باستخدام Scopes 🔥
        if ($filter == 'active') {
            $query->active();
        } elseif ($filter == 'expiring_soon') {
            $query->expiringSoon(30);
        } elseif ($filter == 'expired') {
            $query->expired();
        }

        // فلتر البحث
        if ($search) {
            $query->whereHas('employee', function($q) use ($search) {
                $q->where('full_name_ar', 'like', "%{$search}%")
                  ->orWhere('employee_code', 'like', "%{$search}%");
            });
        }

        // فلتر القسم
        if ($departmentId) {
            $query->whereHas('employee', function($q) use ($departmentId) {
                $q->where('hr_department_id', $departmentId);
            });
        }

        $contracts = $query->paginate(20)->appends($request->all());
        
        // جلب الموظفين والأقسام للفلاتر
        $employees = HrEmployee::where('status', 'active')
            ->with('department')
            ->orderBy('full_name_ar')
            ->get();
        
        $departments = HrDepartment::orderBy('name_ar')->get();

        return view('backend.hr.contracts.index', compact(
            'contracts', 'employees', 'departments',
            'activeCount', 'expiredCount', 'expiringSoon', 'totalContracts',
            'filter', 'search', 'departmentId'
        ));
    }

    // ==========================================
    // 🔥 2. تسجيل عقد جديد 🔥
    // ==========================================
    public function store(Request $request)
    {
        abort_if(!auth()->user()->can('manage_hr_contracts'), 403);

        $validated = $request->validate([
            'employee_id'     => 'required|exists:hr_employees,id',
            'contract_number' => 'required|string|unique:hr_contracts,contract_number|max:50',
            'start_date'      => 'required|date',
            'end_date'        => 'required|date|after:start_date',
            'total_salary'    => 'required|numeric|min:0',
            'attachment'      => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'notes'           => 'nullable|string|max:1000',
        ], [
            'contract_number.unique' => 'رقم العقد موجود مسبقاً، يرجى استخدام رقم مختلف',
            'end_date.after' => 'تاريخ نهاية العقد يجب أن يكون بعد تاريخ البداية',
        ]);

        $data = $validated;
        $data['status'] = 'valid';

        // رفع الملف المرفق
        if ($request->hasFile('attachment')) {
            $data['attachment'] = $request->file('attachment')->store('hr_contracts', 'public');
        }

        DB::beginTransaction();
        try {
            // 1. إنهاء العقود القديمة لنفس الموظف
            HrContract::where('employee_id', $request->employee_id)
                ->where('status', 'valid')
                ->update(['status' => 'expired']);

            // 2. تحديث بيانات الموظف
            $employee = HrEmployee::findOrFail($request->employee_id);
            $employee->update([
                'basic_salary' => $request->total_salary, 
                'status'       => 'active',
            ]);

            // 3. إنشاء العقد الجديد
            $contract = HrContract::create($data);

            // 4. 🔥 محاولة تفعيل حساب المعلم 🔥
            $this->activateTeacherAccount($employee->id);

            DB::commit();
            return redirect()->back()->with('success', 'تم توثيق العقد بنجاح! رقم العقد: ' . $contract->contract_number);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => 'حدث خطأ: ' . $e->getMessage()])
                ->withInput();
        }
    }

    // ==========================================
    // 🔥 3. تعديل العقد 🔥
    // ==========================================
    public function update(Request $request, $id)
    {
        abort_if(!auth()->user()->can('manage_hr_contracts'), 403);

        $contract = HrContract::findOrFail($id);

        $validated = $request->validate([
            'contract_number' => 'required|string|unique:hr_contracts,contract_number,' . $id . '|max:50',
            'start_date'      => 'required|date',
            'end_date'        => 'required|date|after:start_date',
            'total_salary'    => 'required|numeric|min:0',
            'attachment'      => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'notes'           => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            $data = $validated;

            // رفع الملف الجديد لو موجود
            if ($request->hasFile('attachment')) {
                // حذف الملف القديم
                if ($contract->attachment) {
                    Storage::disk('public')->delete($contract->attachment);
                }
                $data['attachment'] = $request->file('attachment')->store('hr_contracts', 'public');
            }

            $contract->update($data);

            // تحديث راتب الموظف
            $employee = HrEmployee::find($contract->employee_id);
            if ($employee) {
                $employee->update(['basic_salary' => $request->total_salary]);
            }

            DB::commit();
            return redirect()->back()->with('success', 'تم تعديل العقد بنجاح!');
            
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withErrors(['error' => 'حدث خطأ: ' . $e->getMessage()])
                ->withInput();
        }
    }

    // ==========================================
    // 🔥 4. تغيير حالة العقد 🔥
    // ==========================================
    public function updateStatus($id, $status)
    {
        abort_if(!auth()->user()->can('manage_hr_contracts'), 403);

        if (!in_array($status, ['valid', 'expired', 'terminated'])) {
            return redirect()->back()->withErrors(['error' => 'حالة العقد غير صحيحة.']);
        }

        $contract = HrContract::findOrFail($id);
        
        DB::beginTransaction();
        try {
            $contract->update(['status' => $status]);
            
            $employee = HrEmployee::find($contract->employee_id);
            
            if ($employee) {
                if (in_array($status, ['expired', 'terminated'])) {
                    // إيقاف الموظف
                    $employee->update(['status' => 'terminated']);
                    
                    // 🔥 محاولة إيقاف حساب المعلم 🔥
                    $this->suspendTeacherAccount($employee->id);
                    
                } elseif ($status == 'valid') {
                    // إعادة تفعيل الموظف
                    $employee->update(['status' => 'active']);
                    
                    // 🔥 محاولة تفعيل المعلم 🔥
                    $this->activateTeacherAccount($employee->id);
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'تم تحديث حالة العقد بنجاح!');
            
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors(['error' => 'حدث خطأ: ' . $e->getMessage()]);
        }
    }

    // ==========================================
    // 🔥 5. حذف العقد 🔥
    // ==========================================
    public function destroy($id)
    {
        abort_if(!auth()->user()->can('manage_hr_contracts'), 403);

        $contract = HrContract::findOrFail($id);

        // 🔥 حماية محسّنة: استخدام Accessors 🔥
        if ($contract->is_active) {
            return redirect()->back()->with('error', 'لا يمكن حذف عقد نشط. قم بإنهائه أولاً.');
        }

        DB::beginTransaction();
        try {
            // حذف البدلات المرتبطة بالعقد
            $contract->allowances()->delete();

            // حذف الملف المرفق
            if ($contract->attachment) {
                Storage::disk('public')->delete($contract->attachment);
            }

            $contract->delete();

            DB::commit();
            return redirect()->back()->with('success', 'تم حذف العقد بنجاح!');
            
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    // ==========================================
    // 🔥 6. طباعة العقد 🔥
    // ==========================================
    public function print($id)
    {
        abort_if(!auth()->user()->can('view_hr_contracts'), 403);

        $contract = HrContract::with([
            'employee.department',
            'employee.jobGrade',
            'allowances.financialType'
        ])->findOrFail($id);

        return view('backend.hr.contracts.print', compact('contract'));
    }

    // ==========================================
    // 🔥 7. عرض تفاصيل العقد (AJAX) 🔥
    // ==========================================
    public function show($id)
    {
        abort_if(!auth()->user()->can('view_hr_contracts'), 403);

        $contract = HrContract::with([
            'employee.department',
            'employee.jobGrade',
            'allowances.financialType'
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $contract->id,
                'contract_number' => $contract->contract_number,
                'employee_name' => $contract->employee->full_name_ar ?? '---',
                'department' => $contract->employee->department->name_ar ?? '---',
                'start_date' => $contract->start_date->format('Y-m-d'),
                'end_date' => $contract->end_date->format('Y-m-d'),
                'total_salary' => number_format($contract->total_salary, 3),
                'total_allowances' => number_format($contract->total_allowances_amount, 3),
                'total_compensation' => number_format($contract->total_compensation, 3),
                'status' => $contract->status_text,
                'status_color' => $contract->status_color,
                'status_icon' => $contract->status_icon,
                'days_remaining' => $contract->days_remaining,
                'duration_text' => $contract->duration_text,
                'allowances' => $contract->allowances->map(function($allowance) {
                    return [
                        'name' => $allowance->financialType->name_ar ?? '---',
                        'amount' => number_format($allowance->amount, 3),
                        'type' => $allowance->financialType->type ?? '---',
                    ];
                }),
            ]
        ]);
    }

    // ==========================================
    // 🔥 دالة مساعدة: تفعيل حساب المعلم 🔥
    // ==========================================
    private function activateTeacherAccount($employeeId)
    {
        try {
            // التحقق من وجود عمود hr_employee_id في جدول teachers
            if (!Schema::hasColumn('teachers', 'hr_employee_id')) {
                return; // العمود غير موجود، نتجاهل
            }

            Teacher::where('hr_employee_id', $employeeId)
                ->update(['status' => 'active']);
                
        } catch (\Exception $e) {
            // تجاهل الأخطاء - المعلم مش ضروري يكون موجود
            Log::warning('Could not activate teacher account: ' . $e->getMessage());
        }
    }

    // ==========================================
    // 🔥 دالة مساعدة: إيقاف حساب المعلم 🔥
    // ==========================================
    private function suspendTeacherAccount($employeeId)
    {
        try {
            // التحقق من وجود عمود hr_employee_id في جدول teachers
            if (!Schema::hasColumn('teachers', 'hr_employee_id')) {
                return; // العمود غير موجود، نتجاهل
            }

            Teacher::where('hr_employee_id', $employeeId)
                ->update(['status' => 'suspended']);
                
        } catch (\Exception $e) {
            // تجاهل الأخطاء
            Log::warning('Could not suspend teacher account: ' . $e->getMessage());
        }
    }
}