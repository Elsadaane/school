<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnItem;
use App\Models\PurchaseInvoice;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseReturnController extends Controller
{
    // ==========================================
    // 1. عرض سجل المرتجعات
    // ==========================================
    public function index()
    {
        // 🔒 حماية الشاشة
        abort_if(!auth()->user()->can('manage_purchases'), 403, 'غير مصرح لك بإدارة مرتجعات المشتريات.');

        $returns = PurchaseReturn::with(['supplier', 'purchaseInvoice'])->latest()->paginate(15);
        return view('backend.purchase_returns.index', compact('returns'));
    }

    // ==========================================
    // 2. شاشة إنشاء مرتجع جديد
    // ==========================================
    public function create()
    {
        abort_if(!auth()->user()->can('manage_purchases'), 403);

        $suppliers = Supplier::where('is_active', true)->get();
        
        // 🔥 التعديل السحري هنا: جلب الفواتير (مع البنود والمنتجات بتاعتها) عشان الـ JS يقرأها 🔥
        $invoices = PurchaseInvoice::with('items.product')->latest()->get(); 
        
        $products = Product::where('is_active', true)->where('type', 'product')->get();

        return view('backend.purchase_returns.create', compact('suppliers', 'invoices', 'products'));
    }

    // ==========================================
    // 3. حفظ المرتجع وتحديث المخازن وحساب المورد
    // ==========================================
    public function store(Request $request)
    {
        abort_if(!auth()->user()->can('manage_purchases'), 403);

        $request->validate([
            'supplier_id'         => 'required|exists:suppliers,id',
            'purchase_invoice_id' => 'required|exists:purchase_invoices,id',
            'return_date'         => 'required|date',
            'items'               => 'required|array|min:1',
            'items.*.product_id'  => 'required|exists:products,id',
            'items.*.quantity'    => 'required|numeric|min:0.1',
            'items.*.unit_cost'   => 'required|numeric|min:0',
            'items.*.row_total'   => 'required|numeric|min:0',
            'total_return_amount' => 'required|numeric|min:0.1',
        ]);

        try {
            DB::beginTransaction();

            $lastReturn = PurchaseReturn::latest('id')->first();
            $nextId = $lastReturn ? $lastReturn->id + 1 : 1;
            $returnNumber = 'PR-' . date('Ym') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

            // 1. إنشاء فاتورة المرتجع (إشعار دائن)
            $purchaseReturn = PurchaseReturn::create([
                'purchase_invoice_id' => $request->purchase_invoice_id,
                'supplier_id'         => $request->supplier_id,
                'return_number'       => $returnNumber,
                'return_date'         => $request->return_date,
                'total_return_amount' => round($request->total_return_amount, 3),
                'notes'               => $request->notes,
            ]);

            // 2. تسجيل البنود وتحديث المخزون
            foreach ($request->items as $item) {
                // 🔥 حماية المخزون من العجز + استخدام Lock لمنع التضارب 🔥
                $product = Product::lockForUpdate()->find($item['product_id']);
                
                if (!$product || $product->quantity < $item['quantity']) {
                    throw new \Exception("عفواً، الرصيد المتاح من المنتج ({$product->name_ar}) لا يكفي لعمل هذا المرتجع. الرصيد الحالي: {$product->quantity}");
                }

                PurchaseReturnItem::create([
                    'purchase_return_id' => $purchaseReturn->id,
                    'product_id'         => $item['product_id'],
                    'returned_quantity'  => $item['quantity'],
                    'unit_cost'          => round($item['unit_cost'], 3),
                    'row_total'          => round($item['row_total'], 3),
                    'is_defective'       => isset($item['is_defective']) ? 1 : 0, 
                    'return_reason'      => $item['return_reason'] ?? null,
                ]);

                // خصم الكمية من المخزن الفعلي
                $product->decrement('quantity', $item['quantity']);
            }

            // 3. خصم قيمة المرتجع من حساب التاجر (تقليل المديونية اللي علينا)
            $supplier = Supplier::lockForUpdate()->find($request->supplier_id);
            if ($supplier) {
                $supplier->decrement('current_balance', round($request->total_return_amount, 3));
            }

            DB::commit();
            return redirect()->route('admin.purchase-returns.index')->with('success', 'تم تسجيل المرتجع بنجاح، وخصم البضاعة من المخزن، وتخفيض مديونية المورد.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors(['error' => 'حدث خطأ أثناء معالجة المرتجع: ' . $e->getMessage()]);
        }
    }

    // ==========================================
    // 4. عرض تفاصيل وإيصال المرتجع (للطباعة)
    // ==========================================
    public function show($id)
    {
        abort_if(!auth()->user()->can('manage_purchases'), 403);

        $purchaseReturn = PurchaseReturn::with([
            'supplier', 
            'purchaseInvoice', 
            'items.product'
        ])->findOrFail($id);

        return view('backend.purchase_returns.show', compact('purchaseReturn'));
    }
}