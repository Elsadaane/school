<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssetSubCategory;
use App\Models\AssetCategory;
use Illuminate\Http\Request;

class AssetSubCategoryController extends Controller
{
    // حفظ قسم فرعي جديد
    public function store(Request $request)
    {
        $request->validate([
            'asset_category_id' => 'required|exists:asset_categories,id',
            'name_ar'           => 'required|string|max:255',
            'name_en'           => 'nullable|string|max:255',
        ], [
            'asset_category_id.required' => 'يرجى اختيار القسم الرئيسي.',
            'name_ar.required'           => 'اسم القسم الفرعي بالعربي مطلوب.',
        ]);

        AssetSubCategory::create([
            'asset_category_id' => $request->asset_category_id,
            'name_ar'           => $request->name_ar,
            'name_en'           => $request->name_en,
        ]);

        return redirect()->back()->with('success', 'تم إضافة القسم الفرعي بنجاح!');
    }

    // حذف قسم فرعي
    public function destroy($id)
    {
        try {
            $subCategory = AssetSubCategory::findOrFail($id);
            
            // التحقق من عدم وجود أصول مرتبطة
            if ($subCategory->assets()->count() > 0) {
                return redirect()->back()->with('error', 'لا يمكن حذف هذا القسم لاحتوائه على أصول!');
            }
            
            $subCategory->delete();
            return redirect()->back()->with('success', 'تم حذف القسم الفرعي بنجاح!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء الحذف: ' . $e->getMessage());
        }
    }
}