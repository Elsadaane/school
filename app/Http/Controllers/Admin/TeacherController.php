<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\AcademicYear;
use App\Models\Branche;
use App\Models\HrEmployee; 
use App\Models\HrContract; 
use App\Models\HrDepartment; 
use App\Models\HrShift; 
use App\Models\ClassSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class TeacherController extends Controller
{
    // ==========================================
    // 1. عرض جدول المعلمين
    // ==========================================
    public function index()
    {
        abort_if(!auth()->user()->can('manage_teachers'), 403, 'غير مصرح لك بإدارة المعلمين.');

        $teachers = Teacher::with('subjects')->withCount('sections')->latest()->get();
        return view('backend.teachers.index', compact('teachers'));
    }

    public function getTeachersBySubject(Request $request, $subject_id)
    {
        $teachers = \App\Models\Teacher::with('branch:id,name_ar')
            ->whereHas('subjects', function($q) use ($subject_id) {
                $q->where('subjects.id', $subject_id);
            })
            ->where('status', 'active')
            ->get(['id', 'name_ar', 'branch_id']); 

        return response()->json($teachers);
    }

    // ==========================================
    // 2. صفحة إضافة معلم جديد
    // ==========================================
    public function create()
    {
        abort_if(!auth()->user()->can('manage_teachers'), 403);
        $branches = Branche::where('is_active', 1)->get(); 
        
        $departments = HrDepartment::all();
        $shifts = HrShift::all();

        $managers = HrEmployee::where('status', 'active')->get();

        return view('backend.teachers.create', compact('branches', 'departments', 'shifts', 'managers'));
    }

    // ==========================================
    // 3. حفظ المعلم (وإنشاء ملف HR + عقد أوتوماتيك) 🔥
    // ==========================================
    public function store(Request $request)
    {
        abort_if(!auth()->user()->can('manage_teachers'), 403);

        $request->validate([
            'employee_code'    => 'nullable|string|unique:hr_employees,employee_code', 
            'branch_id'        => 'required|exists:branches,id', 
            'hr_department_id' => 'required|exists:hr_departments,id',
            'hr_shift_id'      => 'required|exists:hr_shifts,id',
            'manager_id'       => 'nullable|exists:hr_employees,id',
            'name_ar'          => 'required|string|max:255',
            'subjects'         => 'required|array|min:1',
            'subjects.*'       => 'exists:subjects,id', 
            'national_id'      => 'required|string|unique:teachers,national_id|unique:hr_employees,national_id',
            'phone'            => 'required|string|unique:teachers,phone|unique:hr_employees,phone',
            'email'            => 'nullable|email|unique:teachers,email|unique:hr_employees,email',
            'password'         => 'required|min:6', 
            'sections'         => 'nullable|array', 
            'signature_data'   => 'required|string', 
            'basic_salary'     => 'required|numeric|min:0', 
            'contract_months'  => 'nullable|integer|min:1', 
            'profile_image'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048', 
        ], [
            'phone.unique' => 'رقم الهاتف مسجل مسبقاً لمعلم أو موظف آخر.',
            'national_id.unique' => 'رقم الهوية مسجل مسبقاً لمعلم أو موظف آخر.',
            'email.unique' => 'البريد الإلكتروني مسجل مسبقاً لمعلم أو موظف آخر.',
        ]);

        try {
            DB::beginTransaction();

            $imagePath = null;
            if ($request->hasFile('profile_image')) {
                $imagePath = $request->file('profile_image')->store('hr_employees', 'public');
            }

            // توليد كود الموظف أوتوماتيكياً بشكل تسلسلي ذكي
            $empCode = $request->employee_code;
            if (!$empCode) {
                $lastEmp = HrEmployee::latest('id')->first();
                $nextSequence = $lastEmp ? $lastEmp->id + 1 : 1;
                $empCode = 'EMP-' . date('Y') . '-' . str_pad($nextSequence, 4, '0', STR_PAD_LEFT);
            }

            // 1. إنشاء ملف الموظف في الـ HR 
            $employee = HrEmployee::create([
                'employee_code'    => $empCode,
                'branch_id'        => $request->branch_id,
                'hr_department_id' => $request->hr_department_id,
                'hr_shift_id'      => $request->hr_shift_id,
                'manager_id'       => $request->manager_id,
                'full_name_ar'     => $request->name_ar,
                'national_id'      => $request->national_id,
                'phone'            => $request->phone,
                'email'            => $request->email,
                'job_title'        => 'معلم',
                'basic_salary'     => $request->basic_salary,
                'join_date'        => now(),
                'status'           => 'active',
                'profile_image'    => $imagePath,
            ]);

            // 2. إنشاء العقد الوظيفي أوتوماتيكياً
            $contractDuration = $request->contract_months ?? 12;
            HrContract::create([
                'employee_id'     => $employee->id,
                'contract_number' => 'TCH-' . date('Y') . '-' . rand(1000, 9999), 
                'start_date'      => now(),
                'end_date'        => now()->addMonths($contractDuration),
                'total_salary'    => $request->basic_salary, 
                'status'          => 'valid'
            ]);

            // 3. إنشاء ملف المعلم الأكاديمي وربطه برقم الموظف (HR)
            $teacher = Teacher::create([
                'hr_employee_id'    => $employee->id,
                'branch_id'         => $request->branch_id, 
                'name_ar'           => $request->name_ar,
                'national_id'       => $request->national_id,
                'phone'             => $request->phone,
                'email'             => $request->email,
                'address'           => $request->address,
                'nationality'       => $request->nationality,
                'password'          => Hash::make($request->password),
                'contract_terms_ar' => $request->contract_terms_ar,
                'contract_terms_en' => $request->contract_terms_en,
                'signature_data'    => $request->signature_data,
                'status'            => 'active',
                // 'image'             => $imagePath, // 💡 لو جدول المدرسين مفهوش عمود image الغي السطر ده عشان ميضربش إيرور
            ]);

            // 4. ربط المواد والشعب
            if ($request->has('subjects')) {
                $teacher->subjects()->sync($request->subjects);
            }
            if ($request->has('sections')) {
                $teacher->sections()->attach($request->sections);
            }

            DB::commit();
            return redirect()->route('admin.teachers.index')->with('success', 'تمت إضافة المعلم، وتوليد كود وظيفي وعقد إداري بنجاح.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'حدث خطأ أثناء الحفظ: ' . $e->getMessage()])->withInput();
        }
    }

    // ==========================================
    // 4. عرض تفاصيل المعلم
    // ==========================================
    public function show($id)
    {
        abort_if(!auth()->user()->can('view_teachers'), 403);
        $teacher = Teacher::with(['sections.grade', 'subjects', 'hrEmployee'])->findOrFail($id);
        return view('backend.teachers.show', compact('teacher'));
    }

    // ==========================================
    // 5. فتح صفحة التعديل
    // ==========================================
    // public function edit($id)
    // {
    //     abort_if(!auth()->user()->can('manage_teachers'), 403);

    //     $teacher = Teacher::with(['sections', 'subjects', 'hrEmployee'])->findOrFail($id);
    //     $teacherSectionIds = $teacher->sections->pluck('id')->toArray();
    //     $branches = Branche::where('is_active', 1)->get(); 
        
    //     $departments = HrDepartment::all();
    //     $shifts = HrShift::all();

    //     $managers = HrEmployee::where('status', 'active');
    //     if ($teacher->hr_employee_id) {
    //         $managers = $managers->where('id', '!=', $teacher->hr_employee_id);
    //     }
    //     $managers = $managers->get();

    //     return view('backend.teachers.edit', compact('teacher', 'teacherSectionIds', 'branches', 'departments', 'shifts', 'managers'));
    // }
    // ==========================================
    // 5. فتح صفحة التعديل
    // ==========================================
    public function edit($id)
    {
        abort_if(!auth()->user()->can('manage_teachers'), 403);

        $teacher = Teacher::with(['sections', 'subjects', 'hrEmployee'])->findOrFail($id);

        // 🔥 كود الإصلاح الذاتي (Self-Healing) عند فتح الصفحة 🔥
        // لو المعلم ملوش ملف HR مربوط، ندور عليه في جدول الـ HR بالهوية أو التليفون ونربطهم ببعض فوراً
        if (!$teacher->hr_employee_id) {
            $existingEmp = HrEmployee::where('national_id', $teacher->national_id)
                ->orWhere('phone', $teacher->phone)
                ->first();

            if ($existingEmp) {
                // نربط المعلم بالملف الإداري
                $teacher->update(['hr_employee_id' => $existingEmp->id]);
                // نحدث الـ Object الحالي ونحمل العلاقة من الداتا بيز عشان تظهر في الشاشة فوراً
                $teacher->hr_employee_id = $existingEmp->id;
                $teacher->load('hrEmployee'); 
            }
        }

        $teacherSectionIds = $teacher->sections->pluck('id')->toArray();
        $branches = Branche::where('is_active', 1)->get(); 
        
        $departments = HrDepartment::all();
        $shifts = HrShift::all();

        $managers = HrEmployee::where('status', 'active');
        if ($teacher->hr_employee_id) {
            $managers = $managers->where('id', '!=', $teacher->hr_employee_id);
        }
        $managers = $managers->get();

        return view('backend.teachers.edit', compact('teacher', 'teacherSectionIds', 'branches', 'departments', 'shifts', 'managers'));
    }

    // ==========================================
    // 6. حفظ التعديلات (تزامن مع الـ HR والعقود) 🔥 مُؤمن بالكامل 🔥
    // ==========================================
    public function update(Request $request, $id)
    {
        abort_if(!auth()->user()->can('manage_teachers'), 403);

        $teacher = Teacher::findOrFail($id);

        // 🔥 كود الإصلاح الذاتي: لو المعلم قديم ومش مربوط بملف HR، نربطه بناءً على الهوية أو التليفون 🔥
        if (!$teacher->hr_employee_id) {
            $existingEmp = HrEmployee::where('national_id', $teacher->national_id)
                ->orWhere('phone', $teacher->phone)
                ->first();
            if ($existingEmp) {
                $teacher->update(['hr_employee_id' => $existingEmp->id]);
                $teacher->hr_employee_id = $existingEmp->id;
            }
        }

        $hrEmployeeId = $teacher->hr_employee_id;

        $request->validate([
            'branch_id'        => 'required|exists:branches,id',
            'hr_department_id' => 'required|exists:hr_departments,id',
            'hr_shift_id'      => 'required|exists:hr_shifts,id',
            'manager_id'       => 'nullable|exists:hr_employees,id', 
            'name_ar'          => 'required|string|max:255',
            'subjects'         => 'required|array|min:1',
            'subjects.*'       => 'exists:subjects,id',
            
            // 🔥 استثناء المعلم الحالي من فحص التكرار لمنع الأخطاء 🔥
            'national_id'      => [
                'required', 'string',
                \Illuminate\Validation\Rule::unique('teachers', 'national_id')->ignore($teacher->id),
                $hrEmployeeId ? \Illuminate\Validation\Rule::unique('hr_employees', 'national_id')->ignore($hrEmployeeId) : \Illuminate\Validation\Rule::unique('hr_employees', 'national_id')
            ],
            'phone'            => [
                'required', 'string',
                \Illuminate\Validation\Rule::unique('teachers', 'phone')->ignore($teacher->id),
                $hrEmployeeId ? \Illuminate\Validation\Rule::unique('hr_employees', 'phone')->ignore($hrEmployeeId) : \Illuminate\Validation\Rule::unique('hr_employees', 'phone')
            ],
            'email'            => [
                'nullable', 'email',
                \Illuminate\Validation\Rule::unique('teachers', 'email')->ignore($teacher->id),
                $hrEmployeeId ? \Illuminate\Validation\Rule::unique('hr_employees', 'email')->ignore($hrEmployeeId) : \Illuminate\Validation\Rule::unique('hr_employees', 'email')
            ],
            
            'password'         => 'nullable|min:6', 
            'sections'         => 'nullable|array',
            'basic_salary'     => 'required|numeric|min:0', 
            'profile_image'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'phone.unique' => 'رقم الهاتف مسجل مسبقاً لمعلم أو موظف آخر.',
            'national_id.unique' => 'رقم الهوية مسجل مسبقاً لمعلم أو موظف آخر.',
            'email.unique' => 'البريد الإلكتروني مسجل مسبقاً لمعلم أو موظف آخر.',
        ]);

        try {
            DB::beginTransaction();

            $imagePath = $teacher->image ?? null; 
            if ($request->hasFile('profile_image')) {
                if ($teacher->hrEmployee && $teacher->hrEmployee->profile_image) {
                    Storage::disk('public')->delete($teacher->hrEmployee->profile_image);
                }
                $imagePath = $request->file('profile_image')->store('hr_employees', 'public');
            }

            // 1. تحديث بيانات المعلم الأكاديمية
            $data = $request->except(['password', 'sections', 'signature_data', 'subjects', 'basic_salary', 'hr_department_id', 'hr_shift_id', 'manager_id', 'profile_image', 'employee_code', 'contract_months']);
            
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }
            if ($imagePath) {
                $data['image'] = $imagePath;
            }
            
            $teacher->update($data);
            
            // 2. تحديث بياناته الإدارية والمالية في الـ HR
            if ($teacher->hr_employee_id) {
                HrEmployee::where('id', $teacher->hr_employee_id)->update([
                    'branch_id'        => $request->branch_id,
                    'hr_department_id' => $request->hr_department_id,
                    'hr_shift_id'      => $request->hr_shift_id,
                    'manager_id'       => $request->manager_id, 
                    'full_name_ar'     => $request->name_ar, 
                    'national_id'      => $request->national_id,
                    'phone'            => $request->phone,
                    'email'            => $request->email,
                    'basic_salary'     => $request->basic_salary,
                    'profile_image'    => $imagePath,
                ]);

                // 3. تحديث راتب العقد الساري تلقائياً لتوافق حسابات Payroll
                HrContract::where('employee_id', $teacher->hr_employee_id)
                    ->where('status', 'valid')
                    ->update([
                        'total_salary' => $request->basic_salary
                    ]);
            }

            // 4. تحديث المواد والشعب
            $teacher->subjects()->sync($request->has('subjects') ? $request->subjects : []);
            $teacher->sections()->sync($request->sections ?? []);

            DB::commit();
            return redirect()->route('admin.teachers.index')->with('success', 'تم تحديث بيانات المعلم، وتحديث راتبه في العقد المبرم بنجاح.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'حدث خطأ أثناء التحديث: ' . $e->getMessage()]);
        }
    } 

    // ==========================================
    // 7. حذف المعلم (بحماية الجدول)
    // ==========================================
    public function destroy($id)
    {
        abort_if(!auth()->user()->can('manage_teachers'), 403);

        $teacher = Teacher::findOrFail($id);
        
        try {
            DB::beginTransaction();

            $hasSchedule = ClassSchedule::where('teacher_id', $id)->exists();
            if ($hasSchedule) {
                return redirect()->back()->withErrors(['error' => 'لا يمكن حذف هذا المعلم لارتباطه بجدول حصص دراسية نشط. قم بتعديل الجدول أولاً.']);
            }

            $teacher->sections()->detach(); 
            $teacher->subjects()->detach(); 
            
            if ($teacher->hr_employee_id) {
                HrContract::where('employee_id', $teacher->hr_employee_id)->update(['status' => 'terminated']);
                
                $hrEmp = HrEmployee::find($teacher->hr_employee_id);
                if ($hrEmp && $hrEmp->profile_image) {
                    Storage::disk('public')->delete($hrEmp->profile_image);
                }
                
                HrEmployee::where('id', $teacher->hr_employee_id)->delete();
            }

            $teacher->delete();

            DB::commit();
            return redirect()->back()->with('success', 'تم حذف المعلم وإنهاء ارتباطاته من الموارد البشرية بنجاح.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'حدث خطأ أثناء الحذف: ' . $e->getMessage()]);
        }
    }

    // ==========================================
    // 8. حفظ التسكين الأكاديمي السريع
    // ==========================================
    public function assignSectionsQuick(Request $request, $id)
    {
        abort_if(!auth()->user()->can('manage_teachers'), 403);

        $teacher = Teacher::findOrFail($id);
        
        $request->validate([
            'sections' => 'nullable|array'
        ]);

        $teacher->sections()->sync($request->sections ?? []);

        return redirect()->back()->with('success', 'تم تحديث التوزيع الأكاديمي للمعلم بنجاح.');
    }

    // ==========================================
    // 9. طباعة عقد المعلم
    // ==========================================
    public function printContract($id)
    {
        abort_if(!auth()->user()->can('manage_teachers'), 403);
        $teacher = Teacher::with('subjects')->findOrFail($id);
        return view('backend.teachers.print_contract', compact('teacher'));
    }

    // ==========================================
    // 10. جلب شجرة الشعب الخاصة بفرع معين (AJAX)
    // ==========================================
    public function getBranchSections($branch_id, $teacher_id = null)
    {
        abort_if(!auth()->user()->can('manage_teachers'), 403);

        try {
            $academicYears = AcademicYear::whereHas('branches', function ($query) use ($branch_id) {
                $query->where('branches.id', $branch_id);
            })
            ->with(['sections' => function ($q) use ($branch_id) {
                $q->where('branch_id', $branch_id)->with('grade'); 
            }])
            ->get();

            $teacherSectionIds = [];
            if ($teacher_id) {
                $teacher = Teacher::find($teacher_id);
                if ($teacher) {
                    $teacherSectionIds = $teacher->sections->pluck('id')->toArray();
                }
            }

            return view('backend.teachers.partials.sections_tree', compact('academicYears', 'teacherSectionIds'))->render();

        } catch (\Throwable $e) {
            return response()->json(['error' => 'حدث خطأ في جلب البيانات.'], 500); 
        }
    }
}