<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branche;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index()
    {
        $branches = Branche::latest()->get(); // ✅ تم التصحيح
        return view('backend.branches.index', compact('branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        Branche::create([ // ✅ تم التصحيح
            'name_ar' => $request->name_ar,
            'name_en' => $request->name_en,
            'phone' => $request->phone,
            'address' => $request->address,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->back()->with('success', 'تمت إضافة الفرع بنجاح.');
    }

    public function update(Request $request, $id)
    {
        $branch = Branche::findOrFail($id); // ✅ تم التصحيح

        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        $branch->update([
            'name_ar' => $request->name_ar,
            'name_en' => $request->name_en,
            'phone' => $request->phone,
            'address' => $request->address,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->back()->with('success', 'تم تعديل بيانات الفرع بنجاح.');
    }

    public function destroy($id)
    {
        $branch = Branche::findOrFail($id); // ✅ تم التصحيح

        // التحقق من العلاقات (حماية النظام من حذف فرع به بيانات)
        if ($branch->academicYears()->exists()) {
            return redirect()->back()->with('error', 'عفواً! لا يمكن حذف الفرع لوجود سنوات دراسية مرتبطة به.');
        }

        if ($branch->sections()->exists()) {
            return redirect()->back()->with('error', 'عفواً! لا يمكن حذف الفرع لوجود شعب دراسية نشطة داخله.');
        }

        if (method_exists($branch, 'students') && $branch->students()->exists()) {
            return redirect()->back()->with('error', 'عفواً! لا يمكن حذف الفرع لوجود طلاب مسجلين عليه.');
        }

        try {
            $branch->delete();
            return redirect()->back()->with('success', 'تم حذف الفرع بنجاح.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ غير متوقع أثناء محاولة الحذف: ' . $e->getMessage());
        }
    }
}