<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceItem;
use App\Models\Product;
use App\Models\Supplier; // تأكد من إنشاء موديل المورد
use App\Models\Branche;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseInvoiceController extends Controller
{
    public function index()
    {
        // جلب الفواتير مع المورد والفرع
        $invoices = PurchaseInvoice::with(['supplier', 'branch'])->latest()->paginate(15);
        return view('backend.purchase_invoices.index', compact('invoices'));
    }

    public function create()
    {
        $branches = Branche::all();
        $suppliers = Supplier::where('is_active', true)->get();
        // جلب المنتجات والخدمات
        $products = Product::where('is_active', true)->get();
        
        return view('backend.purchase_invoices.create', compact('branches', 'suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'invoice_date' => 'required|date',
            'items' => 'required|array|min:1',
            'sub_total' => 'required|numeric',
            'discount' => 'required|numeric',
            'tax_amount' => 'required|numeric',
            'grand_total' => 'required|numeric',
            'paid_amount' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // حساب المتبقي وتحديد حالة الدفع
            $due_amount = $request->grand_total - $request->paid_amount;
            $payment_status = $due_amount <= 0 ? 'paid' : ($request->paid_amount > 0 ? 'partial' : 'unpaid');

            // 1. حفظ الفاتورة الأساسية
            $invoice = PurchaseInvoice::create([
                'branch_id' => $request->branch_id,
                'supplier_id' => $request->supplier_id,
                'invoice_number' => 'PI-' . time(), // رقم فاتورة النظام
                'supplier_invoice_number' => $request->supplier_invoice_number, // رقم فاتورة المورد الورقية
                'invoice_date' => $request->invoice_date,
                'sub_total' => $request->sub_total,
                'discount' => $request->discount,
                'tax_amount' => $request->tax_amount,
                'grand_total' => $request->grand_total,
                'paid_amount' => $request->paid_amount,
                'due_amount' => $due_amount,
                'payment_status' => $payment_status,
                'notes' => $request->notes,
            ]);

            // 2. حفظ تفاصيل الفاتورة وتحديث المخزون
            foreach ($request->items as $item) {
                PurchaseInvoiceItem::create([
                    'purchase_invoice_id' => $invoice->id,
                    'product_id' => $item['product_id'],
                    'unit_cost' => $item['unit_cost'],
                    'quantity' => $item['quantity'],
                    'row_total' => $item['row_total'],
                ]);

                // 🔥 سحر المخازن: تحديث الكميات وسعر التكلفة 🔥
                $product = Product::find($item['product_id']);
                if ($product && $product->type === 'product') { // لو منتج ملموس نزود المخزن
                    $product->increment('quantity', $item['quantity']);
                    // تحديث سعر التكلفة ليكون آخر سعر شراء
                    $product->update(['cost_price' => $item['unit_cost']]);
                }
            }

            // 3. تحديث رصيد المورد إذا كان هناك مبلغ آجل (لم يُدفع بالكامل)
            if ($due_amount > 0) {
                $supplier = Supplier::find($request->supplier_id);
                $supplier->increment('current_balance', $due_amount);
            }

            DB::commit();
            return redirect()->route('admin.purchase-invoices.index')->with('success', 'تم حفظ فاتورة المشتريات وتحديث المخزون ورصيد المورد بنجاح.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors(['error' => 'حدث خطأ أثناء حفظ الفاتورة: ' . $e->getMessage()]);
        }
    }
}