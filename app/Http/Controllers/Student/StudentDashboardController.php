<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ClassSchedule;
use App\Models\EmergencyOnlineDay;
use App\Models\StudentContract;
use App\Models\StudentAttendance;
use App\Models\StudentEvaluation;
use App\Models\VirtualClass; // 💡 استدعاء موديل الفصول الافتراضية الجديد
use Carbon\Carbon;

class StudentDashboardController extends Controller
{
    // ==========================================
    // 1. عرض لوحة تحكم الطالب (الداشبورد)
    // ==========================================
    public function index()
    {
        /** @var \App\Models\Student $student */
        $student = auth('student')->user();
        $student->load(['grade', 'section', 'branch']);
        
        $todayName = strtolower(date('l')); 
        $todayDate = date('Y-m-d'); 
        $branchId = $student->branch_id ?? 1;

        // التحقق من حالة الطوارئ (الأونلاين الإجباري)
        $isEmergencyOnline = EmergencyOnlineDay::where('target_date', $todayDate)
            ->where(function($query) use ($branchId) {
                $query->whereNull('branch_id')->orWhere('branch_id', $branchId);
            })->exists();

        // 1. جلب جدول الحصص الأسبوعي الثابت لليوم
        $schedules = ClassSchedule::with(['subject', 'teacher'])
            ->where('section_id', $student->section_id)
            ->where('day_of_week', $todayName)
            ->orderBy('period_number', 'asc')
            ->get();

        // دمج حالة الطوارئ برمجياً في الجدول الأسبوعي
        if ($isEmergencyOnline) {
            $schedules->each(fn($s) => $s->is_virtual = true);
        }

        // 🌟 إضافة قوية: جلب حصص البث المباشر الديناميكية المجدولة لليوم من جدول virtual_classes 🌟
        $liveClasses = VirtualClass::with(['subject', 'teacher'])
            ->where('section_id', $student->section_id)
            ->whereDate('start_at', Carbon::today())
            ->where('is_active', true)
            ->orderBy('start_at', 'asc')
            ->get();

        // 2. جلب الملخص المالي (العقد الفعال)
        $activeContract = StudentContract::where('student_id', $student->id)
            ->where('contract_status', 'active')
            ->latest('contract_date')
            ->first();

        // 3. جلب آخر التقييمات
        $latestEvaluations = StudentEvaluation::with('teacher')
            ->where('student_id', $student->id)
            ->latest()
            ->take(3)
            ->get();

        // 4. إحصائية سريعة للحضور والغياب (للشهر الحالي)
        $attendanceStats = StudentAttendance::where('student_id', $student->id)
            ->whereYear('attendance_date', date('Y'))
            ->whereMonth('attendance_date', date('m'))
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        return view('student.dashboard', compact(
            'student', 'schedules', 'liveClasses', 'isEmergencyOnline', 
            'activeContract', 'latestEvaluations', 'attendanceStats'
        ));
    }

    // ==========================================
    // 2. دخول الطالب لغرفة البث المباشر المجدول (Dynamic Virtual Class)
    // ==========================================
    public function joinLiveClass($id)
    {
        $student = auth('student')->user();
        $liveClass = VirtualClass::with(['subject', 'teacher'])->findOrFail($id);

        // حماية أمنية: التأكد أن البث مخصص لشعبة الطالب
        if ($liveClass->section_id !== $student->section_id) {
            return redirect()->back()->withErrors(['error' => 'غير مصرح لك بدخول هذا البث المباشر.']);
        }

        // إذا كان البث يعتمد على رابط خارجي (زوم / تيمز / جوجل ميت)
        if ($liveClass->join_url) {
            return redirect()->away($liveClass->join_url);
        }

        // إذا كان البث داخلي (Jitsi Room)
        $roomName = $liveClass->meeting_id;
        $subjectName = $liveClass->subject->name_ar;
        $teacherName = $liveClass->teacher->name_ar ?? 'غير محدد';

        return view('student.schedule.room', compact('roomName', 'subjectName', 'teacherName'));
    }

    // ==========================================
    // 3. دخول الطالب للحصص الافتراضية من الجدول الثابت
    // ==========================================
    public function joinVirtualRoom($id)
    {
        $student = auth('student')->user();
        $todayDate = date('Y-m-d'); 

        $schedule = ClassSchedule::with(['subject', 'teacher', 'section'])->findOrFail($id);

        if ($schedule->section_id !== $student->section_id) {
            return redirect()->back()->withErrors(['error' => 'غير مصرح لك بدخول هذه الحصة.']);
        }

        $isEmergencyOnline = EmergencyOnlineDay::where('target_date', $todayDate)
            ->where(function($query) use ($student) {
                $query->whereNull('branch_id')->orWhere('branch_id', $student->branch_id);
            })->exists();

        if (!$schedule->is_virtual && !$isEmergencyOnline) {
            return redirect()->back()->withErrors(['error' => 'هذه الحصة مبرمجة كحصة حضورية في المدرسة.']);
        }

        $roomName = "SmartStudent_" . hash('sha256', $schedule->id . '_' . $schedule->section_id . '_' . date('Y-m-d'));
        $subjectName = $schedule->subject->name_ar;
        $teacherName = $schedule->teacher->name_ar ?? 'غير محدد';

        return view('student.virtual_classes.room', compact('roomName', 'subjectName', 'teacherName'));
    }
}