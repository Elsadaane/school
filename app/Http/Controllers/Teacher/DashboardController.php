<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ClassSchedule;
use App\Models\StudentAttendance;
use App\Models\AcademicYear;
use App\Models\EmergencyOnlineDay;
use Carbon\Carbon;

class DashboardController extends Controller
{
    // ==========================================
    // 1. عرض لوحة تحكم المعلم (الداشبورد)
    // ==========================================
    public function index()
    {
        $teacher = auth('teacher')->user();
        $branchId = $teacher->hrEmployee->branch_id ?? 1; // الاعتماد على فرع المعلم في الـ HR
        
        $dayMap = [
            'Sunday' => 'sunday', 'Monday' => 'monday', 'Tuesday' => 'tuesday',
            'Wednesday' => 'wednesday', 'Thursday' => 'thursday',
            'Friday' => 'friday', 'Saturday' => 'saturday'
        ];

        // 🌟 جلب العام الدراسي الحالي لضمان دقة الجدول 🌟
        $currentYearId = AcademicYear::where('is_current', 1)->value('id');

        // 1. بيانات اليوم
        $todayEnum = $dayMap[date('l')] ?? 'sunday';
        $todayDate = date('Y-m-d');
        
        $todayClasses = ClassSchedule::with(['subject', 'grade', 'section', 'branch'])
            ->where('teacher_id', $teacher->id)
            ->where('day_of_week', $todayEnum)
            ->when($currentYearId, function ($query) use ($currentYearId) {
                $query->where('academic_year_id', $currentYearId);
            })
            ->orderBy('period_number', 'asc')
            ->get();

        // 🌟 التحقق من حالة الطوارئ (الأونلاين الإجباري) 🌟
        $isEmergencyOnline = EmergencyOnlineDay::where('target_date', $todayDate)
            ->where(function($query) use ($branchId) {
                $query->whereNull('branch_id')->orWhere('branch_id', $branchId);
            })->exists();

        // تعيين حالة الحصص كأونلاين إذا كان هناك طوارئ
        if ($isEmergencyOnline) {
            $todayClasses->each(fn($class) => $class->is_virtual = true);
        }

        // أ. جلب معرفات الشعب لحصص اليوم
        $todaySectionIds = $todayClasses->pluck('section_id')->unique();
        
        // ب. فحص الشعب التي تم أخذ غيابها اليوم (بواسطة أي معلم) لمنع التكرار
        $sectionsWithAttendanceToday = clone $todaySectionIds->isEmpty() ? collect() : StudentAttendance::whereIn('section_id', $todaySectionIds)
            ->where('attendance_date', $todayDate)
            ->pluck('section_id')
            ->unique()
            ->toArray();

        // ج. تحديد الحصص التي (مطلوب أخذ الغياب فيها)
        $pendingAttendanceClasses = $todayClasses->filter(function($class) use ($sectionsWithAttendanceToday) {
            return !in_array($class->section_id, $sectionsWithAttendanceToday);
        });

        // 2. بيانات غداً (للتجهيز والتحضير)
        $tomorrowEnum = $dayMap[date('l', strtotime('+1 day'))] ?? 'sunday';
        $tomorrowClasses = ClassSchedule::with(['subject', 'grade', 'section', 'branch'])
            ->where('teacher_id', $teacher->id)
            ->where('day_of_week', $tomorrowEnum)
            ->when($currentYearId, function ($query) use ($currentYearId) {
                $query->where('academic_year_id', $currentYearId);
            })
            ->orderBy('period_number', 'asc')
            ->get();

        // 3. جدول الأسبوع بالكامل
        $weeklySchedules = ClassSchedule::with(['subject', 'grade', 'section'])
            ->where('teacher_id', $teacher->id)
            ->when($currentYearId, function ($query) use ($currentYearId) {
                $query->where('academic_year_id', $currentYearId);
            })
            ->orderBy('period_number', 'asc')
            ->get()
            ->groupBy('day_of_week');

        $daysAr = [
            'sunday' => 'الأحد', 'monday' => 'الإثنين', 'tuesday' => 'الثلاثاء',
            'wednesday' => 'الأربعاء', 'thursday' => 'الخميس'
        ];

        return view('teacher.dashboard', compact(
            'todayClasses', 
            'tomorrowClasses', 
            'weeklySchedules', 
            'daysAr',
            'pendingAttendanceClasses',
            'isEmergencyOnline'
        ));
    }
}