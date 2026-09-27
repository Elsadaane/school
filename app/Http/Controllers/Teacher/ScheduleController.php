<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ClassSchedule;
use App\Models\EmergencyOnlineDay;
use App\Models\AcademicYear;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    // ==========================================
    // 🔥 عرض جدول الحصص الأسبوعي للمعلم 🔥
    // ==========================================
    public function index(Request $request)
    {
        $teacher = auth('teacher')->user();
        
        // 🌟 جلب العام الدراسي الحالي لمنع تداخل جداول السنوات السابقة 🌟
        $currentYearId = AcademicYear::where('is_current', 1)->value('id');

        // جلب جدول الحصص مع الترتيب حسب رقم الحصة من الداتا بيز مباشرة
        $schedulesQuery = ClassSchedule::with(['subject', 'section.grade', 'branch'])
            ->where('teacher_id', $teacher->id)
            ->when($currentYearId, function ($query) use ($currentYearId) {
                $query->where('academic_year_id', $currentYearId);
            })
            ->orderBy('period_number', 'asc'); // ترتيب الحصص زمنياً

        if ($request->filled('branch_id')) {
            $schedulesQuery->where('branch_id', $request->branch_id);
        }

        $schedules = $schedulesQuery->get();

        // ترتيب الحصص حسب أيام الأسبوع لعرضها في الجدول
        $daysOrder = [
            'sunday' => 1, 'monday' => 2, 'tuesday' => 3, 
            'wednesday' => 4, 'thursday' => 5, 'friday' => 6, 'saturday' => 7
        ];

        $schedulesByDay = $schedules->sortBy(function ($schedule) use ($daysOrder) {
            return $daysOrder[strtolower($schedule->day_of_week)] ?? 99;
        })->groupBy('day_of_week');

        // التحقق من حالات الطوارئ (الأونلاين الإجباري)
        $today = Carbon::today()->format('Y-m-d');
        $teacherBranchId = $teacher->hrEmployee->branch_id ?? $teacher->branch_id ?? 1;
        
        $emergencyToday = EmergencyOnlineDay::where('target_date', $today)
            ->where(function($q) use ($teacherBranchId) {
                $q->whereNull('branch_id')
                  ->orWhere('branch_id', $teacherBranchId);
            })->first();

        // توجيه المعلم لصفحة العرض (View)
        if (view()->exists('teacher.schedule.index')) {
            return view('teacher.schedule.index', compact('schedulesByDay', 'teacher', 'emergencyToday'));
        }

        // رسالة مؤقتة لحين تصميم شاشة الـ Blade
        return response()->json([
            'message'          => 'مرحباً بك يا ' . $teacher->name_ar . ' في صفحة جدول الحصص.',
            'status'           => 'واجهة المستخدم (Blade) قيد التصميم...',
            'emergency_status' => $emergencyToday ? 'يوجد حالة طوارئ اليوم، جميع الحصص أونلاين' : 'يوم دراسي طبيعي',
            'schedules_count'  => $schedules->count(),
            'data'             => $schedulesByDay
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
}