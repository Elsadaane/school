<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HrEmployee;
use App\Models\HrDepartment;
use App\Models\HrShift;
use App\Models\HrJobGrade;
use App\Models\HrEmployeeDocument;
use App\Models\User;
use App\Models\Teacher; // 🔥 استدعاء موديل المعلم 🔥
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class HrEmployeeController extends Controller
{
    // ==========================================
    // 🔥 عرض قائمة الموظفين 🔥
    // ==========================================
    public function index()
    {
        abort_if(!auth()->user()->can('view_employees'), 403, 'غير مصرح لك بعرض بيانات الموظفين.');

        // جلب الموظفين مع الفروع والمدير المباشر لتسهيل الاستعلام
        $employees = HrEmployee::with(['department', 'shift', 'jobGrade', 'branch', 'manager'])->orderBy('id', 'DESC')->get();
        return view('backend.hr.employees.index', compact('employees'));
    }

    // ==========================================
    // 🔥 فتح شاشة إضافة موظف جديد 🔥
    // ==========================================
    // ==========================================
    // 🔥 فتح شاشة إضافة موظف جديد 🔥
    // ==========================================
    public function create()
    {
        abort_if(!auth()->user()->can('create_employees'), 403);

        // 1. شيلنا الـ where('is_active', 1) لأن العمود ده مش موجود في جدول الأقسام
        $departments = HrDepartment::all();
        $shifts = HrShift::all();
        $grades = HrJobGrade::orderBy('id')->get();
        
        // 2. ضفنا جلب الفروع عشان القائمة المنسدلة تظهر وميضربش إيرور في الـ Blade
        $branches = \App\Models\Branche::where('is_active', 1)->get();

        // 3. جلب الموظفين النشطين لاختيار المدير المباشر للهيكل التنظيمي
        $managers = HrEmployee::where('status', 'active')->get();

        // 4. تمرير الـ branches مع باقي المتغيرات
        return view('backend.hr.employees.create', compact('departments', 'shifts', 'grades', 'managers', 'branches'));
    }

    // ==========================================
    // 🔥 حفظ بيانات الموظف وإنشاء حساب نظام 🔥
    // ==========================================
    public function store(Request $request)
    {
        abort_if(!auth()->user()->can('create_employees'), 403);

        $request->validate([
            'employee_code'    => 'required|unique:hr_employees,employee_code',
            'full_name_ar'     => 'required|string|max:255',
            'phone'            => 'required|unique:hr_employees,phone',
            'national_id'      => 'required|unique:hr_employees,national_id',
            'email'            => 'nullable|email|unique:hr_employees,email|unique:users,email', 
            'job_title'        => 'required|string|max:255',
            'join_date'        => 'required|date',
            'basic_salary'     => 'required|numeric|min:0',
            'hr_department_id' => 'required|exists:hr_departments,id',
            'hr_shift_id'      => 'required|exists:hr_shifts,id',
            'hr_job_grade_id'  => 'nullable|exists:hr_job_grades,id',
            'branch_id'        => 'required|exists:branches,id',
            'manager_id'       => 'nullable|exists:hr_employees,id', // 🔥 إضافة حقل المدير المباشر
            
            // الحقول العمانية والأساسية
            'nationality'      => 'nullable|string',
            'pasi_number'      => 'nullable|string',
            'is_insured'       => 'nullable|boolean',
            'birth_date'       => 'nullable|date',
            'gender'           => 'nullable|in:male,female',
            'profile_image'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'email.unique' => 'البريد الإلكتروني مسجل بالفعل لموظف أو مستخدم آخر.',
            'phone.unique' => 'رقم الهاتف مسجل بالفعل لموظف آخر.',
        ]);

        $data = $request->except(['_token', 'profile_image']);
        $data['is_insured'] = $request->has('is_insured') ? 1 : 0;

        // توليد بريد إلكتروني افتراضي إذا لم يتم إدخاله
        $email = $request->email ?? $request->phone . '@smartstudent.om';

        DB::beginTransaction();
        try {
            // إنشاء مستخدم للسيستم (User Account)
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $request->full_name_ar,
                    'password' => Hash::make($request->phone), // الباسورد الافتراضي
                    'status' => 'active',
                ]
            );

            $data['user_id'] = $user->id;

            // رفع الصورة بطريقة صحيحة وآمنة
            if ($request->hasFile('profile_image')) {
                $data['profile_image'] = $request->file('profile_image')->store('hr_employees', 'public');
            }

            HrEmployee::create($data);

            DB::commit();
            return redirect()->route('admin.hr.employees.index')->with('success', 'تم تسجيل الموظف وإنشاء حساب نظام له بنجاح! (كلمة المرور: رقم الهاتف)');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'حدث خطأ أثناء الحفظ: ' . $e->getMessage()]);
        }
    }

    // ==========================================
    // 🔥 عرض بروفايل الموظف الشامل (360 View) 🔥
    // ==========================================
    public function show($id)
    {
        abort_if(!auth()->user()->can('view_employees'), 403);

        $employee = HrEmployee::with([
            'department', 'shift', 'jobGrade', 'branch', 'manager', // 🔥 جلب المدير
            'contracts', 'documents', 'loans', 'leaveBalances', 
            'custodies.asset.category', 'penalties', 'attendances'    
        ])->findOrFail($id);

        return view('backend.hr.employees.show', compact('employee'));
    }

    // ==========================================
    // 🔥 عرض صفحة تعديل بيانات الموظف 🔥
    // ==========================================
    public function edit($id)
    {
        abort_if(!auth()->user()->can('edit_employees'), 403);

        $employee = HrEmployee::findOrFail($id);
        $departments = HrDepartment::all();
        $shifts = HrShift::all();
        $jobGrades = HrJobGrade::all();
        
        // 🔥 جلب كل المديرين ما عدا الموظف نفسه عشان مينفعش يبقى مدير نفسه! 🔥
        $managers = HrEmployee::where('status', 'active')->where('id', '!=', $id)->get();

        return view('backend.hr.employees.edit', compact('employee', 'departments', 'shifts', 'jobGrades', 'managers'));
    }

    // ==========================================
    // 🔥 تحديث بيانات الموظف 🔥
    // ==========================================
    public function update(Request $request, $id)
    {
        abort_if(!auth()->user()->can('edit_employees'), 403);

        $employee = HrEmployee::findOrFail($id);

        $request->validate([
            'full_name_ar'       => 'required|string|max:255',
            'full_name_en'       => 'nullable|string|max:255',
            'phone'              => 'required|string|max:20|unique:hr_employees,phone,' . $employee->id,
            'email'              => 'nullable|email|max:255|unique:hr_employees,email,' . $employee->id,
            'national_id'        => 'nullable|string|max:50|unique:hr_employees,national_id,' . $employee->id,
            'hr_department_id'   => 'required|exists:hr_departments,id',
            'job_title'          => 'required|string|max:255',
            'hr_shift_id'        => 'required|exists:hr_shifts,id',
            'hr_job_grade_id'    => 'nullable|exists:hr_job_grades,id',
            'branch_id'          => 'required|exists:branches,id',
            'manager_id'         => 'nullable|exists:hr_employees,id', // 🔥 تحديث المدير المباشر
            'join_date'          => 'required|date',
            'basic_salary'       => 'required|numeric|min:0',
            'status'             => 'required|in:active,on_leave,suspended,terminated',
            'profile_image'      => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'nationality'        => 'nullable|string',
            'pasi_number'        => 'nullable|string',
        ]);

        $data = $request->except(['_token', 'profile_image', 'is_insured']);
        $data['is_insured'] = $request->has('is_insured') ? 1 : 0;

        // التعامل مع رفع الصورة
        if ($request->hasFile('profile_image')) {
            if ($employee->profile_image) {
                Storage::disk('public')->delete($employee->profile_image); 
            }
            $data['profile_image'] = $request->file('profile_image')->store('hr_employees', 'public');
        }

        DB::beginTransaction();
        try {
            $employee->update($data);

            // 1. تحديث بيانات مستخدم النظام المرتبط (User)
            if ($employee->user_id) {
                User::where('id', $employee->user_id)->update([
                    'name' => $request->full_name_ar,
                    'email' => $request->email ?? $employee->user->email,
                    'status' => $request->status == 'active' ? 'active' : 'inactive', // إيقاف الحساب لو مفصول
                ]);
            }

            // 🔥 2. تحديث حساب المعلم (لو الموظف ده طلع معلم) 🔥
            $teacher = Teacher::where('hr_employee_id', $employee->id)->first();
            if ($teacher) {
                $teacher->update([
                    'name_ar' => $request->full_name_ar,
                    'phone'   => $request->phone,
                    // لو الموظف تم إيقافه، نوقف حسابه كمعلم فوراً
                    'status'  => $request->status == 'active' ? 'active' : 'suspended',
                ]);
            }

            DB::commit();
            return redirect()->route('admin.hr.employees.index')->with('success', 'تم تحديث بيانات الموظف بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'حدث خطأ: ' . $e->getMessage()]);
        }
    }

    // ==========================================
    // 🔥 رفع وحفظ الوثائق الرسمية للموظف 🔥
    // ==========================================
    public function storeDocument(Request $request)
    {
        abort_if(!auth()->user()->can('edit_employees'), 403);

        $request->validate([
            'employee_id' => 'required|exists:hr_employees,id',
            'document_type' => 'required|string',
            'document_number' => 'required|string',
            'expiry_date' => 'required|date',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $data = $request->except(['_token', 'attachment']);

        if ($request->hasFile('attachment')) {
            $data['attachment'] = $request->file('attachment')->store('hr_documents', 'public');
        }

        HrEmployeeDocument::create($data);

        return redirect()->back()->with('success', 'تم رفع وحفظ الوثيقة الرسمية بنجاح!');
    }

    // ==========================================
    // 🔥 تعديل وثيقة رسمية للموظف 🔥
    // ==========================================
    public function updateDocument(Request $request, $id)
    {
        abort_if(!auth()->user()->can('edit_employees'), 403);

        $request->validate([
            'document_type' => 'required|string',
            'document_number' => 'required|string',
            'expiry_date' => 'required|date',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $document = HrEmployeeDocument::findOrFail($id);
        $data = $request->except(['_token', 'attachment']);

        if ($request->hasFile('attachment')) {
            if ($document->attachment) {
                Storage::disk('public')->delete($document->attachment); 
            }
            $data['attachment'] = $request->file('attachment')->store('hr_documents', 'public');
        }

        $document->update($data);

        return redirect()->back()->with('success', 'تم تعديل الوثيقة بنجاح!');
    }

    // ==========================================
    // 🔥 حذف وثيقة رسمية للموظف 🔥
    // ==========================================
    public function destroyDocument($id)
    {
        abort_if(!auth()->user()->can('edit_employees'), 403);

        $document = HrEmployeeDocument::findOrFail($id);

        if ($document->attachment) {
            Storage::disk('public')->delete($document->attachment);
        }
        
        $document->delete();

        return redirect()->back()->with('success', 'تم حذف الوثيقة بنجاح!');
    }
}