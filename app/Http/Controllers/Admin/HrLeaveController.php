<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HrLeaveRequest;
use App\Models\HrLeaveBalance;
use App\Models\HrAttendance; // 🔥 استدعاء موديل الحضور عشان التسميع 🔥
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Carbon\CarbonPeriod; // 🔥 استدعاء الكلاس ده عشان نلف على أيام الإجازة 🔥

class HrLeaveController extends Controller
{
    // ==========================================
    // 🔥 عرض كل طلبات الإجازة للمدير 🔥
    // ==========================================
    public function index()
    {
        // حماية الشاشة
        abort_if(!auth()->user()->can('manage_leave_requests'), 403, 'غير مصرح لك بإدارة الإجازات.');

        // سحب الطلبات مع العلاقات (القسم، نوع الإجازة، والموظف/المعلم)
        $requests = HrLeaveRequest::with(['employee.department', 'leaveType'])
                                  ->orderBy('created_at', 'desc')
                                  ->get();
                                  
        return view('backend.hr.leaves.index', compact('requests'));
    }

    // ==========================================
    // 🔥 تغيير حالة الطلب (موافقة / رفض) مع حماية الرصيد والتسميع في الحضور 🔥
    // ==========================================
    public function updateStatus(Request $request, $id)
    {
        abort_if(!auth()->user()->can('manage_leave_requests'), 403);

        $request->validate([
            'status' => 'required|in:approved,rejected,pending'
        ]);

        $leaveRequest = HrLeaveRequest::findOrFail($id);
        $newStatus = $request->status;
        $oldStatus = $leaveRequest->status;

        // لو مفيش تغيير في الحالة، نرجع فوراً
        if ($newStatus == $oldStatus) {
            return redirect()->back();
        }

        // 🌟 Transaction لضمان عدم ضياع الرصيد أو تضارب الحضور لو حصل خطأ 🌟
        DB::beginTransaction();
        try {
            $year = Carbon::parse($leaveRequest->start_date)->year;
            
            // نجيب رصيد الموظف/المعلم للسنة دي ونعمل عليه Lock لمنع التضارب
            $balance = HrLeaveBalance::where('employee_id', $leaveRequest->employee_id)
                                     ->where('leave_type_id', $leaveRequest->leave_type_id)
                                     ->where('year', $year)
                                     ->lockForUpdate() 
                                     ->first();

            // ==========================================
            // 1. حالة الموافقة على الطلب (تخصيص الرصيد + تسميع الحضور)
            // ==========================================
            if ($newStatus == 'approved') {
                
                // نتأكد إن الرصيد موجود وبيكفي
                if (!$balance || $balance->remaining_days < $leaveRequest->total_days) {
                    DB::rollBack();
                    return redirect()->back()->with('error', 'عفواً، رصيد إجازات المعلم/الموظف المتبقي لا يكفي للموافقة على هذا الطلب!');
                }

                // خصم الرصيد
                $balance->used_days += $leaveRequest->total_days;
                $balance->remaining_days -= $leaveRequest->total_days;
                $balance->save();

                // 🔥 التسميع التلقائي في جدول الحضور والانصراف 🔥
                // بنعمل فترة (Period) من تاريخ البداية للنهاية ونلف عليهم يوم يوم
                $period = CarbonPeriod::create($leaveRequest->start_date, $leaveRequest->end_date);
                
                foreach ($period as $date) {
                    // تخطي أيام العطلة الأسبوعية (الجمعة والسبت) لأنها مبتتحسبش إجازة مخصومة
                    if ($date->isFriday() || $date->isSaturday()) {
                        continue;
                    }

                    // نسجل الإجازة في الحضور عشان مسير الرواتب يقرأها وميخصمش غياب
                    HrAttendance::updateOrCreate(
                        [
                            'employee_id' => $leaveRequest->employee_id,
                            'date'        => $date->format('Y-m-d'),
                        ],
                        [
                            'check_in'      => null,
                            'check_out'     => null,
                            'delay_minutes' => 0,
                            'status'        => 'on_leave', // 🏖️ الحالة إجازة معتمدة
                            'notes'         => 'إجازة معتمدة من الإدارة - طلب رقم: ' . $leaveRequest->id
                        ]
                    );
                }

            } 
            // ==========================================
            // 2. حالة التراجع عن موافقة سابقة (رفض طلب كان متوافق عليه)
            // ==========================================
            elseif ($oldStatus == 'approved' && in_array($newStatus, ['rejected', 'pending'])) {
                if ($balance) {
                    // إرجاع الرصيد للموظف/المعلم
                    $balance->used_days -= $leaveRequest->total_days;
                    $balance->remaining_days += $leaveRequest->total_days;
                    $balance->save();
                }

                // 🔥 حذف التسميع من جدول الحضور عشان يرجع يتحسبله غياب لو مجاش 🔥
                HrAttendance::where('employee_id', $leaveRequest->employee_id)
                            ->whereBetween('date', [$leaveRequest->start_date, $leaveRequest->end_date])
                            ->where('status', 'on_leave')
                            ->delete();
            }

            // تحديث حالة الطلب
            $leaveRequest->update(['status' => $newStatus]);

            DB::commit();
            return redirect()->back()->with('success', 'تم تحديث حالة الطلب، وتعديل الرصيد، وتسميع الإجازة في سجل الحضور بنجاح!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'حدث خطأ أثناء معالجة الطلب: ' . $e->getMessage());
        }
    }
}