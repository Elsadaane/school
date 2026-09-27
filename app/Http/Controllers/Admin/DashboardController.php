<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Expense;
use App\Models\StudentLeave;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // ==========================================
        // 1. الكروت الذكية (KPIs)
        // ==========================================
        $newStudentsToday = Student::whereDate('created_at', $today)->count();
        $totalActiveStudents = Student::where('status', 'active')->count(); // 🔥 الطلاب الفعالين فقط
        
        $todayRevenue = Payment::whereDate('payment_date', $today)->sum('amount');
        $todayExpenses = Expense::whereDate('expense_date', $today)->sum('total_amount'); // 🔥 مصروفات اليوم

        $lowStockCount = Product::where('type', 'product')->whereColumn('quantity', '<=', 'alert_quantity')->count();
        $pendingLeaves = StudentLeave::where('status', 'pending')->count(); // 🔥 الإجازات المعلقة

        // ==========================================
        // 2. بيانات المخطط البياني (الإيرادات والمصروفات - آخر 7 أيام)
        // ==========================================
        
        // 🔥 الحل: تعريف المتغير كمصفوفة عادية بدلاً من Collection
        $last7Days = [];
        
        // تهيئة المصفوفات
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i)->format('Y-m-d');
            $last7Days[$date] = [
                'revenue' => 0,
                'expense' => 0
            ]; 
        }

        // جلب الإيرادات
        $payments = Payment::select(DB::raw('DATE(payment_date) as date'), DB::raw('SUM(amount) as total'))
            ->where('payment_date', '>=', Carbon::today()->subDays(6))
            ->groupBy('date')
            ->get();

        foreach ($payments as $payment) {
            if (isset($last7Days[$payment->date])) {
                $last7Days[$payment->date]['revenue'] = (float) $payment->total;
            }
        }

        // جلب المصروفات
        $expenses = Expense::select(DB::raw('DATE(expense_date) as date'), DB::raw('SUM(total_amount) as total'))
            ->where('expense_date', '>=', Carbon::today()->subDays(6))
            ->groupBy('date')
            ->get();

        foreach ($expenses as $expense) {
            if (isset($last7Days[$expense->date])) {
                $last7Days[$expense->date]['expense'] = (float) $expense->total;
            }
        }

        // فصل البيانات لتمريرها للـ Chart
        $chartDates = array_keys($last7Days); // استخراج التواريخ
        $chartRevenues = array_column($last7Days, 'revenue'); // استخراج قيم الإيرادات فقط
        $chartExpenses = array_column($last7Days, 'expense'); // استخراج قيم المصروفات فقط

        return view('backend.index', compact(
            'newStudentsToday', 'totalActiveStudents', 
            'todayRevenue', 'todayExpenses', 
            'lowStockCount', 'pendingLeaves',
            'chartDates', 'chartRevenues', 'chartExpenses'
        ));
    }
}