<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HrShiftRoster;
use App\Models\HrEmployee;
use App\Models\HrShift;
use Carbon\Carbon;

class HrShiftRosterController extends Controller
{
    // ==========================================
    // 🔥 1. عرض جدول الورديات (الروستر) 🔥
    // ==========================================
    public function index(Request $request)
    {
        // حماية الشاشة
        abort_if(!auth()->user()->can('manage_hr_attendances'), 403, 'غير مصرح لك بإدارة جداول الورديات.');

        // إتاحة الفلترة من الواجهة، والافتراضي هو الأسبوع الحالي
        $startOfWeek = $request->input('start_date', Carbon::now()->startOfWeek()->format('Y-m-d'));
        $endOfWeek = $request->input('end_date', Carbon::now()->endOfWeek()->format('Y-m-d'));

        // جلب الروستر مع العلاقات لحل مشكلة الـ N+1 Query
        $rosters = HrShiftRoster::with(['employee.department', 'shift'])
            ->where(function ($query) use ($startOfWeek, $endOfWeek) {
                // التأكد من جلب أي وردية تتقاطع مع الفترة المحددة
                $query->whereBetween('start_date', [$startOfWeek, $endOfWeek])
                      ->orWhereBetween('end_date', [$startOfWeek, $endOfWeek])
                      ->orWhere(function ($q) use ($startOfWeek, $endOfWeek) {
                          $q->where('start_date', '<=', $startOfWeek)
                            ->where('end_date', '>=', $endOfWeek);
                      });
            })
            ->orderBy('start_date', 'asc')
            ->paginate(15);

        $employees = HrEmployee::where('status', 'active')->get();
        $shifts = HrShift::all(); // لأن مفيش حالة نشط/غير نشط في الجدول

        return view('backend.hr.shift_rosters.index', compact('rosters', 'employees', 'shifts', 'startOfWeek', 'endOfWeek'));
    }

    // ==========================================
    // 🔥 2. تعيين وردية لموظف 🔥
    // ==========================================
    public function store(Request $request)
    {
        abort_if(!auth()->user()->can('manage_hr_attendances'), 403);

        $validated = $request->validate([
            'employee_id' => 'required|exists:hr_employees,id',
            'shift_id'    => 'required|exists:hr_shifts,id',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
        ]);

        // 🌟 الحماية القاتلة للتعارض (Overlap Logic الصح) 🌟
        $overlap = HrShiftRoster::where('employee_id', $request->employee_id)
            ->where('start_date', '<=', $request->end_date)
            ->where('end_date', '>=', $request->start_date)
            ->exists();

        if ($overlap) {
            return redirect()->back()->with('error', 'رفض أمني: يوجد تعارض! الموظف معين بالفعل في وردية أخرى تتقاطع مع هذه الفترة.');
        }

        // استخدام المتغير المفلتر بدلاً من request->all
        HrShiftRoster::create($validated);

        return redirect()->back()->with('success', 'تم إدراج الموظف في جدول الورديات بنجاح!');
    }

    // ==========================================
    // 🔥 3. تعديل تعيين وردية 🔥
    // ==========================================
    public function update(Request $request, $id)
    {
        abort_if(!auth()->user()->can('manage_hr_attendances'), 403);

        $validated = $request->validate([
            'employee_id' => 'required|exists:hr_employees,id',
            'shift_id'    => 'required|exists:hr_shifts,id',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
        ]);

        $roster = HrShiftRoster::findOrFail($id);

        // التأكد من عدم التعارض، مع استثناء الروستر الحالي اللي بنعدله
        $overlap = HrShiftRoster::where('employee_id', $request->employee_id)
            ->where('id', '!=', $id)
            ->where('start_date', '<=', $request->end_date)
            ->where('end_date', '>=', $request->start_date)
            ->exists();

        if ($overlap) {
            return redirect()->back()->with('error', 'رفض أمني: يوجد تعارض زمني مع وردية أخرى مسجلة لهذا الموظف.');
        }

        $roster->update($validated);

        return redirect()->back()->with('success', 'تم تعديل بيانات الوردية بنجاح!');
    }

    // ==========================================
    // 🔥 4. حذف تعيين وردية 🔥
    // ==========================================
    public function destroy($id)
    {
        abort_if(!auth()->user()->can('manage_hr_attendances'), 403);

        $roster = HrShiftRoster::findOrFail($id);
        $roster->delete();
        
        return redirect()->back()->with('success', 'تم إلغاء تعيين الوردية من جدول الموظف.');
    }
}