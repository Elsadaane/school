<?php

namespace App\Http\Controllers\Guardian;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\StudentContract;
use App\Models\StudentInstallment;
use App\Models\StudentAttendance;
use App\Models\StudentEvaluation;
use App\Models\StudentMark; // 🔥 استدعاء موديل الدرجات 🔥
use App\Models\AcademicYear;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // استخدام الجارد الخاص بولي الأمر
        $guardian = auth('guardian')->user();

        // 🌟 جلب العام الدراسي الحالي لضمان دقة الإحصائيات 🌟
        $currentYear = AcademicYear::where('is_current', 1)->first();
        $currentYearId = $currentYear ? $currentYear->id : null;

        // 1. جلب الأبناء (النشطين حالياً فقط) مع بياناتهم
        $children = Student::with(['branch', 'grade', 'section'])
            ->where('parent_id', $guardian->id)
            ->where('status', 'active') // 🔥 استبعاد الخريجين من الواجهة الرئيسية
            ->get();

        $childrenIds = $children->pluck('id');

        // 2. الملخص المالي (العقود النشطة)
        $activeContracts = StudentContract::whereIn('student_id', $childrenIds)
            ->where('contract_status', 'active')
            ->get();

        $totalRequired = $activeContracts->sum('grand_total');
        $totalPaid = $activeContracts->sum('paid_amount');
        $totalDue = $activeContracts->sum('total_remaining');

        // جلب القسط القادم المستحق
        $nextInstallment = StudentInstallment::whereIn('student_contract_id', $activeContracts->pluck('id'))
            ->whereIn('status', ['unpaid', 'partial'])
            ->orderBy('due_date', 'asc')
            ->first();

        // 3. حضور وغياب اليوم
        $today = Carbon::today()->toDateString();
        $todayAttendance = StudentAttendance::whereIn('student_id', $childrenIds)
            ->where('attendance_date', $today)
            ->get()
            ->keyBy('student_id');

        // 4. إحصائية إجمالي أيام الغياب (للعام الدراسي الحالي فقط) 🔥
        // تم إزالة clone
        $absencesCount = $childrenIds->isEmpty() ? collect() : StudentAttendance::whereIn('student_id', $childrenIds)
            ->where('status', 'absent')
            ->when($currentYearId, function ($query) use ($currentYearId) {
                $query->where('academic_year_id', $currentYearId);
            })
            ->selectRaw('student_id, count(*) as total_absent')
            ->groupBy('student_id')
            ->pluck('total_absent', 'student_id');

        // 5. أحدث التقييمات السلوكية/الأكاديمية
        // تم إزالة clone
        $latestEvaluations = $childrenIds->isEmpty() ? collect() : StudentEvaluation::with(['student', 'teacher'])
            ->whereIn('student_id', $childrenIds)
            ->latest()
            ->take(5)
            ->get();

        // 6. 🔥 أحدث درجات الامتحانات (Marks) 🔥
        // تم إزالة clone
        $latestMarks = $childrenIds->isEmpty() ? collect() : StudentMark::with(['student', 'subject'])
            ->whereIn('student_id', $childrenIds)
            ->latest()
            ->take(5)
            ->get();

        return view('guardian.dashboard', compact(
            'guardian', 
            'children', 
            'totalDue', 
            'totalPaid', 
            'nextInstallment', 
            'todayAttendance', 
            'absencesCount', 
            'latestEvaluations',
            'latestMarks' // تمرير الدرجات للـ View
        ));
    }
}