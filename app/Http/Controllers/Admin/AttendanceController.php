<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\Branch; // ✅ تم التصحيح من Branche إلى Branch
use App\Models\Section;
use App\Models\AcademicYear;
use App\Models\Branche;
use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    // ==========================================
    // الشاشة الأولى: عرض الشعب والفلاتر
    // ==========================================
    public function index(Request $request)
    {
        $branches = Branche::all(); // ✅ تم التصحيح

        // بناء الاستعلام مع الفلاتر
        $query = Section::with(['grade', 'academicYear', 'branch'])
            ->withCount('students');

        // تطبيق الفلاتر
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }
        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }
        if ($request->filled('grade_id')) {
            $query->where('grade_id', $request->grade_id);
        }
        if ($request->filled('section_id')) {
            $query->where('id', $request->section_id);
        }

        $sections = $query->orderBy('branch_id')->orderBy('grade_id')->get();

        return view('backend.attendances.index', compact('sections', 'branches'));
    }

    // ==========================================
    // 🆕 AJAX: جلب السنوات الدراسية للفرع
    // ==========================================
    public function getBranchAcademicYears($branch_id)
    {
        $years = AcademicYear::whereHas('branches', function($q) use ($branch_id) {
            $q->where('branches.id', $branch_id);
        })->get(['id', 'name']);

        if ($years->isEmpty()) {
            $years = AcademicYear::select('id', 'name')->get();
        }

        return response()->json($years);
    }

    // ==========================================
    // 🆕 AJAX: جلب الصفوف للفرع
    // ==========================================
    public function getBranchGrades($branch_id)
    {
        $grades = Grade::whereHas('sections', function($q) use ($branch_id) {
            $q->where('branch_id', $branch_id);
        })->get(['id', 'name_ar', 'name_en']);

        return response()->json($grades);
    }

    // ==========================================
    // 🆕 AJAX: جلب الشعب للصف والسنة والفرع
    // ==========================================
    public function getGradeSections($branch_id, $year_id, $grade_id)
    {
        $sections = Section::where('branch_id', $branch_id)
            ->where('academic_year_id', $year_id)
            ->where('grade_id', $grade_id)
            ->get(['id', 'name_ar', 'name_en']);

        return response()->json($sections);
    }

    // ==========================================
    // الشاشة الثانية: عرض أيام الغياب لشعبة معينة
    // ==========================================
    public function showDateAttendance($section_id)
    {
        $section = Section::with(['grade', 'academicYear', 'branch'])->findOrFail($section_id);

        $dates = StudentAttendance::where('section_id', $section_id)
            ->select('attendance_date',
                DB::raw('count(student_id) as total_students'),
                DB::raw("sum(case when status = 'present' then 1 else 0 end) as present_count"),
                DB::raw("sum(case when status = 'absent' then 1 else 0 end) as absent_count"),
                DB::raw("sum(case when status = 'late' then 1 else 0 end) as late_count")
            )
            ->groupBy('attendance_date')
            ->orderBy('attendance_date', 'desc')
            ->get();

        return view('backend.attendances.show_date_attendance', compact('section', 'dates'));
    }

    // ==========================================
    // شاشة أخذ الغياب للشعبة
    // ==========================================
    public function create(Request $request)
    {
        $section_id = $request->section_id;
        $date = $request->date ?? date('Y-m-d');
        
        $section = Section::with(['grade', 'academicYear', 'branch'])->findOrFail($section_id);
        $academic_year_id = $section->academic_year_id;

        $students = Student::with('parent')
            ->where('section_id', $section_id)
            ->where('academic_year_id', $academic_year_id)
            ->withCount([
                'attendances as present_days' => function ($query) use ($academic_year_id) {
                    $query->where('status', 'present')->where('academic_year_id', $academic_year_id);
                },
                'attendances as absent_days' => function ($query) use ($academic_year_id) {
                    $query->where('status', 'absent')->where('academic_year_id', $academic_year_id);
                },
                'attendances as late_days' => function ($query) use ($academic_year_id) {
                    $query->where('status', 'late')->where('academic_year_id', $academic_year_id);
                }
            ])
            ->orderBy('first_name')
            ->get();

        $attendances = StudentAttendance::where('section_id', $section_id)
            ->where('attendance_date', $date)
            ->get()
            ->keyBy('student_id');

        return view('backend.attendances.create', compact('students', 'attendances', 'section_id', 'date', 'academic_year_id', 'section'));
    }

    // ==========================================
    // حفظ أو تحديث الغياب الجماعي
    // ==========================================
    public function store(Request $request)
    {
        $request->validate([
            'section_id' => 'required|exists:sections,id',
            'attendance_date' => 'required|date',
            'attendances' => 'required|array',
            'attendances.*' => 'required|in:present,absent,late,excused',
            'absence_reasons' => 'nullable|array',
            'absence_reasons.*' => 'nullable|in:unexcused,sick,family,other',
            'notes' => 'nullable|array',
            'mode' => 'nullable|array',
        ]);

        $section = Section::findOrFail($request->section_id);
        
        $validStudentIds = Student::where('section_id', $section->id)
            ->where('academic_year_id', $section->academic_year_id)
            ->pluck('id')
            ->toArray();

        try {
            DB::beginTransaction();

            foreach ($request->attendances as $student_id => $status) {
                
                if (!in_array($student_id, $validStudentIds)) {
                    continue;
                }
                
                $reason = null;
                if (in_array($status, ['absent', 'late'])) {
                    $reason = $request->absence_reasons[$student_id] ?? null;
                }
                
                $note = $request->notes[$student_id] ?? null;
                $attendanceMode = isset($request->mode[$student_id]) ? 'online' : 'in_person';

                StudentAttendance::updateOrCreate(
                    [
                        'student_id' => $student_id,
                        'attendance_date' => $request->attendance_date,
                    ],
                    [
                        'academic_year_id' => $section->academic_year_id,
                        'grade_id' => $section->grade_id,
                        'section_id' => $section->id,
                        'status' => $status,
                        'attendance_mode' => $attendanceMode,
                        'absence_reason' => $reason,
                        'notes' => $note,
                        'recorded_by' => auth()->id(),
                    ]
                );
            }

            DB::commit();
            return redirect()->back()->with('success', 'تم حفظ وتحديث قائمة الحضور والغياب بنجاح.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors(['error' => 'حدث خطأ: ' . $e->getMessage()]);
        }
    }

    // ==========================================
    // جلب ملف الطالب وتقييماته (AJAX)
    // ==========================================
    public function getStudentProfile($id)
    {
        $student = Student::with([
            'parent',
            'grade',
            'section',
            'evaluations' => function($query) {
                $query->latest()->limit(5);
            }
        ])->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $student
        ]);
    }
}