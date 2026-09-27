<?php

namespace App\Http\Controllers\Guardian;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\StudentEvaluation;
use App\Models\StudentContract;
use App\Models\StudentInstallment;
use App\Models\StudentMark;
use App\Models\AcademicYear;
use App\Models\User;
use App\Notifications\LeaveRequestNotification;
use Illuminate\Support\Facades\Notification;

class StudentController extends Controller
{
    // ==========================================
    // 🔥 1. عرض بروفايل الطالب الشامل 🔥
    // ==========================================
    public function show($id)
    {
        $guardian = auth('guardian')->user();

        // جلب العام الدراسي الحالي لضمان عرض بيانات السنة دي بس
        $currentYear = AcademicYear::where('is_current', 1)->first();
        $currentYearId = $currentYear ? $currentYear->id : null;

        // 1. جلب بيانات الطالب (حماية: لازم يكون ابنه)
        $student = Student::with(['branch', 'grade', 'section'])
            ->where('parent_id', $guardian->id)
            ->findOrFail($id);

        // 2. سجل الحضور الشامل والإحصائيات (للعام الحالي فقط)
        $attendances = StudentAttendance::where('student_id', $id)
            ->when($currentYearId, function ($query) use ($currentYearId) {
                $query->where('academic_year_id', $currentYearId);
            })
            ->orderBy('attendance_date', 'desc')
            ->get();

        $attendanceStats = [
            'present' => $attendances->where('status', 'present')->count(),
            'absent'  => $attendances->where('status', 'absent')->count(),
            'late'    => $attendances->where('status', 'late')->count(),
            'excused' => $attendances->where('status', 'excused')->count(),
        ];

// 3. جلب درجات الطالب متجمعة حسب المادة
        $marks = StudentMark::with(['subject', 'category'])
            ->where('student_id', $id)
            ->get()
            ->groupBy('subject_id');
        // 4. التقييمات الأكاديمية والملاحظات (للعام الحالي فقط)
        $evaluations = StudentEvaluation::with('teacher')
            ->where('student_id', $id)
            ->when($currentYearId, function ($query) use ($currentYearId) {
                $query->where('academic_year_id', $currentYearId);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // 5. الملف المالي (العقد الفعال)
        $contract = StudentContract::where('student_id', $id)
            ->where('contract_status', 'active')
            ->latest('contract_date')
            ->first();
        
        $installments = collect();
        if ($contract) {
            $installments = StudentInstallment::where('student_contract_id', $contract->id)
                ->orderBy('due_date', 'asc')
                ->get();
        }

        return view('guardian.student.profile', compact(
            'student', 
            'attendances', 
            'attendanceStats',
            'marks',
            'evaluations', 
            'contract', 
            'installments'
        ));
    }

    // ==========================================
    // 🔥 2. استقبال طلب الاستئذان من ولي الأمر 🔥
    // ==========================================
    public function submitLeaveRequest(Request $request, $id)
    {
        $request->validate([
            'from_date'  => 'required|date|after_or_equal:today', // منع طلب إجازة في الماضي
            'to_date'    => 'required|date|after_or_equal:from_date',
            'reason'     => 'required|string|max:500',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);

        $guardianId = auth('guardian')->id();
        $student = Student::where('parent_id', $guardianId)->findOrFail($id);

        // 🌟 منع تقديم أكثر من طلب معلق لنفس الفترة (حماية من الـ Spam) 🌟
        $existingLeave = \App\Models\StudentLeave::where('student_id', $student->id)
            ->where('status', 'pending')
            ->where(function ($query) use ($request) {
                $query->whereBetween('from_date', [$request->from_date, $request->to_date])
                      ->orWhereBetween('to_date', [$request->from_date, $request->to_date]);
            })
            ->exists();

        if ($existingLeave) {
            return back()->withErrors(['error' => 'هناك طلب استئذان قيد المراجعة بالفعل لهذه الفترة.']);
        }

        $data = [
            'student_id'  => $student->id,
            'guardian_id' => $guardianId,
            'from_date'   => $request->from_date,
            'to_date'     => $request->to_date,
            'reason'      => $request->reason,
            'status'      => 'pending'
        ];

        if ($request->hasFile('attachment')) {
            $data['attachment'] = $request->file('attachment')->store('leave_attachments', 'public');
        }

        $leaveRequest = \App\Models\StudentLeave::create($data);

        // 🌟 إرسال الإشعار للموظفين المختصين (Admins) فقط وليس لكل المستخدمين 🌟
        $admins = User::permission('manage_leaves')->get(); // يتطلب حزمة Spatie
        
        // لو مفيش حد معاه الصلاحية المحددة، نبعت للمدير العام
        if ($admins->isEmpty()) {
            $admins = User::where('id', 1)->get();
        }

        Notification::send($admins, new LeaveRequestNotification($leaveRequest, $student->first_name . ' ' . $student->tribe));

        return back()->with('success', 'تم تقديم طلب الاستئذان بنجاح، وجاري مراجعته من قبل إدارة المدرسة.');
    }

    public function showProfile($id)
    {
        // 1. الأمان: نتأكد إن الطالب ده يخص ولي الأمر اللي عامل تسجيل دخول
        $guardianId = auth('guardian')->id();
        
        $student = Student::with(['branch', 'grade', 'section', 'academicYear'])
            ->where('parent_id', $guardianId)
            ->findOrFail($id); // لو الـ ID غلط أو مش ابنه، هيطلع 404 تلقائياً

        // 2. جلب العام الدراسي الحالي
        $currentYear = AcademicYear::where('is_current', 1)->first();
        $currentYearId = $currentYear ? $currentYear->id : null;

        // 3. سجل الحضور والغياب للعام الحالي
        $attendances = StudentAttendance::where('student_id', $id)
            ->when($currentYearId, function ($query) use ($currentYearId) {
                $query->where('academic_year_id', $currentYearId);
            })
            ->latest('attendance_date')
            ->get();

        $attendanceStats = [
            'present' => $attendances->where('status', 'present')->count(),
            'absent'  => $attendances->where('status', 'absent')->count(),
            'late'    => $attendances->where('status', 'late')->count(),
            'excused' => $attendances->where('status', 'excused')->count(), // ضفت المستأذن عشان واجهتك القديمة
            'total'   => $attendances->count(),
        ];

        // 4. سجل الدرجات (Marks) مجمعة بالمواد
        $marks = StudentMark::with(['subject', 'category'])
            ->where('student_id', $id)
            ->get()
            ->groupBy('subject_id');

        // 5. التقييمات السلوكية
        $evaluations = StudentEvaluation::with(['teacher'])
            ->where('student_id', $id)
            ->latest()
            ->get();

// 6. الملف المالي (العقد الفعال والأقساط)
        $contract = StudentContract::with('academicYear')
            ->where('student_id', $id)
            ->where('contract_status', 'active')
            ->latest('contract_date')
            ->first();
        
        $installments = collect();
        if ($contract) {
            $installments = \App\Models\StudentInstallment::where('student_contract_id', $contract->id)
                ->orderBy('due_date', 'asc')
                ->get();
        }

        // توجيه البيانات لصفحة العرض
        return view('guardian.student.profile', compact(
            'student', 
            'attendances', 
            'attendanceStats', 
            'marks', 
            'evaluations', 
            'contract', // 🔥 رجعناه للمفرد زي ما الـ Blade طالب
            'installments' // 🔥 ضفنا الأقساط
        ));
            }
}