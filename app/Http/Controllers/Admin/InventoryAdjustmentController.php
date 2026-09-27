<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryAdjustment;
use App\Models\InventoryAdjustmentItem;
use App\Models\Product;
use App\Models\Branche;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryAdjustmentController extends Controller
{
    // ==========================================
    // 1. عرض قائمة سندات الجرد
    // ==========================================
    public function index(Request $request)
    {
        abort_if(!auth()->user()->can('manage_inventory_adjustments'), 403, 'غير مصرح لك بإدارة تسويات الجرد.');

        $query = InventoryAdjustment::with(['user', 'approver', 'branch'])->latest();

        // فلاتر البحث
        if ($request->filled('search')) {
            $query->where('adjustment_number', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $adjustments = $query->paginate(15)->withQueryString();
        $branches = Branche::where('is_active', 1)->get();

        return view('backend.inventory_adjustments.index', compact('adjustments', 'branches'));
    }

    // ==========================================
    // 2. عرض شاشة عمل جرد جديد
    // ==========================================
    public function create()
    {
        abort_if(!auth()->user()->can('manage_inventory_adjustments'), 403);

        $branches = Branche::where('is_active', 1)->get();
        return view('backend.inventory_adjustments.create', compact('branches'));
    }

    // ==========================================
    // 🔥 3. دالة AJAX لجلب منتجات الفرع المحدد للجرد 🔥
    // ==========================================
    public function getBranchProducts(Request $request)
    {
        $branchId = $request->branch_id;
        $search = $request->q;

        if (!$branchId) return response()->json([]);

        $products = Product::where('branch_id', $branchId)
            ->where('type', 'product')
            ->where('is_active', 1)
            ->when($search, function($q) use ($search) {
                $q->where(function($subQ) use ($search) {
                    $subQ->where('name_ar', 'like', "%{$search}%")
                         ->orWhere('sku', 'like', "%{$search}%");
                });
            })->limit(50)->get();

        $formatted = $products->map(function($prod) {
            return [
                'id'         => $prod->id,
                'text'       => $prod->name_ar . ($prod->sku ? ' | كود: ' . $prod->sku : ''),
                'system_qty' => $prod->quantity // مهم لتحديد العجز والزيادة
            ];
        });

        return response()->json($formatted);
    }

    // ==========================================
    // 4. حفظ الجرد كمسودة (بدون تأثير على المخزن)
    // ==========================================
    public function store(Request $request)
    {
        abort_if(!auth()->user()->can('manage_inventory_adjustments'), 403);

        $request->validate([
            'branch_id'               => 'required|exists:branches,id',
            'adjustment_date'         => 'required|date',
            'items'                   => 'required|array|min:1',
            'items.*.product_id'      => 'required|exists:products,id',
            'items.*.actual_quantity' => 'required|numeric|min:0', // تعديل لدعم الأوزان (numeric)
        ]);

        try {
            DB::beginTransaction();

            $lastAdjustment = InventoryAdjustment::latest('id')->first();
            $nextId = $lastAdjustment ? $lastAdjustment->id + 1 : 1;
            $adjNumber = 'ADJ-' . date('Ym') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

            $adjustment = InventoryAdjustment::create([
                'branch_id'         => $request->branch_id,
                'user_id'           => Auth::id(),
                'adjustment_number' => $adjNumber,
                'adjustment_date'   => $request->adjustment_date,
                'status'            => 'pending', 
                'notes'             => $request->notes,
            ]);

            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                if ($product) {
                    $systemQty = $product->quantity;
                    $actualQty = $item['actual_quantity'];
                    $difference = $actualQty - $systemQty; 

                    // تسجيل المنتجات اللي فيها فروقات فقط لتخفيف الداتا بيز
                    if ($difference != 0) {
                        InventoryAdjustmentItem::create([
                            'inventory_adjustment_id' => $adjustment->id,
                            'product_id'              => $item['product_id'],
                            'system_quantity'         => $systemQty,
                            'actual_quantity'         => $actualQty,
                            'difference'              => $difference,
                            'reason'                  => $item['reason'] ?? null,
                        ]);
                    }
                }
            }

            if ($adjustment->items()->count() == 0) {
                $adjustment->delete();
                return redirect()->back()->withErrors(['error' => 'الكميات الفعلية مطابقة لرصيد النظام تماماً، لم يتم إنشاء سند جرد.']);
            }

            DB::commit();
            return redirect()->route('admin.inventory-adjustments.index')->with('success', 'تم حفظ سند الجرد بنجاح وبانتظار الاعتماد لتحديث المخازن.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors(['error' => 'حدث خطأ: ' . $e->getMessage()]);
        }
    }

    // ==========================================
    // 5. عرض تفاصيل السند
    // ==========================================
    public function show($id)
    {
        abort_if(!auth()->user()->can('manage_inventory_adjustments'), 403);
        $adjustment = InventoryAdjustment::with(['items.product', 'user', 'approver', 'branch'])->findOrFail($id);
        return view('backend.inventory_adjustments.show', compact('adjustment'));
    }

    // ==========================================
    // 6. اعتماد السند وتحديث المخزن فعلياً 🔥
    // ==========================================
    public function approve($id)
    {
        // 🔒 حماية إضافية: صلاحية اعتماد الجرد يجب أن تكون للمديرين فقط
        abort_if(!auth()->user()->can('approve_inventory_adjustments'), 403, 'غير مصرح لك باعتماد تسويات المخازن.');

        $adjustment = InventoryAdjustment::with('items')->findOrFail($id);

        if ($adjustment->status !== 'pending') {
            return redirect()->back()->withErrors(['error' => 'مرفوض أمنياً: هذا السند تم التعامل معه مسبقاً.']);
        }

        try {
            DB::beginTransaction();

            foreach ($adjustment->items as $item) {
                // 🔥 استخدام lockForUpdate لمنع تضارب عمليات البيع (POS) أثناء الجرد 🔥
                $product = Product::where('id', $item->product_id)->lockForUpdate()->first();
                if ($product) {
                    $product->update(['quantity' => $item->actual_quantity]);
                }
            }

            $adjustment->update([
                'status'      => 'approved',
                'approved_by' => Auth::id(),
            ]);

            DB::commit();
            return redirect()->route('admin.inventory-adjustments.index')->with('success', 'تم اعتماد الجرد وتحديث أرصدة المخازن بنجاح.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors(['error' => 'حدث خطأ أثناء الاعتماد: ' . $e->getMessage()]);
        }
    }

    // ==========================================
    // 7. رفض السند (إلغاء الجرد)
    // ==========================================
    public function reject($id)
    {
        abort_if(!auth()->user()->can('approve_inventory_adjustments'), 403);

        $adjustment = InventoryAdjustment::findOrFail($id);

        if ($adjustment->status !== 'pending') {
            return redirect()->back()->withErrors(['error' => 'مرفوض أمنياً: هذا السند تم التعامل معه مسبقاً.']);
        }

        $adjustment->update([
            'status'      => 'rejected',
            'approved_by' => Auth::id(),
        ]);

        return redirect()->route('admin.inventory-adjustments.index')->with('success', 'تم رفض سند الجرد وحفظه في الأرشيف.');
    }
}