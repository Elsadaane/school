<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HrCustody;
use App\Models\HrEmployee;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HrCustodyController extends Controller
{
    // ==========================================
    // 🔥 1. عرض سجل العهد 🔥
    // ==========================================
    // ==========================================
    // 🔥 عرض سجل العهد والممتلكات 🔥
    // ==========================================
    public function index()
    {
        // حماية الشاشة
        // abort_if(!auth()->user()->can('view_custody'), 403, 'غير مصرح لك بالاطلاع على سجلات العهد.');

        // 1. حساب الإحصائيات مباشرة من قاعدة البيانات
        $totalItems = HrCustody::count();
        $inUse = HrCustody::where('status', 'in_use')->count();
        $returned = HrCustody::where('status', 'returned')->count();
        $damagedOrLost = HrCustody::whereIn('status', ['damaged', 'lost'])->count();

        // 2. جلب الموظفين النشطين (للقائمة المنسدلة في المودال)
        $employees = HrEmployee::where('status', 'active')->get();

        // 3. جلب العهد مع الترقيم (Pagination) بدلاً من get() 🔥
        $custodies = HrCustody::with('employee')->orderBy('receive_date', 'desc')->paginate(15);

        return view('backend.hr.custodies.index', compact('custodies', 'employees', 'totalItems', 'inUse', 'returned', 'damagedOrLost'));
    }

    // ==========================================
    // 🔥 2. تسليم عهدة جديدة لموظف 🔥
    // ==========================================
    public function store(Request $request)
    {
        abort_if(!auth()->user()->can('manage_asset_custodies'), 403);

        $validated = $request->validate([
            'employee_id'  => 'required|exists:hr_employees,id',
            'item_name'    => 'required|string|max:255',
            'item_code'    => 'nullable|string|max:100', // السيريال نمبر أو الباركود
            'receive_date' => 'required|date',
            'notes'        => 'nullable|string'
        ]);

        $validated['status'] = 'in_use'; // الحالة الافتراضية

        HrCustody::create($validated);

        return redirect()->back()->with('success', 'تم تسجيل تسليم العهدة للموظف بنجاح!');
    }

    // ==========================================
    // 🔥 3. تحديث بيانات العهدة (تعديل) 🔥
    // ==========================================
    public function update(Request $request, $id)
    {
        abort_if(!auth()->user()->can('manage_asset_custodies'), 403);

        $validated = $request->validate([
            'employee_id'  => 'required|exists:hr_employees,id',
            'item_name'    => 'required|string|max:255',
            'item_code'    => 'nullable|string|max:100',
            'receive_date' => 'required|date',
            'notes'        => 'nullable|string'
        ]);

        $custody = HrCustody::findOrFail($id);
        
        // استخدام المخرجات المفلترة فقط لمنع التلاعب
        $custody->update($validated);

        return redirect()->back()->with('success', 'تم تعديل بيانات العهدة بنجاح!');
    }

    // ==========================================
    // 🔥 4. تحديث حالة العهدة (إرجاع أو إتلاف/فقدان) 🔥
    // ==========================================
    public function updateStatus(Request $request, $id)
    {
        abort_if(!auth()->user()->can('manage_asset_custodies'), 403);

        $request->validate([
            'status'       => 'required|in:returned,damaged,lost',
            'return_notes' => 'nullable|string',
        ]);

        $custody = HrCustody::findOrFail($id);

        // إضافة ملاحظات الإرجاع للملاحظات القديمة دون مسحها
        $newNotes = $custody->notes;
        if ($request->return_notes) {
            $newNotes .= "\n--- تحديث يوم (" . Carbon::now()->format('Y-m-d') . ") ---\n" . $request->return_notes;
        }

        $custody->update([
            'status'      => $request->status,
            'return_date' => Carbon::now()->format('Y-m-d'),
            'notes'       => $newNotes,
        ]);

        return redirect()->back()->with('success', 'تم تحديث حالة العهدة بنجاح!');
    }

    // ==========================================
    // 🔥 5. إرجاع عهد متعددة (إخلاء طرف من شاشة العقود) 🔥
    // ==========================================
    public function returnMultiple(Request $request)
    {
        abort_if(!auth()->user()->can('manage_asset_custodies'), 403);

        $request->validate([
            'custody_ids'   => 'required|array',
            'custody_ids.*' => 'exists:hr_custodies,id'
        ]);

        DB::beginTransaction();
        try {
            HrCustody::whereIn('id', $request->custody_ids)->update([
                'status'      => 'returned',
                'return_date' => Carbon::now()->format('Y-m-d'),
                // تحديث آمن للنصوص في قاعدة البيانات
                'notes'       => DB::raw("CONCAT(COALESCE(notes, ''), '\n--- تم الإرجاع عبر إخلاء الطرف المجمع ---')")
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'تم إخلاء طرف الموظف من العهد المحددة بنجاح!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'حدث خطأ أثناء إخلاء الطرف: ' . $e->getMessage()]);
        }
    }

    // ==========================================
    // 🔥 6. حذف العهدة 🔥
    // ==========================================
    public function destroy($id)
    {
        abort_if(!auth()->user()->can('manage_asset_custodies'), 403);

        $custody = HrCustody::findOrFail($id);
        $custody->delete();
        
        return redirect()->back()->with('success', 'تم حذف العهدة من سجلات الموظف!');
    }
}