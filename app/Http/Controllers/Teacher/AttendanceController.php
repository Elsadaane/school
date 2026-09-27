<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ClassSchedule;
use App\Models\Student;
use App\Models\StudentAttendance;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    // ==========================================
    // 1. عرض شاشة أخذ الغياب
    // ==========================================
    public function create($schedule_id)
    {
        $teacher = auth('teacher')->user();
        
        // 🌟 التأكد إن الحصة دي بتاعة المدرس ده
        $schedule = ClassSchedule::with(['subject', 'grade', 'section'])
            ->where('id', $schedule_id)
            ->where('teacher_id', $teacher->id)
            ->firstOrFail();

        // 🌟 جلب طلاب الشعبة النشطين فقط
        $students = Student::where('section_id', $schedule->section_id)
            ->where('status', 'active') // 🔥 تم تعديلها لتطابق نظامنا 🔥
            ->orderBy('first_name')
            ->get();

        $today = date('Y-m-d');
        
        // جلب سجل الغياب لليوم لتعبئة الفورم في حالة التعديل
        $attendances = StudentAttendance::whereIn('student_id', $students->pluck('id'))
            ->where('attendance_date', $today)
            ->get()
            ->keyBy('student_id'); 

        return view('teacher.attendance.take', compact('schedule', 'students', 'attendances', 'today'));
    }

    // ==========================================
    // 2. حفظ واعتماد الغياب في الداتا بيز
    // ==========================================
    public function store(Request $request, $schedule_id)
    {
        $request->validate([
            'attendance'          => 'required|array',
            'attendance.*.status' => 'required|in:present,absent,late,excused',
            'attendance.*.notes'  => 'nullable|string|max:255'
        ]);

        $teacher = auth('teacher')->user();
        $today = date('Y-m-d');

        // 🌟 حماية أمنية (IDOR Protection): التأكد أن الحصة ملك للمدرس وليست لمدرس آخر 🌟
        $schedule = ClassSchedule::where('id', $schedule_id)
            ->where('teacher_id', $teacher->id)
            ->firstOrFail();

        // 🌟 حماية من حقن البيانات (Data Injection): استخراج أرقام طلاب هذه الشعبة فقط 🌟
        $validStudentIds = Student::where('section_id', $schedule->section_id)
            ->where('status', 'active')
            ->pluck('id')
            ->toArray();

        try {
            DB::transaction(function () use ($request, $schedule, $teacher, $today, $validStudentIds) {
                foreach ($request->attendance as $student_id => $data) {
                    
                    // تجاهل أي محاولة لتسجيل غياب لطالب خارج هذه الشعبة
                    if (!in_array($student_id, $validStudentIds)) {
                        continue;
                    }

                    StudentAttendance::updateOrCreate(
                        [
                            'student_id'      => $student_id,
                            'attendance_date' => $today
                        ],
                        [
                            'academic_year_id' => $schedule->academic_year_id,
                            'grade_id'         => $schedule->grade_id,
                            'section_id'       => $schedule->section_id,
                            'status'           => $data['status'],
                            'notes'            => $data['notes'] ?? null,
                            'recorded_by'      => $teacher->id, // يسجل ID المدرس لتوثيق من أخذ الغياب
                        ]
                    );
                }
            });

            return redirect()->route('teacher.dashboard')->with('success', 'تم حفظ سجل الحضور والغياب بنجاح لحصة: ' . $schedule->subject->name_ar);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'حدث خطأ أثناء الحفظ: ' . $e->getMessage()]);
        }
    }
}