<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HrTrainee;
use App\Models\HrDepartment;
use Illuminate\Support\Facades\DB;

class HrTraineeController extends Controller
{
    // ==========================================
    // 🔥 1. عرض سجل المتدربين 🔥
    // ==========================================
    public function index()
    {
        // حماية الشاشة بصلاحيات العرض
        abort_if(!auth()->user()->can('view_employees'), 403, 'غير مصرح لك بالاطلاع على سجلات المتدربين.');

        $trainees = HrTrainee::with('department')->orderBy('created_at', 'desc')->get();
        
        // جلب الأقسام النشطة فقط لاختيارها في فورم الإضافة
        $departments = HrDepartment::where('is_active', 1)->get();

        // إحصائيات سريعة
        $total = $trainees->count();
        $active = $trainees->where('status', 'active')->count();
        $completed = $trainees->where('status', 'completed')->count();

        return view('backend.hr.trainees.index', compact('trainees', 'departments', 'total', 'active', 'completed'));
    }

    // ==========================================
    // 🔥 2. تسجيل متدرب جديد 🔥
    // ==========================================
    public function store(Request $request)
    {
        abort_if(!auth()->user()->can('edit_employees'), 403);

        $validated = $request->validate([
            'full_name'            => 'required|string|max:255',
            'phone'                => 'required|string|max:20|unique:hr_trainees,phone',
            'hr_department_id'     => 'required|exists:hr_departments,id',
            'university_or_school' => 'nullable|string|max:255',
            'start_date'           => 'required|date',
            'end_date'             => 'required|date|after_or_equal:start_date',
        ], [
            'phone.unique' => 'عفواً، رقم الهاتف مسجل بالفعل لمتدرب آخر.'
        ]);

        DB::beginTransaction();
        try {
            // استخدام البيانات المفلترة فقط لحماية قاعدة البيانات
            HrTrainee::create($validated);
            
            DB::commit();
            return redirect()->back()->with('success', 'تم تسجيل المتدرب بنجاح!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'حدث خطأ أثناء التسجيل: ' . $e->getMessage());
        }
    }

    // ==========================================
    // 🔥 3. تعديل بيانات متدرب 🔥
    // ==========================================
    public function update(Request $request, $id)
    {
        abort_if(!auth()->user()->can('edit_employees'), 403);

        $trainee = HrTrainee::findOrFail($id);

        $validated = $request->validate([
            'full_name'            => 'required|string|max:255',
            'phone'                => 'required|string|max:20|unique:hr_trainees,phone,' . $trainee->id,
            'hr_department_id'     => 'required|exists:hr_departments,id',
            'university_or_school' => 'nullable|string|max:255',
            'start_date'           => 'required|date',
            'end_date'             => 'required|date|after_or_equal:start_date',
        ]);

        DB::beginTransaction();
        try {
            $trainee->update($validated);
            
            DB::commit();
            return redirect()->back()->with('success', 'تم تحديث بيانات المتدرب بنجاح!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'حدث خطأ أثناء التحديث: ' . $e->getMessage());
        }
    }

    // ==========================================
    // 🔥 4. تحديث حالة التدريب (اكتمل / أُلغي) 🔥
    // ==========================================
    public function updateStatus(Request $request, $id)
    {
        abort_if(!auth()->user()->can('edit_employees'), 403);

        $request->validate([
            'status' => 'required|in:active,completed,cancelled'
        ]);

        $trainee = HrTrainee::findOrFail($id);
        $trainee->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'تم تحديث حالة المتدرب بنجاح!');
    }

    // ==========================================
    // 🔥 5. حذف متدرب من السجل 🔥
    // ==========================================
    public function destroy($id)
    {
        abort_if(!auth()->user()->can('edit_employees'), 403);

        $trainee = HrTrainee::findOrFail($id);
        $trainee->delete();

        return redirect()->back()->with('success', 'تم حذف بيانات المتدرب من السجلات.');
    }
}