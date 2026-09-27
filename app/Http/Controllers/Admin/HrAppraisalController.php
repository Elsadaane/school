<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HrAppraisal;
use App\Models\HrEmployee;
use Illuminate\Support\Facades\Auth;

class HrAppraisalController extends Controller
{
    // ==========================================
    // 🔥 عرض سجل التقييمات والأداء 🔥
    // ==========================================
    public function index()
    {
        // حماية الشاشة
        abort_if(!auth()->user()->can('view_employees'), 403, 'غير مصرح لك بمشاهدة تقييمات الموظفين.');

        // 🔥 1. حساب الإحصائيات مباشرة من الداتا بيز (أسرع وتدعم كل البيانات مش الصفحة الأولى بس) 🔥
        $total = HrAppraisal::count();
        $excellent = HrAppraisal::where('score_out_of_100', '>=', 90)->count();
        $good = HrAppraisal::whereBetween('score_out_of_100', [75, 89])->count();
        $needsImprovement = HrAppraisal::where('score_out_of_100', '<', 75)->count();

        // 2. جلب الموظفين النشطين للقائمة المنسدلة
        $employees = HrEmployee::where('status', 'active')->get();

        // 🔥 3. جلب التقييمات مع الترقيم (Pagination) بدلاً من get() 🔥
        $appraisals = HrAppraisal::with(['employee.department', 'evaluator'])
            ->orderBy('evaluation_date', 'desc')
            ->paginate(15);

        return view('backend.hr.appraisals.index', compact('appraisals', 'employees', 'total', 'excellent', 'good', 'needsImprovement'));
    }

    // ==========================================
    // 🔥 حفظ تقييم جديد 🔥
    // ==========================================
    public function store(Request $request)
    {
        abort_if(!auth()->user()->can('edit_employees'), 403);

        $request->validate([
            'employee_id' => 'required|exists:hr_employees,id',
            'evaluation_period' => 'required|string|max:255',
            'evaluation_date' => 'required|date',
            'score_out_of_100' => 'required|integer|min:0|max:100',
            'feedback' => 'nullable|string',
        ]);

        HrAppraisal::create([
            'employee_id' => $request->employee_id,
            'evaluator_id' => Auth::id(), // تسجيل اسم المدير اللي عمل التقييم
            'evaluation_period' => $request->evaluation_period,
            'evaluation_date' => $request->evaluation_date,
            'score_out_of_100' => $request->score_out_of_100,
            'feedback' => $request->feedback,
        ]);

        return redirect()->back()->with('success', 'تم اعتماد تقييم الموظف وحفظه في سجله بنجاح!');
    }

    // ==========================================
    // 🔥 تعديل تقييم موجود 🔥
    // ==========================================
    public function update(Request $request, $id)
    {
        abort_if(!auth()->user()->can('edit_employees'), 403);

        $request->validate([
            'employee_id' => 'required|exists:hr_employees,id',
            'evaluation_period' => 'required|string|max:255',
            'evaluation_date' => 'required|date',
            'score_out_of_100' => 'required|integer|min:0|max:100',
            'feedback' => 'nullable|string',
        ]);

        $appraisal = HrAppraisal::findOrFail($id);

        $appraisal->update([
            'employee_id' => $request->employee_id,
            // 🔥 حافظنا على الـ evaluator_id عشان منغيرش اسم المُقيم الأصلي 🔥
            'evaluation_period' => $request->evaluation_period,
            'evaluation_date' => $request->evaluation_date,
            'score_out_of_100' => $request->score_out_of_100,
            'feedback' => $request->feedback,
        ]);

        return redirect()->back()->with('success', 'تم تعديل التقييم بنجاح!');
    }

    // ==========================================
    // 🔥 حذف التقييم 🔥
    // ==========================================
    public function destroy($id)
    {
        abort_if(!auth()->user()->can('edit_employees'), 403);

        $appraisal = HrAppraisal::findOrFail($id);
        $appraisal->delete();

        return redirect()->back()->with('success', 'تم حذف التقييم من سجل الموظف بنجاح!');
    }
}