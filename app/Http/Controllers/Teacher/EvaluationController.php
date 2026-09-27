<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ClassSchedule;
use App\Models\Student;
use App\Models\StudentEvaluation;
use App\Notifications\StudentEvaluationSent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class EvaluationController extends Controller
{
    // ==========================================
    // 1. عرض شاشة رصد التقييمات لطلاب شعبة محددة
    // ==========================================
    public function create($schedule_id)
    {
        $teacher = auth('teacher')->user();
        
        // 🌟 التأكد الأمني من ملكية الحصة للمعلم
        $schedule = ClassSchedule::with(['subject', 'grade', 'section'])
            ->where('id', $schedule_id)
            ->where('teacher_id', $teacher->id)
            ->firstOrFail();

        // 🌟 جلب طلاب الشعبة النشطين فقط
        $students = Student::where('section_id', $schedule->section_id)
            ->where('status', 'active')
            ->orderBy('first_name')
            ->get();

        // جلب التقييمات السابقة للملء التلقائي (للتعديل)
        $evaluations = StudentEvaluation::whereIn('student_id', $students->pluck('id'))
            ->where('academic_year_id', $schedule->academic_year_id)
            ->where('evaluated_by', $teacher->id)
            ->get()
            ->keyBy('student_id');

        return view('teacher.evaluations.create', compact('schedule', 'students', 'evaluations'));
    }

    // ==========================================
    // 2. حفظ التقييمات وإرسال الإشعارات بذكاء
    // ==========================================
    public function store(Request $request, $schedule_id)
    {
        $request->validate([
            'evaluations'                       => 'required|array',
            'evaluations.*.overall_level'       => 'required|string|max:100',
            'evaluations.*.weak_subjects'       => 'nullable|string|max:255',
            'evaluations.*.teacher_notes'       => 'nullable|string|max:1000',
            'evaluations.*.previous_year_score' => 'nullable|numeric|min:0|max:100',
        ]);

        $teacher = auth('teacher')->user();
        
        // 🌟 حماية أمنية (IDOR): التأكد من ملكية الحصة قبل الحفظ
        $schedule = ClassSchedule::with('subject')
            ->where('id', $schedule_id)
            ->where('teacher_id', $teacher->id)
            ->firstOrFail();

        // 🌟 حماية ضد الحقن: استخراج الأرقام التعريفية لطلاب الشعبة فقط
        $validStudentIds = Student::where('section_id', $schedule->section_id)
            ->where('status', 'active')
            ->pluck('id')
            ->toArray();

        $studentsToNotify = [];

        try {
            // 🌟 1. حفظ البيانات داخل Transaction لضمان سلامة قاعدة البيانات
            DB::transaction(function () use ($request, $schedule, $teacher, $validStudentIds, &$studentsToNotify) {
                foreach ($request->evaluations as $student_id => $data) {
                    
                    // تجاهل أي طالب لا ينتمي لهذه الشعبة
                    if (!in_array($student_id, $validStudentIds)) {
                        continue;
                    }
                    
                    $evaluation = StudentEvaluation::updateOrCreate(
                        [
                            'student_id'       => $student_id,
                            'academic_year_id' => $schedule->academic_year_id,
                            'evaluated_by'     => $teacher->id,
                        ],
                        [
                            'overall_level'       => $data['overall_level'],
                            'weak_subjects'       => $data['weak_subjects'] ?? null,
                            'teacher_notes'       => $data['teacher_notes'] ?? null,
                            'previous_year_score' => $data['previous_year_score'] ?? null,
                        ]
                    );

                    // تجميع بيانات التقييمات لإرسال الإشعارات لاحقاً
                    $studentsToNotify[$student_id] = $evaluation;
                }
            });

            // 🌟 2. إرسال الإشعارات "خارج" الـ Transaction لعدم إرهاق السيرفر وتجنب الـ N+1 Query
            if (!empty($studentsToNotify)) {
                $students = Student::whereIn('id', array_keys($studentsToNotify))->get();
                
                foreach ($students as $student) {
                    $evaluation = $studentsToNotify[$student->id];
                    Notification::send($student, new StudentEvaluationSent($evaluation, $teacher->name_ar, $schedule->subject->name_ar));
                }
            }

            return redirect()->route('teacher.dashboard')->with('success', 'تم رصد التقييمات وإرسال الإشعارات للطلاب بنجاح!');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'حدث خطأ أثناء حفظ التقييمات: ' . $e->getMessage()]);
        }
    }
}