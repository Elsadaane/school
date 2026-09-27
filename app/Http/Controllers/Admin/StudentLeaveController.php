<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StudentLeave;
use App\Models\StudentAttendance;
use Carbon\Carbon;
use Carbon\CarbonPeriod; // 🔥 استدعاء مكتبة الفترات الزمنية 🔥
use Illuminate\Support\Facades\DB;

class StudentLeaveController extends Controller
{
    // ==========================================
    // 1. عرض قائمة طلبات الاستئذان للإدارة
    // ==========================================
    public function index(Request $request)
    {
        // 🔒 حماية الشاشة
        abort_if(!auth()->user()->can('manage_leaves'), 403, 'غير مصرح لك بإدارة طلبات الاستئذان.');

        $query = StudentLeave::with(['student.grade', 'student.section', 'guardian'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $leaves = $query->paginate(15)->withQueryString();

        return view('backend.leave_requests.index', compact('leaves'));
    }

    // ==========================================
    // 2. معالجة الطلب (قبول / رفض) مع الذكاء المحاسبي للأيام
    // ==========================================
    public function updateStatus(Request $request, $id)
    {
        abort_if(!auth()->user()->can('manage_leaves'), 403);

        $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        $leave = StudentLeave::with('student')->findOrFail($id);
        $adminId = auth()->id();

        // لو الحالة الجديدة هي نفس الحالة القديمة، مفيش داعي نعمل أي حاجة
        if ($leave->status === $request->status) {
            return back()->with('info', 'حالة الطلب مطابقة للحالة الحالية بالفعل.');
        }

        try {
            DB::transaction(function () use ($leave, $request, $adminId) {
                
                // تحديث حالة الطلب
                $leave->update(['status' => $request->status]);

                $period = CarbonPeriod::create($leave->from_date, $leave->to_date);

                // 🔥 السيناريو الأول: تم القبول -> تسجيل الغياب كـ "مستأذن" 🔥
                if ($request->status == 'approved') {
                    
                    foreach ($period as $date) {
                        // نتخطى أيام الإجازة الأسبوعية (الجمعة والسبت)
                        if ($date->isFriday() || $date->isSaturday()) {
                            continue;
                        }

                        StudentAttendance::updateOrCreate(
                            [
                                'student_id'      => $leave->student_id,
                                'attendance_date' => $date->format('Y-m-d'),
                            ],
                            [
                                'academic_year_id' => $leave->student->academic_year_id,
                                'grade_id'         => $leave->student->grade_id,
                                'section_id'       => $leave->student->section_id,
                                'status'           => 'excused', 
                                'notes'            => 'تم تسجيله تلقائياً بناءً على طلب استئذان معتمد رقم: ' . $leave->id,
                                'absence_reason'   => $leave->reason,
                                'recorded_by'      => $adminId,
                            ]
                        );
                    }
                } 
                // 🔥 السيناريو الثاني: تم الرفض (وكان مقبولاً من قبل) -> مسح أيام الاستئذان 🔥
                elseif ($request->status == 'rejected') {
                    
                    $datesArray = [];
                    foreach ($period as $date) {
                        $datesArray[] = $date->format('Y-m-d');
                    }

                    // نحذف فقط السجلات اللي اتسجلت كـ "مستأذن" في الفترة دي عشان منمسحش غياب حقيقي
                    StudentAttendance::where('student_id', $leave->student_id)
                        ->whereIn('attendance_date', $datesArray)
                        ->where('status', 'excused')
                        ->delete();
                }
            });

            // يمكن إضافة كود إرسال إشعار (Notification) لولي الأمر هنا

            return back()->with('success', 'تم تحديث حالة طلب الاستئذان ومزامنة سجل الحضور بنجاح.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'حدث خطأ أثناء المعالجة: ' . $e->getMessage()]);
        }
    }

    // ==========================================
    // 3. دالة وسيطة لقبول الطلب (Approve)
    // ==========================================
    public function approve(Request $request, $id)
    {
        // بنحقن حالة "القبول" في الـ Request ونبعته للدالة الرئيسية
        $request->merge(['status' => 'approved']);
        return $this->updateStatus($request, $id);
    }

    // ==========================================
    // 4. دالة وسيطة لرفض الطلب (Reject)
    // ==========================================
    public function reject(Request $request, $id)
    {
        // بنحقن حالة "الرفض" في الـ Request ونبعته للدالة الرئيسية
        $request->merge(['status' => 'rejected']);
        return $this->updateStatus($request, $id);
    }
}