<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HrEmployeeDocument;
use App\Models\HrContract;
use Carbon\Carbon;

class HrAlertController extends Controller
{
    public function index()
    {
        // 🔥 حماية الصفحة بالصلاحيات 🔥
        abort_if(!auth()->user()->can('view_employees'), 403, 'غير مصرح لك بعرض تنبيهات الموارد البشرية.');

        $today = Carbon::now()->startOfDay();
        $thirtyDaysFromNow = Carbon::now()->addDays(30)->endOfDay();
        $sixtyDaysFromNow = Carbon::now()->addDays(60)->endOfDay();

        // 🔥 دالة فلترة الموظفين النشطين فقط (لمنع تنبيهات الموظفين المستقيلين) 🔥
        $activeEmployeesOnly = function($query) {
            $query->whereIn('status', ['active', 'on_leave']);
        };

        // 1. الوثائق التي ستنتهي خلال 30 يوم (للموظفين الحاليين فقط)
        $expiringDocuments = HrEmployeeDocument::with('employee')
            ->whereHas('employee', $activeEmployeesOnly)
            ->whereBetween('expiry_date', [$today, $thirtyDaysFromNow])
            ->orderBy('expiry_date', 'asc')
            ->get();

        // 2. وثائق منتهية بالفعل (خطر قانوني وغرامات)
        $expiredDocuments = HrEmployeeDocument::with('employee')
            ->whereHas('employee', $activeEmployeesOnly)
            ->where('expiry_date', '<', $today)
            ->orderBy('expiry_date', 'desc')
            ->get();

        // 3. العقود التي ستنتهي خلال 60 يوم (لإشعار الموظف بالتجديد)
        $expiringContracts = HrContract::with('employee')
            ->whereHas('employee', $activeEmployeesOnly)
            ->where('status', 'valid')
            ->whereBetween('end_date', [$today, $sixtyDaysFromNow])
            ->orderBy('end_date', 'asc')
            ->get();

        // 4. العقود المنتهية بالفعل (تتطلب تحديث الحالة في النظام)
        $expiredContracts = HrContract::with('employee')
            ->whereHas('employee', $activeEmployeesOnly)
            ->where('status', 'valid') // حالة العقد لسه مسجلة ساري بالخطأ رغم انتهاء التاريخ
            ->where('end_date', '<', $today)
            ->orderBy('end_date', 'desc')
            ->get();

        return view('backend.hr.alerts.index', compact(
            'expiringDocuments', 
            'expiredDocuments',
            'expiringContracts', 
            'expiredContracts',
            'today'
        ));
    }
}