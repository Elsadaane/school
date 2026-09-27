<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\AcademicYear;
use App\Models\Grade;
use App\Models\Teacher; 
use App\Models\Branche; 
use App\Models\Student;
use App\Models\StudentAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SectionController extends Controller
{
    // ==========================================
    // 1. عرض الشعب الدراسية
    // ==========================================
    public function index()
    {
        // 🔒 حماية الشاشة
        abort_if(!auth()->user()->can('manage_sections'), 403, 'غير مصرح لك بإدارة الشعب الدراسية.');

        $sections = Section::with(['academicYear', 'grade', 'branch'])->latest()->get();
        
        $academicYears = AcademicYear::latest()->get();
        $grades = Grade::latest()->get();
        $teachers = Teacher::where('status', 'active')->get(); 
        $branches = Branche::where('is_active', 1)->get();

        return view('backend.sections.index', compact('sections', 'academicYears', 'grades', 'teachers', 'branches'));
    }

    // ==========================================
    // 2. دوال الـ AJAX للفلترة الديناميكية
    // ==========================================
    public function getBranchFilters($branch_id)
    {
        abort_if(!auth()->user()->can('manage_sections'), 403);
        $branch = Branche::with('academicYears')->findOrFail($branch_id);
        return response()->json(['years' => $branch->academicYears]);
    }

    public function getSectionsAjax(Request $request)
    {
        abort_if(!auth()->user()->can('manage_sections'), 403);
        $sections = Section::where('branch_id', $request->branch_id)
            ->where('academic_year_id', $request->academic_year_id)
            ->where('grade_id', $request->grade_id)
            ->get();
            
        return response()->json($sections);
    }

    public function getAcademicYears($branch_id)
    {
        abort_if(!auth()->user()->can('manage_sections'), 403);
        $years = AcademicYear::whereHas('branches', function ($query) use ($branch_id) {
            $query->where('branch_id', $branch_id);
        })->select('id', 'name')->get();
        
        return response()->json($years);
    }

    public function getSectionsByBranch($branch_id)
    {
        abort_if(!auth()->user()->can('manage_sections'), 403);
        $sections = Section::with(['grade', 'academicYear'])
                           ->where('branch_id', $branch_id)
                           ->get();
        return response()->json($sections);
    }

    public function getGrades($branch_id)
    {
        abort_if(!auth()->user()->can('manage_sections'), 403);
        $grades = Grade::whereHas('branches', function ($query) use ($branch_id) {
            $query->where('branch_id', $branch_id);
        })->select('id', 'name_ar')->orderBy('level_order')->get();
        
        return response()->json($grades);
    }

    // ==========================================
    // 3. إضافة وتعديل وحذف الشعب
    // ==========================================
    public function store(Request $request)
    {
        abort_if(!auth()->user()->can('manage_sections'), 403);

        $request->validate([
            'branch_id'               => 'required|exists:branches,id',
            'academic_year_id'        => 'required|exists:academic_years,id',
            'grade_id'                => 'required|exists:grades,id',
            'sections'                => 'required|array|min:1',
            'sections.*.max_capacity' => 'required|integer|min:1',
            'sections.*.name_ar'      => 'required|string|max:255',
            'sections.*.teachers'     => 'nullable|array', 
            'sections.*.teachers.*'   => 'exists:teachers,id',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->sections as $sectionData) {
                $section = Section::create([
                    'branch_id'        => $request->branch_id,
                    'academic_year_id' => $request->academic_year_id,
                    'max_capacity'     => $sectionData['max_capacity'],
                    'grade_id'         => $request->grade_id,
                    'name_ar'          => $sectionData['name_ar'],
                ]);

                if (!empty($sectionData['teachers'])) {
                    $section->teachers()->attach($sectionData['teachers']);
                }
            }
            DB::commit();
            return redirect()->back()->with('success', 'تمت إضافة الشعب بنجاح.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'حدث خطأ أثناء الإضافة: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        abort_if(!auth()->user()->can('manage_sections'), 403);

        $request->validate([
            'branch_id'        => 'required|exists:branches,id', 
            'academic_year_id' => 'required|exists:academic_years,id',
            'grade_id'         => 'required|exists:grades,id',
            'name_ar'          => 'required|string|max:255',
            'max_capacity'     => 'required|integer|min:1',
            'teachers'         => 'nullable|array',
            'teachers.*'       => 'exists:teachers,id',
        ]);

        DB::beginTransaction();
        try {
            $section = Section::findOrFail($id);
            
            $section->update([
                'branch_id'        => $request->branch_id, 
                'academic_year_id' => $request->academic_year_id,
                'grade_id'         => $request->grade_id,
                'name_ar'          => $request->name_ar,
                'max_capacity'     => $request->max_capacity,
            ]);

            if ($request->has('teachers')) {
                $section->teachers()->sync($request->teachers);
            } else {
                $section->teachers()->detach(); 
            }

            DB::commit();
            return redirect()->back()->with('success', 'تم تعديل بيانات الشعبة بنجاح.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'حدث خطأ أثناء التعديل: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        abort_if(!auth()->user()->can('manage_sections'), 403);

        $section = Section::findOrFail($id);

        if ($section->students()->exists()) {
            return redirect()->back()->with('error', 'عفواً، لا يمكن حذف هذه الشعبة لوجود طلاب مسجلين بداخلها. قم بنقل الطلاب أولاً.');
        }

        $section->delete();
        return redirect()->back()->with('success', 'تم حذف الشعبة بنجاح.');
    }

    // ==========================================
    // 4. النقل الجماعي للطلاب (Bulk Transfer)
    // ==========================================
    public function bulkTransfer(Request $request)
    {
        abort_if(!auth()->user()->can('manage_sections'), 403);

        $request->validate([
            'student_ids'    => 'required|array|min:1',
            'student_ids.*'  => 'exists:students,id',
            'new_branch_id'  => 'required|exists:branches,id',
            'new_section_id' => 'required|exists:sections,id',
        ]);

        DB::beginTransaction();
        try {
            $newSection = Section::findOrFail($request->new_section_id);

            // تأكيد أن الشعبة الجديدة تابعة للفرع المختار لمنع التلاعب عبر الـ UI
            if ($newSection->branch_id != $request->new_branch_id) {
                throw new \Exception('الشعبة المختارة لا تنتمي للفرع المحدد.');
            }

            Student::whereIn('id', $request->student_ids)->update([
                'branch_id'        => $request->new_branch_id,
                'section_id'       => $request->new_section_id,
                'grade_id'         => $newSection->grade_id,
                'academic_year_id' => $newSection->academic_year_id,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'تم نقل الطلاب المحددين إلى الشعبة الجديدة بنجاح.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    // ==========================================
    // 5. شاشة الحضور والغياب واعتماده
    // ==========================================
    public function attendance($id, Request $request)
    {
        abort_if(!auth()->user()->can('manage_attendance'), 403, 'غير مصرح لك بإدارة حضور وغياب الطلاب.');

        $section = Section::with(['branch', 'academicYear', 'grade'])->findOrFail($id);
        $date = $request->attendance_date ?? date('Y-m-d'); 
        
        $students = Student::where('section_id', $id)->orderBy('first_name')->get();
        
        $attendances = StudentAttendance::where('section_id', $id)
            ->where('attendance_date', $date)
            ->get()
            ->keyBy('student_id');

        return view('backend.sections.attendance', compact('section', 'students', 'date', 'attendances'));
    }

    public function storeAttendance(Request $request, $id)
    {
        abort_if(!auth()->user()->can('manage_attendance'), 403);

        $request->validate([
            'attendance_date' => 'required|date',
            'attendances'     => 'required|array',
            'attendances.*'   => 'in:present,absent,late,excused' // 🔥 تأمين المدخلات
        ]);

        DB::beginTransaction();
        try {
            // 🔥 حل مشكلة الـ N+1 Query باستدعاء بيانات الشعبة مرة واحدة فقط 🔥
            $section = Section::findOrFail($id);

            foreach ($request->attendances as $student_id => $status) {
                StudentAttendance::updateOrCreate(
                    [
                        'student_id'      => $student_id, 
                        'attendance_date' => $request->attendance_date
                    ],
                    [
                        'section_id'       => $section->id, 
                        'status'           => $status,
                        'academic_year_id' => $section->academic_year_id, 
                        'grade_id'         => $section->grade_id, 
                        'recorded_by'      => auth()->id(), 
                    ]
                );
            }

            DB::commit();
            return redirect()->back()->with('success', 'تم حفظ واعتماد سجل الحضور بنجاح لتاريخ: ' . $request->attendance_date);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'حدث خطأ أثناء حفظ الحضور: ' . $e->getMessage());
        }
    }

    // ==========================================
    // 6. عرض طلاب الشعبة (Roster)
    // ==========================================
    public function studentsRoster($id)
    {
        abort_if(!auth()->user()->can('view_students'), 403);

        $section = Section::with(['branch', 'academicYear', 'grade'])->findOrFail($id);
        
        $students = Student::where('section_id', $id)
            ->where('branch_id', $section->branch_id) 
            ->latest()
            ->get();

        return view('backend.sections.students_roster', compact('section', 'students'));
    }
}