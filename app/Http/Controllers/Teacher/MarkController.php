<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Student;
use App\Models\ExamCategory;
use App\Models\StudentMark;
use App\Models\AcademicTerm;
use App\Models\ClassSchedule; // 🔥 استدعاء الجداول للحماية الأمنية 🔥

class MarkController extends Controller
{
    // ==========================================
    // 1. عرض شاشة الرصد (Excel Grid)
    // ==========================================
    public function index($section_id, $subject_id)
    {
        $teacherId = auth('teacher')->id();

        // 🌟 حماية أمنية صارمة (IDOR): التأكد أن المعلم يدرس هذه المادة لهذه الشعبة 🌟
        $isAuthorized = ClassSchedule::where('teacher_id', $teacherId)
            ->where('section_id', $section_id)
            ->where('subject_id', $subject_id)
            ->exists();

        abort_if(!$isAuthorized, 403, 'مرفوض أمنياً: غير مصرح لك بالوصول لدفتر درجات هذه الشعبة.');

        // جلب الفصل الدراسي النشط
        $term = AcademicTerm::where('is_active', 1)->firstOrFail();
        
        $section = Section::findOrFail($section_id);
        $subject = Subject::findOrFail($subject_id);

        // جلب أدوات التقويم
        $categories = ExamCategory::where('academic_term_id', $term->id)
            ->where('grade_id', $section->grade_id)
            ->where('subject_id', $subject_id)
            ->orderBy('sort_order')
            ->get();

        // 🌟 جلب طلاب الشعبة (النشطين فقط) 🌟
        $students = Student::where('section_id', $section_id)
            ->where('status', 'active')
            ->orderBy('first_name')
            ->get();

        // جلب الدرجات المرصودة مسبقاً
        $marks = StudentMark::whereIn('student_id', $students->pluck('id'))
            ->where('subject_id', $subject_id)
            ->get()
            ->groupBy('student_id');

        return view('teacher.marks.index', compact('section', 'subject', 'categories', 'students', 'marks', 'term'));
    }

    // ==========================================
    // 2. حفظ الدرجة (AJAX Auto-Save)
    // ==========================================
    public function store(Request $request)
    {
        $request->validate([
            'student_id'       => 'required|exists:students,id',
            'exam_category_id' => 'required|exists:exam_categories,id',
            'subject_id'       => 'required|exists:subjects,id',
            'mark_obtained'    => 'nullable|numeric|min:0'
        ]);

        $teacherId = auth('teacher')->id();
        $student = Student::findOrFail($request->student_id);

        // 🌟 حماية أمنية (Data Injection & IDOR) من التلاعب بطلبات الـ AJAX 🌟
        $isAuthorized = ClassSchedule::where('teacher_id', $teacherId)
            ->where('section_id', $student->section_id)
            ->where('subject_id', $request->subject_id)
            ->exists();

        if (!$isAuthorized) {
            return response()->json([
                'status'  => 'error', 
                'message' => 'مرفوض أمنياً: لا تملك صلاحية رصد درجات هذا الطالب في هذه المادة.'
            ], 403);
        }

        $category = ExamCategory::findOrFail($request->exam_category_id);
        
        // 🌟 حماية تطابق البيانات: التأكد أن فئة التقييم تنتمي لنفس مادة وصف الطالب 🌟
        if ($category->subject_id != $request->subject_id || $category->grade_id != $student->grade_id) {
            return response()->json([
                'status'  => 'error', 
                'message' => 'تلاعب بالبيانات: أداة التقويم لا تتطابق مع مادة أو صف الطالب.'
            ], 422);
        }

        // حماية الباك إند: التأكد من عدم تخطي النهاية العظمى
        if ($request->mark_obtained !== null && $request->mark_obtained > $category->max_mark) {
            return response()->json([
                'status'  => 'error', 
                'message' => 'الدرجة المدخلة تتجاوز النهاية العظمى (' . floatval($category->max_mark) . ')'
            ], 422);
        }

        StudentMark::updateOrCreate(
            [
                'student_id'       => $request->student_id,
                'exam_category_id' => $request->exam_category_id,
            ],
            [
                'subject_id'       => $request->subject_id,
                'teacher_id'       => $teacherId,
                'mark_obtained'    => $request->mark_obtained,
                'academic_year_id' => $student->academic_year_id, // 🔥 لضمان أرشفة درجات السنة الحالية بشكل دقيق 🔥
            ]
        );

        return response()->json(['status' => 'success', 'message' => 'تم الحفظ ✔️']);
    }
}