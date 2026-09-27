<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HrJobCandidate;
use App\Models\HrDepartment;
use App\Models\User;
use App\Models\HrEmployee;   
use App\Models\HrShift; 
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class HrJobCandidateController extends Controller
{
    // ==========================================
    // 🔥 عرض سجل المرشحين للوظائف 🔥
    // ==========================================
    public function index()
    {
        // حماية الشاشة بصلاحيات الـ HR
        abort_if(!auth()->user()->can('view_employees'), 403, 'غير مصرح لك بالاطلاع على سجلات التوظيف.');

        // 🔥 1. حساب الإحصائيات من الداتا بيز مباشرة عشان تكون دقيقة لكل السجلات 🔥
        $total = HrJobCandidate::count();
        $new = HrJobCandidate::where('status', 'new')->count();
        $interviewed = HrJobCandidate::whereIn('status', ['interviewed', 'shortlisted'])->count();
        $hired = HrJobCandidate::where('status', 'hired')->count();

        // 2. جلب الأقسام
        $departments = HrDepartment::all();

        // 🔥 3. جلب المرشحين مع الترقيم (paginate) بدلاً من get() 🔥
        $candidates = HrJobCandidate::with('department')->orderBy('created_at', 'desc')->paginate(15);

        return view('backend.hr.candidates.index', compact('candidates', 'departments', 'total', 'new', 'interviewed', 'hired'));
    }

    // ==========================================
    // 🔥 إضافة مرشح جديد ورفع السيرة الذاتية 🔥
    // ==========================================
    public function store(Request $request)
    {
        abort_if(!auth()->user()->can('edit_employees'), 403);

        $request->validate([
            'full_name' => 'required|string|max:255',
            
            // الحماية: نمنع تكرار التليفون في المرشحين وفي الموظفين
            'phone' => 'required|string|max:20|unique:hr_job_candidates,phone|unique:hr_employees,phone',
            
            // الحماية: نمنع تكرار الإيميل في المرشحين وفي جدول المستخدمين
            'email' => 'nullable|email|unique:hr_job_candidates,email|unique:users,email',
            
            'applied_for_position' => 'required|string|max:255',
            'department_id' => 'nullable|exists:hr_departments,id',
            'cv_attachment' => 'nullable|mimes:pdf,doc,docx|max:5120', // يقبل PDF أو Word لحد 5 ميجا
        ], [
            'phone.unique' => 'عفواً، رقم الهاتف مسجل بالفعل لمرشح أو موظف آخر!',
            'email.unique' => 'عفواً، البريد الإلكتروني مستخدم مسبقاً في النظام!',
        ]);

        $data = $request->except(['_token', 'cv_attachment']);

        // 🔥 استخدام الـ Storage لرفع السير الذاتية بشكل آمن ومعياري 🔥
        if ($request->hasFile('cv_attachment')) {
            $data['cv_attachment'] = $request->file('cv_attachment')->store('hr_cvs', 'public');
        }

        HrJobCandidate::create($data);

        return redirect()->back()->with('success', 'تم تسجيل المرشح ورفع السيرة الذاتية بنجاح!');
    }

    // ==========================================
    // 🔥 تحديث حالة المرشح (وتحويله لموظف رسمي) 🔥
    // ==========================================
    public function updateStatus(Request $request, $id)
    {
        abort_if(!auth()->user()->can('edit_employees'), 403);

        $candidate = HrJobCandidate::findOrFail($id);

        // لو الحالة تعيين، لازم نتأكد إن الراتب والتاريخ مبعوتين
        if ($request->status == 'hired') {
            $request->validate([
                'basic_salary' => 'required|numeric|min:0',
                'join_date' => 'required|date',
            ]);
        }

        DB::beginTransaction();
        try {
            $candidate->update(['status' => $request->status]);

            // 🌟 إنشاء ملف الموظف وحساب الدخول تلقائياً 🌟
            if ($request->status == 'hired') {
                $email = $candidate->email ?? $candidate->phone . '@smartstudent.om'; // الإيميل الافتراضي

                // 1. إنشاء حساب الدخول للموظف الجديد
                $user = User::firstOrCreate(
                    ['email' => $email],
                    [
                        'name' => $candidate->full_name,
                        'password' => Hash::make($candidate->phone),
                        'status' => 'active',
                    ]
                );

                $employeeExists = HrEmployee::where('user_id', $user->id)->exists();

                // 2. إنشاء ملف الموظف في الـ HR
                if (!$employeeExists) {
                    $lastEmployee = HrEmployee::latest('id')->first();
                    $nextId = $lastEmployee ? $lastEmployee->id + 1 : 1;
                    $employeeCode = 'EMP-' . (1000 + $nextId);

                    // تأمين وردية افتراضية لمنع توقف قواعد البيانات
                    $defaultShift = HrShift::first();

                    HrEmployee::create([
                        'user_id'          => $user->id,
                        'employee_code'    => $employeeCode,
                        'full_name_ar'     => $candidate->full_name,
                        'phone'            => $candidate->phone,
                        'email'            => $candidate->email,
                        'hr_department_id' => $candidate->department_id,
                        'job_title'        => $candidate->applied_for_position,
                        'status'           => 'active',
                        'join_date'        => $request->join_date,
                        'basic_salary'     => $request->basic_salary,
                        
                        // إدخال قيم افتراضية مؤقتة للحقول الإجبارية لتفادي أخطاء الـ SQL
                        'national_id'      => 'PENDING-' . time(), 
                        'branch_id'        => 1, // الفرع الرئيسي افتراضياً لحين تعديله من الـ HR
                        'hr_shift_id'      => $defaultShift ? $defaultShift->id : 1, // 🔥 منع مشكلة الوردية
                    ]);
                }

                DB::commit();
                return redirect()->back()->with('success', 'تم تعيين المرشح بمرتب (' . number_format($request->basic_salary, 3) . ' ر.ع)، وإنشاء ملف موظف وحساب دخول له بنجاح!');
            }

            DB::commit();
            return redirect()->back()->with('success', 'تم تحديث حالة المرشح بنجاح!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'حدث خطأ أثناء تحديث البيانات: ' . $e->getMessage()]);
        }
    }
}