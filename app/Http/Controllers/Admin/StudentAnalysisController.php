<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentMark;
use App\Models\StudentAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // ✅ مطلوب لاستعلامات DB::table

class StudentAnalysisController extends Controller
{
public function index(Request $request)
{
    abort_if(!auth()->user()->can('view_reports'), 403, 'غير مصرح لك بالاطلاع على تحليلات النظام.');

    $totalStudents = Student::where('status', 'active')->count();
    
    // ✅ تم إضافة 'attendances' إلى with() لمنع مشكلة N+1
    $atRiskStudents = Student::with(['grade', 'section', 'attendances'])
        ->where('status', 'active') 
        ->where(function ($query) {
            $query->whereHas('attendances', fn($q) => $q->where('status', 'absent'), '>=', 5)
                  ->orWhereHas('marks', fn($q) => $q->where('mark_obtained', '<', 50));
        })->take(10)->get();

    // ✅ استخدام withAvg لضمان عدالة التقييم (GPA Logic)
    $topStudents = Student::with(['grade', 'section'])
        ->where('status', 'active')
        ->withAvg('marks', 'mark_obtained')
        ->orderByDesc('marks_avg_mark_obtained')
        ->take(5)->get();

    // ✅ تم تعديل الاستعلام ليحسب النسبة المئوية الفعلية بالقسمة على max_mark
    $subjectAnalysis = DB::table('student_marks')
        ->join('exam_categories', 'student_marks.exam_category_id', '=', 'exam_categories.id')
        ->join('subjects', 'student_marks.subject_id', '=', 'subjects.id')
        ->selectRaw('subjects.id, subjects.name_ar as subject_name, 
                     AVG((student_marks.mark_obtained / NULLIF(exam_categories.max_mark, 0)) * 100) as avg_mark, 
                     COUNT(CASE WHEN ((student_marks.mark_obtained / NULLIF(exam_categories.max_mark, 0)) * 100) < 50 THEN 1 END) as failed_count')
        ->whereNotNull('student_marks.mark_obtained')
        ->where('student_marks.is_absent', 0)
        ->groupBy('subjects.id', 'subjects.name_ar')
        ->orderByDesc('failed_count')->take(5)->get();

    $attendanceStats = StudentAttendance::selectRaw('
        COUNT(id) as total,
        SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present_count,
        SUM(CASE WHEN status = "absent" THEN 1 ELSE 0 END) as absent_count,
        SUM(CASE WHEN status = "late" THEN 1 ELSE 0 END) as late_count
    ')->first();

    $totalAttendances = $attendanceStats->total ?? 0;
    $attendanceRates = [
        'present' => $totalAttendances > 0 ? round(($attendanceStats->present_count / $totalAttendances) * 100) : 0,
        'absent'  => $totalAttendances > 0 ? round(($attendanceStats->absent_count / $totalAttendances) * 100) : 0,
        'late'    => $totalAttendances > 0 ? round(($attendanceStats->late_count / $totalAttendances) * 100) : 0,
    ];

    return view('backend.reports.ai_analysis', compact('totalStudents', 'atRiskStudents', 'topStudents', 'subjectAnalysis', 'attendanceRates'));
}}