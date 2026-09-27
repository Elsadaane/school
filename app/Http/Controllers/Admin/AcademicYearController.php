<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Grade;
use App\Models\Branch; // ✅ تم التصحيح من Branche
use App\Models\Student;
use App\Models\StudentContract;
use App\Models\StudentAttendance;
use App\Models\AcademicArchive;
use App\Models\Branche;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AcademicYearController extends Controller
{
    public function index()
    {
        $academicYears = AcademicYear::with('branches')
            ->withCount([
                'sections',
                'students',
                'sections as active_grades_count' => function ($query) {
                    $query->select(DB::raw('count(distinct(grade_id))'));
                }
            ])
            ->latest()
            ->get();

        $branches = Branche::all(); // ✅ تم التصحيح
        return view('backend.academic_years.index', compact('academicYears', 'branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'branches' => 'required|array|min:1',
            'branches.*' => 'exists:branches,id'
        ]);

        if ($request->has('is_current')) {
            AcademicYear::query()->update(['is_current' => 0]);
        }

        $year = AcademicYear::create([
            'name' => $request->name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_current' => $request->has('is_current') ? 1 : 0,
        ]);

        $year->branches()->sync($request->branches);
        return redirect()->back()->with('success', 'تمت إضافة السنة الدراسية بنجاح.');
    }

    public function show($id)
    {
        $academicYear = AcademicYear::with(['branches', 'sections.branch', 'sections.grade', 'students'])->findOrFail($id);

        $activeGrades = Grade::whereHas('sections', function($query) use ($id) {
            $query->where('academic_year_id', $id);
        })->withCount(['sections' => function($query) use ($id) {
            $query->where('academic_year_id', $id);
        }])->get();

        return view('backend.academic_years.show', compact('academicYear', 'activeGrades'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'branches' => 'required|array|min:1',
        ]);

        $year = AcademicYear::findOrFail($id);

        if ($request->has('is_current')) {
            AcademicYear::where('id', '!=', $id)->update(['is_current' => 0]);
        }

        $year->update([
            'name' => $request->name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_current' => $request->has('is_current') ? 1 : 0,
        ]);

        $year->branches()->sync($request->branches);
        return redirect()->back()->with('success', 'تم التعديل بنجاح.');
    }

    public function destroy($id)
    {
        $year = AcademicYear::findOrFail($id);

        // ✅ حماية جديدة: منع حذف سنة مغلقة ومؤرشفة
        if ($year->is_closed) {
            return redirect()->back()->with('error', 'لا يمكن حذف سنة دراسية مغلقة ومؤرشفة!');
        }

        if ($year->sections()->exists()) {
            return redirect()->back()->with('error', 'عفواً! لا يمكن حذف السنة لوجود شعب دراسية مسجلة عليها.');
        }

        if ($year->students()->exists()) {
            return redirect()->back()->with('error', 'لا يمكن الحذف! هناك طلاب مسجلون بالفعل في هذه السنة الدراسية.');
        }

        try {
            $year->branches()->detach(); // ✅ فصل العلاقات قبل الحذف
            $year->delete();
            return redirect()->back()->with('success', 'تم حذف السنة الدراسية بنجاح.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء الحذف، ربما السنة مرتبطة ببيانات أخرى.');
        }
    }

    // ==========================================
    // 🔒 دالة إغلاق العام الدراسي وأرشفة البيانات (النسخة فائقة الأداء)
    // ==========================================
    public function closeYear(Request $request, $id)
    {
        $academicYear = AcademicYear::findOrFail($id);

        if ($academicYear->is_closed) {
            return redirect()->back()->withErrors(['error' => 'هذه السنة الدراسية مغلقة ومؤرشفة بالفعل!']);
        }

        try {
            DB::beginTransaction();

            $yearId = $academicYear->id;
            $authId = auth()->id();

            // 1. جلب الطلاب مع بياناتهم الأساسية مرة واحدة فقط
            $students = Student::where('academic_year_id', $yearId)->get();
            $studentIds = $students->pluck('id');

            if ($studentIds->isEmpty()) {
                // لو مفيش طلاب، نقفل السنة بس
                $academicYear->update(['is_current' => false, 'is_closed' => true]);
                DB::commit();
                return redirect()->back()->with('success', 'تم إغلاق العام الدراسي بنجاح!');
            }

            // 2. جلب العقود الخاصة بهؤلاء الطلاب في هذه السنة (Mapping by student_id)
            $contracts = StudentContract::whereIn('student_id', $studentIds)
                ->where('academic_year_id', $yearId)
                ->get()
                ->groupBy('student_id');

            // 3. جلب إحصائيات الحضور مجمعة من قاعدة البيانات (استعلام واحد بدلاً من آلاف الاستعلامات!)
            $attendanceStats = StudentAttendance::whereIn('student_id', $studentIds)
                ->where('academic_year_id', $yearId)
                ->selectRaw('student_id, 
                    SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as total_present,
                    SUM(CASE WHEN status = "absent" THEN 1 ELSE 0 END) as total_absent,
                    SUM(CASE WHEN status = "late" THEN 1 ELSE 0 END) as total_late')
                ->groupBy('student_id')
                ->get()
                ->keyBy('student_id');

            // 4. بناء بيانات الأرشيف
            $archiveData = [];
            $now = now();

            foreach ($students as $student) {
                $contract = $contracts->get($student->id)?->first();
                $attendance = $attendanceStats->get($student->id);

                $financialSnapshot = $contract ? [
                    'contract_number' => $contract->contract_number,
                    'grand_total'     => (float) $contract->grand_total,
                    'paid_amount'     => (float) $contract->paid_amount,
                    'debt_amount'     => (float) ($contract->grand_total - $contract->paid_amount),
                    'discount'        => (float) $contract->discount,
                ] : null;

                $academicSnapshot = [
                    'total_present' => $attendance->total_present ?? 0,
                    'total_absent'  => $attendance->total_absent ?? 0,
                    'total_late'    => $attendance->total_late ?? 0,
                ];

                // ✅ حماية البيانات الحساسة: نأخذ الحقول الآمنة فقط ونتجاهل كلمة المرور
                $safeStudentData = $student->only([
                    'first_name', 'second_name', 'third_name', 'fourth_name', 'tribe',
                    'nationality', 'gender', 'dob', 'grade_id', 'section_id', 'branch_id', 'transport_type'
                ]);

                $archiveData[] = [
                    'student_id'         => $student->id,
                    'academic_year_id'   => $yearId,
                    'branch_id'          => $student->branch_id,
                    'grade_id'           => $student->grade_id,
                    'student_snapshot'   => json_encode($safeStudentData),
                    'financial_snapshot' => json_encode($financialSnapshot),
                    'academic_snapshot'  => json_encode($academicSnapshot),
                    'archived_by'        => $authId,
                    'created_at'         => $now,
                    'updated_at'         => $now,
                ];
            }

            // 5. حفظ الأرشيف باستخدام upsert (أداء خيالي وسرعة فائقة)
            if (!empty($archiveData)) {
                AcademicArchive::upsert(
                    $archiveData,
                    ['student_id', 'academic_year_id'],
                    ['branch_id', 'grade_id', 'student_snapshot', 'financial_snapshot', 'academic_snapshot', 'archived_by', 'updated_at']
                );
            }

            // 6. قفل السنة الدراسية
            $academicYear->update([
                'is_current' => false,
                'is_closed'  => true,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'تم إغلاق العام الدراسي بنجاح وأرشفة بيانات جميع الطلاب!');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error closing academic year: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'حدث خطأ أثناء الأرشفة: ' . $e->getMessage()]);
        }
    }
}