<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentInstallment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RadarController extends Controller
{
    public function index(Request $request)
    {
        // 🔒 حماية الشاشة
        abort_if(!auth()->user()->can('view_reports'), 403, 'غير مصرح لك بالاطلاع على رادار النظام.');

        $today = Carbon::today()->toDateString();
        $thirtyDaysAgo = Carbon::today()->subDays(30)->toDateString(); // فترة مراقبة الغياب

        // ==========================================
        // 🔴 1. رادار المتأخرات المالية (أقساط لم تُسدد وتجاوزت تاريخ الاستحقاق)
        // ==========================================
        $overdueInstallments = StudentInstallment::with(['contract.student.grade', 'contract.student.parent'])
            ->whereColumn('paid_amount', '<', 'amount') 
            ->where('due_date', '<', $today)
            ->orderBy('due_date', 'asc')
            // 🔥 استخدام Custom Pagination لمنع تداخل الصفحات وحماية السيرفر 🔥
            ->paginate(10, ['*'], 'installments_page')
            ->appends($request->all());

        // ==========================================
        // 🟡 2. رادار الغياب المتكرر (طلاب غابوا 3 أيام أو أكثر خلال آخر شهر)
        // ==========================================
        $absentStudents = Student::with(['grade', 'section', 'parent'])
            ->whereHas('attendances', function ($query) use ($thirtyDaysAgo, $today) {
                // 🔥 تحديد فترة الرادار عشان منجبش غياب السنين اللي فاتت 🔥
                $query->where('status', 'absent')
                      ->whereBetween('date', [$thirtyDaysAgo, $today]);
            }, '>=', 3) // الحد الأقصى للإنذار (3 أيام فأكثر)
            ->withCount(['attendances as absent_count' => function ($query) use ($thirtyDaysAgo, $today) {
                $query->where('status', 'absent')
                      ->whereBetween('date', [$thirtyDaysAgo, $today]);
            }])
            ->orderByDesc('absent_count')
            // 🔥 Custom Pagination للجدول التاني 🔥
            ->paginate(10, ['*'], 'absents_page')
            ->appends($request->all());

        return view('backend.reports.radar', compact('overdueInstallments', 'absentStudents'));
    }
}