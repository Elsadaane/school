<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ClassSchedule;
use App\Models\EmergencyOnlineDay;
use App\Models\AcademicYear;
use Illuminate\Http\Request;

class VirtualClassController extends Controller
{
    // ==========================================
    // 1. عرض جدول حصص المدرس لليوم الحالي (البث المباشر)
    // ==========================================
    public function mySchedule()
    {
        $teacher = auth('teacher')->user(); 
        $branchId = $teacher->hrEmployee->branch_id ?? $teacher->branch_id ?? 1;

        $todayName = strtolower(date('l')); 
        $todayDate = date('Y-m-d'); 
        
        $currentYearId = AcademicYear::where('is_current', 1)->value('id');

        // جلب جدول الحصص الأساسي لليوم
        $schedules = ClassSchedule::with(['subject', 'section.grade'])
            ->where('teacher_id', $teacher->id)
            ->where('day_of_week', $todayName)
            ->when($currentYearId, function ($query) use ($currentYearId) {
                $query->where('academic_year_id', $currentYearId);
            })
            ->orderBy('period_number', 'asc')
            ->get();

        // فحص هل اليوم ده "يوم طوارئ أونلاين" 
        $isEmergencyOnline = EmergencyOnlineDay::where('target_date', $todayDate)
            ->where(function($query) use ($branchId) {
                $query->whereNull('branch_id')->orWhere('branch_id', $branchId);
            })->exists();

        // لو يوم طوارئ، اجبر كل الحصص تبقى أونلاين في الذاكرة
        if ($isEmergencyOnline) {
            $schedules->each(fn($schedule) => $schedule->is_virtual = true);
        }

        return view('teacher.virtual_classes.index', compact('schedules', 'isEmergencyOnline'));
    }

    // ==========================================
    // 2. فتح غرفة البث المباشر (Jitsi)
    // ==========================================
    public function enterRoom($id)
    {
        $teacher = auth('teacher')->user(); 
        $branchId = $teacher->hrEmployee->branch_id ?? $teacher->branch_id ?? 1;
        $todayDate = date('Y-m-d'); 

        // 🌟 حماية أمنية (IDOR): التأكد أن المعلم يملك هذه الحصة 🌟
        $schedule = ClassSchedule::with(['subject', 'section'])
            ->where('id', $id)
            ->where('teacher_id', $teacher->id)
            ->firstOrFail();

        // فحص الطوارئ
        $isEmergencyOnline = EmergencyOnlineDay::where('target_date', $todayDate)
            ->where(function($query) use ($branchId) {
                $query->whereNull('branch_id')->orWhere('branch_id', $branchId);
            })->exists();

        // حماية: التأكد إن الحصة أونلاين 
        if (!$schedule->is_virtual && !$isEmergencyOnline) {
            return redirect()->back()->withErrors(['error' => 'عفواً، هذه الحصة مبرمجة كحصة حضورية داخل الفصول.']);
        }

        // 🌟 توحيد التشفير مع كنترولر الطالب لضمان دخول الجميع لنفس الغرفة 🌟
        $roomName = "SmartStudent_" . hash('sha256', $schedule->id . '_' . $schedule->section_id . '_' . date('Y-m-d'));
        
        $subjectName = $schedule->subject->name_ar;
        $sectionName = $schedule->section->name_ar;

        return view('teacher.virtual_classes.room', compact('roomName', 'subjectName', 'sectionName'));
    }
}