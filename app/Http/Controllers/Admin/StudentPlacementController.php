<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branche;
use App\Models\AcademicYear;
use App\Models\Grade;
use App\Models\Section;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentPlacementController extends Controller
{
    // ==========================================
    // 1. عرض شاشة التسكين
    // ==========================================
    public function index()
    {
        // 🔒 حماية الشاشة
        abort_if(!auth()->user()->can('manage_students'), 403, 'غير مصرح لك بإدارة تسكين الطلاب.');

        $branches = Branche::where('is_active', 1)->get();
        $academicYears = AcademicYear::all();
        $grades = Grade::orderBy('level_order')->get();

        return view('backend.placements.index', compact('branches', 'academicYears', 'grades'));
    }

    // ==========================================
    // 2. دالة لجلب الطلاب + الشعب المتاحة (AJAX)
    // ==========================================
    public function fetch(Request $request)
    {
        abort_if(!auth()->user()->can('manage_students'), 403);

        $request->validate([
            'branch_id'        => 'required|exists:branches,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'grade_id'         => 'required|exists:grades,id',
            'placement_type'   => 'required|in:new,transfer', // 🔥 تسكين جديد أم نقل؟
            'from_section_id'  => 'required_if:placement_type,transfer|nullable|exists:sections,id' // مطلوب فقط في حالة النقل
        ]);

        // 1. بناء استعلام جلب الطلاب النشطين
        $query = Student::where('branch_id', $request->branch_id)
            ->where('academic_year_id', $request->academic_year_id)
            ->where('grade_id', $request->grade_id)
            ->where('status', 'active');

        // 🔥 فلترة الطلاب بناءً على نوع العملية 🔥
        if ($request->placement_type === 'transfer') {
            $query->where('section_id', $request->from_section_id);
        } else {
            $query->whereNull('section_id');
        }

        $students = $query->get();

        // 2. جلب الشعب المتاحة في الصف ده مع حساب العدد الحالي
        $sections = Section::where('branch_id', $request->branch_id)
            ->where('academic_year_id', $request->academic_year_id)
            ->where('grade_id', $request->grade_id)
            ->withCount('students') 
            ->get();

        return response()->json([
            'students' => $students,
            'sections' => $sections
        ]);
    }

    // ==========================================
    // 3. دالة حفظ التسكين أو النقل (مع حماية التزامن)
    // ==========================================
    public function store(Request $request)
    {
        abort_if(!auth()->user()->can('manage_students'), 403);

        $request->validate([
            'section_id'    => 'required|exists:sections,id',
            'student_ids'   => 'required|array|min:1',
            'student_ids.*' => 'exists:students,id'
        ]);

        try {
            DB::beginTransaction();

            $targetSectionId = $request->section_id;

            // 🔥 تنظيف مسبق: استبعاد الطلاب اللي هما أصلاً في نفس الشعبة المستهدفة 🔥
            $validStudentIds = Student::whereIn('id', $request->student_ids)
                ->where(function($q) use ($targetSectionId) {
                    $q->whereNull('section_id')
                      ->orWhere('section_id', '!=', $targetSectionId);
                })
                ->pluck('id')
                ->toArray();

            if (empty($validStudentIds)) {
                DB::rollBack();
                return response()->json([
                    'status'  => 'error',
                    'message' => 'الطلاب المحددين متواجدين بالفعل في هذه الشعبة!'
                ], 422);
            }

            // 🔥 حماية التزامن (Pessimistic Locking): قفل الشعبة مؤقتاً لحساب الكثافة بدقة 🔥
            $section = Section::lockForUpdate()->findOrFail($targetSectionId);
            
            $currentStudentsCount = $section->students()->count();
            $studentsToAssignCount = count($validStudentIds);
            $availableSeats = $section->max_capacity - $currentStudentsCount;

            // التأكد إن الشعبة تستوعب العدد المحدد داخل الـ Transaction
            if ($studentsToAssignCount > $availableSeats) {
                DB::rollBack();
                return response()->json([
                    'status'  => 'error',
                    'message' => "عفواً، الشعبة لا تستوعب هذا العدد! الأماكن الشاغرة: $availableSeats فقط."
                ], 422);
            }

            // تحديث الـ section_id للطلاب الصالحين للنقل
            Student::whereIn('id', $validStudentIds)
                ->update(['section_id' => $targetSectionId]);

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'تم تسكين/نقل الطلاب بنجاح في الشعبة المحددة!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'حدث خطأ أثناء التنفيذ: ' . $e->getMessage()
            ], 500);
        }
    }
}