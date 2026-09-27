<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceItem;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Branche;
use App\Models\Payment;
use App\Models\FinancialAccount;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class PurchaseInvoiceController extends Controller
{
    // ==========================================
    // 1. عرض سجل الفواتير (مع جلب المرتجعات)
    // ==========================================
    public function index()
    {
        abort_if(!auth()->user()->can('manage_purchases'), 403, 'غير مصرح لك بإدارة المشتريات.');

        // 🔥 التعديل: جلب علاقة الـ returns عشان البليد يقدر يعرف الفاتورة دي اترجع منها إيه 🔥
        $invoices = PurchaseInvoice::with(['branch', 'supplier', 'returns'])->latest()->paginate(15);
        $accounts = FinancialAccount::where('is_active', true)->get();
        
        return view('backend.purchase_invoices.index', compact('invoices', 'accounts'));
    }

    // ==========================================
    // 2. شاشة إنشاء فاتورة جديدة
    // ==========================================
    public function create(Request $request)
    {
        abort_if(!auth()->user()->can('manage_purchases'), 403);
        $branches = Branche::where('is_active', 1)->get();
        $suppliers = Supplier::where('is_active', true)->get();
        $products = Product::where('type', 'product')->get(); 
        $selectedBranch = $request->branch_id ?? null;
        
        // 🔥 السطر ده اللي ضفناه عشان نجيب الخزائن 🔥
        $accounts = FinancialAccount::where('is_active', true)->get(); 

        return view('backend.purchase_invoices.create', compact('branches', 'suppliers', 'products', 'selectedBranch', 'accounts'));
    }

    // ==========================================
    // 3. حفظ الفاتورة وتوليد الأقساط أو الكاش
    // ==========================================
    public function store(Request $request)
    {
        abort_if(!auth()->user()->can('manage_purchases'), 403);

        $request->validate([
            'branch_id'            => 'required|exists:branches,id',
            'supplier_id'          => 'required|exists:suppliers,id',
            'invoice_date'         => 'required|date',
            'items'                => 'required|array|min:1',
            'grand_total'          => 'required|numeric|min:0.1',
            'paid_amount'          => 'required|numeric|min:0',
            'payment_type'         => 'required|in:cash,auto_installment,manual_installment',
        ]);

        // 🔥 تأمين أمني: لو الدفع كاش أو فيه مدفوع مقدم، الخزنة إجبارية 100% 🔥
        if ($request->payment_type === 'cash' || $request->paid_amount > 0) {
            $request->validate(['financial_account_id' => 'required|exists:financial_accounts,id']);
        }

        try {
            DB::beginTransaction();

            // 🎯 حل معضلة الكاش: لو الدفع كاش، المدفوع غصب عن أي حاجة بيساوي الإجمالي كامل 🎯
            $paidAmount = $request->payment_type === 'cash' ? $request->grand_total : $request->paid_amount;
            $dueAmount = $request->grand_total - $paidAmount;
            
            // تحديد حالة السداد بدقة بالغة
            $paymentStatus = $dueAmount <= 0 ? 'paid' : ($paidAmount > 0 ? 'partial' : 'unpaid');
            
            $lastInvoice = PurchaseInvoice::latest('id')->first();
            $nextId = $lastInvoice ? $lastInvoice->id + 1 : 1;
            $invoiceNumber = 'PINV-' . date('Ym') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

            // إنشاء الفاتورة بالقيم المحمية بالسيرفر
            $invoice = PurchaseInvoice::create([
                'branch_id'               => $request->branch_id,
                'supplier_id'             => $request->supplier_id,
                'invoice_number'          => $invoiceNumber,
                'supplier_invoice_number' => $request->supplier_invoice_number,
                'invoice_date'            => $request->invoice_date,
                'sub_total'               => $request->sub_total ?? 0,
                'discount'                => $request->discount ?? 0,
                'tax_amount'              => $request->tax_amount ?? 0,
                'grand_total'             => $request->grand_total,
                'paid_amount'             => $paidAmount,
                'due_amount'              => $dueAmount,
                'payment_status'          => $paymentStatus,
                'notes'                   => $request->notes,
                'payment_type'            => $request->payment_type, 
            ]);

            // إدخال البضاعة للمخزن
            foreach ($request->items as $item) {
                PurchaseInvoiceItem::create([
                    'purchase_invoice_id' => $invoice->id,
                    'product_id'          => $item['product_id'],
                    'quantity'            => $item['quantity'],
                    'unit_cost'           => $item['unit_cost'],
                    'row_total'           => $item['quantity'] * $item['unit_cost'],
                ]);
                Product::lockForUpdate()->find($item['product_id'])->increment('quantity', $item['quantity']);
            }

            $supplier = Supplier::lockForUpdate()->find($request->supplier_id);

            // تسجيل الدفعة النقدية وخصمها من الخزنة المحددة
            if ($paidAmount > 0) {
                $account = FinancialAccount::lockForUpdate()->findOrFail($request->financial_account_id);
                
                // حماية الخزنة من العجز المالي
                if ($account->balance < $paidAmount) {
                    throw new \Exception("رصيد الحساب المالي المختار لا يكفي لسداد هذه الفاتورة. المتاح حالياً: " . number_format($account->balance, 3));
                }

                $account->decrement('balance', $paidAmount);

                Payment::create([
                    'financial_account_id' => $request->financial_account_id,
                    'receipt_number'       => 'PAY-SUP-' . strtoupper(uniqid()),
                    'user_id'              => auth()->id() ?? 1,
                    'payment_type'         => 'purchase_payment', // موحد مع كشف الحساب الجديد
                    'payable_type'         => Supplier::class,
                    'payable_id'           => $supplier->id,
                    'amount'               => -$paidAmount, // صادر بالسالب
                    'payment_date'         => $request->invoice_date,
                    'transaction_details'  => "سداد نقدي بالكامل لفاتورة مشتريات رقم: {$invoice->invoice_number}",
                ]);
            }

            // توليد الأقساط فقط لو الفاتورة آجل وفيها متبقي
            if ($dueAmount > 0) {
                if ($request->payment_type === 'auto_installment') {
                    $months = $request->months_count;
                    $startDate = \Carbon\Carbon::parse($request->start_date);
                    $installmentAmount = $dueAmount / $months;

                    for ($i = 0; $i < $months; $i++) {
                        DB::table('purchase_installments')->insert([
                            'purchase_invoice_id' => $invoice->id,
                            'supplier_id'         => $supplier->id,
                            'amount'               => $installmentAmount,
                            'due_date'            => $startDate->copy()->addMonths($i),
                            'created_at'          => now(),
                            'updated_at'          => now(),
                        ]);
                    }
                } elseif ($request->payment_type === 'manual_installment') {
                    foreach ($request->manual_installments as $inst) {
                        DB::table('purchase_installments')->insert([
                            'purchase_invoice_id' => $invoice->id,
                            'supplier_id'         => $supplier->id,
                            'amount'               => $inst['amount'],
                            'due_date'            => $inst['date'],
                            'created_at'          => now(),
                            'updated_at'          => now(),
                        ]);
                    }
                }
                
                // تحديث مديونية المورد بالآجل المتبقي فقط
                $supplier->increment('current_balance', $dueAmount);
            }

            DB::commit();
            return redirect()->route('admin.purchase-invoices.index')->with('success', 'تم حفظ الفاتورة بنجاح وتحديث أرصدة المخازن والخزائن والموردين.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors(['error' => 'حدث خطأ: ' . $e->getMessage()]);
        }
    }

    // ==========================================
    // 4. عرض تفاصيل الفاتورة للطباعة
    // ==========================================
    public function show($id)
    {
        abort_if(!auth()->user()->can('manage_purchases'), 403);
        $invoice = PurchaseInvoice::with(['items.product', 'supplier', 'branch', 'returns'])->findOrFail($id);
        return view('backend.purchase_invoices.show', compact('invoice'));
    }

    // ==========================================
    // 5. حذف الفاتورة واسترجاع المخزون (محمية)
    // ==========================================
    public function destroy($id)
    {
        abort_if(!auth()->user()->can('manage_purchases'), 403);

        try {
            DB::beginTransaction();
            $invoice = PurchaseInvoice::with(['items', 'returns'])->findOrFail($id);

            // 🔥 حماية محاسبية صارمة: منع حذف فاتورة اتعمل عليها مرتجع أو اندفع فيها كاش 🔥
            if ($invoice->returns()->exists() || $invoice->paid_amount > 0) {
                throw new \Exception('مرفوض محاسبياً: لا يمكن حذف الفاتورة لوجود مرتجعات أو دفعات مالية مسجلة عليها. يرجى مراجعة الحركات المالية.');
            }

            // تنقيص المخزون اللي زاد
            foreach ($invoice->items as $item) {
                $product = Product::lockForUpdate()->find($item->product_id);
                if ($product) {
                    $product->decrement('quantity', $item->quantity);
                }
            }

            // تنقيص مديونية المورد
            $supplier = Supplier::find($invoice->supplier_id);
            if($supplier) {
                $supplier->decrement('current_balance', $invoice->due_amount);
            }

            // مسح الأقساط المرتبطة
            DB::table('purchase_installments')->where('purchase_invoice_id', $invoice->id)->delete();

            $invoice->delete();

            DB::commit();
            return redirect()->route('admin.purchase-invoices.index')->with('success', 'تم حذف الفاتورة واسترجاع كميات المخزون بنجاح.');
            
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors(['error' => 'حدث خطأ أثناء الحذف: ' . $e->getMessage()]);
        }
    }

    // ==========================================
    // 6. سداد جزء من الفاتورة من شاشة الفواتير
    // ==========================================
    public function payInvoice(Request $request, $id)
    {
        abort_if(!auth()->user()->can('manage_purchases'), 403);

        $invoice = PurchaseInvoice::findOrFail($id);

        $request->validate([
            'amount'               => 'required|numeric|min:0.1|max:' . $invoice->due_amount,
            'payment_date'         => 'required|date',
            'financial_account_id' => 'required|exists:financial_accounts,id' 
        ]);

        try {
            DB::beginTransaction();

            $account = FinancialAccount::lockForUpdate()->findOrFail($request->financial_account_id);
            
            if ($account->balance < $request->amount) {
                throw new \Exception("رصيد الخزينة المتاح (" . number_format($account->balance, 3) . ") لا يكفي لدفع هذا المبلغ.");
            }

            $account->decrement('balance', $request->amount);

            Payment::create([
                'financial_account_id' => $request->financial_account_id,
                'receipt_number'       => 'PAY-SUP-' . strtoupper(uniqid()),
                'user_id'              => auth()->id() ?? 1,
                'payment_type'         => 'purchase_payment',
                'payable_type'         => Supplier::class,
                'payable_id'           => $invoice->supplier_id,
                'amount'               => -$request->amount,
                'payment_date'         => $request->payment_date,
                'transaction_details'  => "سداد دفعة من فاتورة مشتريات رقم: {$invoice->invoice_number}",
            ]);

            $invoice->paid_amount += $request->amount;
            $invoice->due_amount -= $request->amount;
            $invoice->payment_status = $invoice->due_amount <= 0 ? 'paid' : 'partial';
            $invoice->save();

            $invoice->supplier->decrement('current_balance', $request->amount);

            DB::commit();
            return redirect()->back()->with('success', 'تم تسجيل الدفعة وتحديث رصيد الخزنة والمورد بنجاح.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors(['error' => 'حدث خطأ: ' . $e->getMessage()]);
        }
    }
}