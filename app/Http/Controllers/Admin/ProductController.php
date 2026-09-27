<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Branche;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    // ==========================================
    // 1. عرض قائمة المنتجات والرسوم
    // ==========================================
    public function index(Request $request)
    {
        // 🔒 حماية الشاشة
        abort_if(!auth()->user()->can('manage_products'), 403, 'غير مصرح لك بإدارة المنتجات.');

        $branches = Branche::where('is_active', 1)->get();
        $query = Product::with('branch')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name_ar', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        $products = $query->paginate(15)->withQueryString();
        
        return view('backend.products.index', compact('products', 'branches'));
    }

    // ==========================================
    // 2. عرض شاشة إضافة منتج/رسم جديد
    // ==========================================
    public function create()
    {
        abort_if(!auth()->user()->can('manage_products'), 403);
        $branches = Branche::where('is_active', 1)->get();
        return view('backend.products.create', compact('branches'));
    }

    // ==========================================
    // 3. حفظ بيانات المنتج/الرسم الجديد
    // ==========================================
    public function store(Request $request)
    {
        abort_if(!auth()->user()->can('manage_products'), 403);

        $request->validate([
            'branch_id'      => 'required|array|min:1', 
            'branch_id.*'    => 'exists:branches,id',
            'name_ar'        => 'required|string|max:255',
            'name_en'        => 'nullable|string|max:255',
            'type'           => 'required|in:product,service',
            'cost_price'     => 'required|numeric|min:0',
            'selling_price'  => 'required|numeric|min:0',
            'sku'            => 'nullable|string', 
            // 🔥 تم إضافة التحقق من الحقول المنسية 🔥
            'tax_percent'    => 'nullable|numeric|min:0|max:100',
            'quantity'       => 'nullable|numeric|min:0',
            'alert_quantity' => 'nullable|numeric|min:0',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
        ]);

        $sku = $request->filled('sku') ? trim($request->sku) : 'ITEM-' . strtoupper(substr(uniqid(), -6));
        $addedCount = 0;

        DB::beginTransaction();
        try {
            foreach ($request->branch_id as $branchId) {
                
                // الفحص داخل الفرع الحالي فقط
                if ($request->filled('sku')) {
                    if (Product::withTrashed()->where('branch_id', $branchId)->where('sku', $sku)->exists()) {
                        DB::rollBack();
                        return redirect()->back()->withErrors(['sku' => "عفواً، كود الصنف (SKU) [{$sku}] مستخدم بالفعل في أحد الفروع المحددة! يرجى تغييره."])->withInput();
                    }
                } else {
                    while (Product::withTrashed()->where('branch_id', $branchId)->where('sku', $sku)->exists()) {
                        $sku = 'ITEM-' . strtoupper(substr(uniqid(), -6));
                    }
                }

                Product::create([
                    'branch_id'      => $branchId,
                    'name_ar'        => $request->name_ar,
                    'name_en'        => $request->name_en,
                    'sku'            => $sku,
                    'type'           => $request->type, 
                    'cost_price'     => round($request->cost_price, 3), // 🔥 تقريب للعملة 🔥
                    'selling_price'  => round($request->selling_price, 3),
                    'tax_percent'    => $request->tax_percent ?? 0,
                    'quantity'       => $request->type === 'product' ? ($request->quantity ?? 0) : 0,
                    'alert_quantity' => $request->type === 'product' ? ($request->alert_quantity ?? 5) : 0,
                    'description_ar' => $request->description_ar,
                    'description_en' => $request->description_en,
                    'is_active'      => $request->has('is_active') ? 1 : 0,
                ]);

                $addedCount++;
            }
            
            DB::commit();
            return redirect()->route('admin.products.index')->with('success', "تمت إضافة البند بنجاح لعدد ({$addedCount}) فرع.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'حدث خطأ: ' . $e->getMessage()])->withInput();
        }
    }

    // ==========================================
    // 4. تحديث بيانات المنتج/الرسم
    // ==========================================
    public function update(Request $request, $id)
    {
        abort_if(!auth()->user()->can('manage_products'), 403);

        $request->validate([
            'branch_id'      => 'required|exists:branches,id', 
            'name_ar'        => 'required|string|max:255',
            'name_en'        => 'nullable|string|max:255',
            'cost_price'     => 'required|numeric|min:0',
            'selling_price'  => 'required|numeric|min:0',
            'sku'            => 'nullable|string',
            // 🔥 إضافة التحقق للكميات في التعديل 🔥
            'quantity'       => 'nullable|numeric|min:0',
            'alert_quantity' => 'nullable|numeric|min:0',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
        ]);

        $product = Product::findOrFail($id);
        
        $data = $request->only([
            'branch_id', 'name_ar', 'name_en', 'cost_price', 'selling_price', 'description_ar', 'description_en'
        ]);

        // تقريب الأسعار
        $data['cost_price'] = round($request->cost_price, 3);
        $data['selling_price'] = round($request->selling_price, 3);

        if ($request->filled('sku') && trim($request->sku) !== $product->sku) {
            $newSku = trim($request->sku);
            if (Product::withTrashed()->where('branch_id', $product->branch_id)->where('sku', $newSku)->exists()) {
                return redirect()->back()->withErrors(['sku' => "كود الصنف (SKU) [{$newSku}] مستخدم مسبقاً في هذا الفرع. يرجى اختيار كود آخر."])->withInput();
            }
            $data['sku'] = $newSku;
        }

        if ($product->type === 'product') {
            $data['quantity'] = $request->quantity ?? 0;
            $data['alert_quantity'] = $request->alert_quantity ?? 5;
        }

        $data['is_active'] = $request->has('is_active') ? 1 : 0;
        $product->update($data);

        return redirect()->route('admin.products.index')->with('success', 'تم تعديل بيانات البند بنجاح.');
    }

    // ==========================================
    // 5. الحذف
    // ==========================================
    public function destroy($id)
    {
        abort_if(!auth()->user()->can('manage_products'), 403);

        $product = Product::findOrFail($id);
        
        if ($product->sold_quantity > 0) {
            return redirect()->back()->withErrors(['error' => 'لا يمكن حذف هذا البند لوجود حركات مبيعات عليه! يمكنك إيقافه من التعديل بدلاً من الحذف.']);
        }

        $product->delete();
        return redirect()->back()->with('success', 'تم حذف البند بنجاح.');
    }

    // ==========================================
    // 6. استنساخ البند لفروع أخرى (Clone / Duplicate)
    // ==========================================
    public function duplicate(Request $request, $id)
    {
        abort_if(!auth()->user()->can('manage_products'), 403);

        $request->validate([
            'branch_id'   => 'required|array|min:1',
            'branch_id.*' => 'exists:branches,id',
        ]);

        $originalProduct = Product::findOrFail($id);
        $addedCount = 0;

        DB::beginTransaction();
        try {
            foreach ($request->branch_id as $branchId) {
                
                $sku = $originalProduct->sku;
                $counter = 1;
                $originalSku = $sku;
                
                while (Product::withTrashed()->where('branch_id', $branchId)->where('sku', $sku)->exists()) {
                    $sku = $originalSku . '-C' . $counter;
                    $counter++;
                }

                Product::create([
                    'branch_id'      => $branchId,
                    'name_ar'        => $originalProduct->name_ar,
                    'name_en'        => $originalProduct->name_en,
                    'sku'            => $sku,
                    'type'           => $originalProduct->type, 
                    'cost_price'     => $originalProduct->cost_price,
                    'selling_price'  => $originalProduct->selling_price,
                    'tax_percent'    => $originalProduct->tax_percent,
                    'quantity'       => 0, // الرصيد صفر في الفرع الجديد
                    'alert_quantity' => $originalProduct->alert_quantity,
                    'description_ar' => $originalProduct->description_ar,
                    'description_en' => $originalProduct->description_en,
                    'is_active'      => $originalProduct->is_active,
                ]);

                $addedCount++;
            }

            DB::commit();
            return redirect()->route('admin.products.index')->with('success', "تم استنساخ البند بنجاح لعدد ({$addedCount}) فرع.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'حدث خطأ أثناء الاستنساخ: ' . $e->getMessage()]);
        }
    }

    // ==========================================
    // 7. رادار النواقص (Low Stock Alerts)
    // ==========================================
    public function lowStock(Request $request)
    {
        abort_if(!auth()->user()->can('manage_products'), 403);

        $branches = Branche::where('is_active', 1)->get();
        $query = Product::with('branch')
            ->where('type', 'product')
            ->whereColumn('quantity', '<=', 'alert_quantity');

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        $products = $query->orderBy('quantity', 'asc')->paginate(15)->withQueryString();
        return view('backend.products.low_stock', compact('products', 'branches'));
    }
}