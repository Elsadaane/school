<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeeItem;
use App\Models\Branche;
use Illuminate\Http\Request;

class FeeItemController extends Controller
{
    // ==========================================
    // 1. عرض بنود الرسوم
    // ==========================================
    public function index()
    {
        abort_if(!auth()->user()->can('view_fee_items'), 403, 'غير مصرح لك بعرض بنود الرسوم.');

        // جلب البنود مع ربطها بالفرع لتقليل الاستعلامات
        $feeItems = FeeItem::with('branch')->latest()->get();
        $branches = Branche::where('is_active', 1)->get(); 

        return view('backend.fee_items.index', compact('feeItems', 'branches'));
    }

    // ==========================================
    // 2. إضافة بند رسوم جديد (لعدة فروع معاً)
    // ==========================================
    public function store(Request $request)
    {
        abort_if(!auth()->user()->can('create_fee_items'), 403);

        $validated = $request->validate([
            'branch_id'   => 'required|array', // 🌟 تحويل لاستقبال مصفوفة من الفروع
            'branch_id.*' => 'exists:branches,id', // التأكد إن كل الفروع صحيحة
            'name_ar'     => 'required|string|max:255',
            'name_en'     => 'nullable|string|max:255',
            'price'       => 'required|numeric|min:0',
            'quantity'    => 'required|integer|min:1',
            'tax_percent' => 'required|numeric|min:0|max:100',
        ]);

        // حساب الإجمالي: (السعر * الكمية) + قيمة الضريبة
        $subTotal = $validated['price'] * $validated['quantity'];
        $taxAmount = $subTotal * ($validated['tax_percent'] / 100);
        $total = $subTotal + $taxAmount;

        // 🌟 الدوران (Loop) لإنشاء نفس البند في كل الفروع المحددة 🌟
        foreach ($request->branch_id as $branchId) {
            FeeItem::create([
                'branch_id'   => $branchId,
                'name_ar'     => $validated['name_ar'],
                'name_en'     => $validated['name_en'],
                'price'       => $validated['price'],
                'quantity'    => $validated['quantity'],
                'tax_percent' => $validated['tax_percent'],
                'total'       => $total,
            ]);
        }

        return redirect()->back()->with('success', 'تم إضافة البند للفروع المحددة بنجاح.');
    }

    // ==========================================
    // 3. تعديل بند رسوم موجود (لفرع واحد)
    // ==========================================
    public function update(Request $request, $id)
    {
        abort_if(!auth()->user()->can('edit_fee_items'), 403);

        $feeItem = FeeItem::findOrFail($id);
        
        $validated = $request->validate([
            'branch_id'   => 'required|exists:branches,id', // هنا بيعدل فرع واحد بس
            'name_ar'     => 'required|string|max:255',
            'name_en'     => 'nullable|string|max:255',
            'price'       => 'required|numeric|min:0',
            'quantity'    => 'required|integer|min:1',
            'tax_percent' => 'required|numeric|min:0|max:100',
        ]);

        $subTotal = $validated['price'] * $validated['quantity'];
        $taxAmount = $subTotal * ($validated['tax_percent'] / 100);
        
        $validated['total'] = $subTotal + $taxAmount;

        $feeItem->update($validated);

        return redirect()->back()->with('success', 'تم تعديل بند الرسوم بنجاح.');
    }

    // ==========================================
    // 4. حذف بند الرسوم
    // ==========================================
    public function destroy($id)
    {
        abort_if(!auth()->user()->can('delete_fee_items'), 403);

        $feeItem = FeeItem::findOrFail($id);
        
        // يمكن إضافة فحص هنا لمنع حذف بند تم استخدامه في عقود سابقة (إذا لزم الأمر)
        
        $feeItem->delete();

        return redirect()->back()->with('success', 'تم حذف البند بنجاح.');
    }
}