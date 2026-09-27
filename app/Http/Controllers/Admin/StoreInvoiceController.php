<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StoreInvoice;
use App\Models\StoreInvoiceItem;
use App\Models\Product;
use App\Models\Student;
use App\Models\Payment;
use App\Models\FinancialAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StoreInvoiceController extends Controller
{
    // ==========================================
    // 🔥 1. عرض شاشة المبيعات (POS) 🔥
    // ==========================================
    public function create()
    {
        // 🔒 حماية الشاشة
        abort_if(!auth()->user()->can('manage_pos'), 403, 'غير مصرح لك بفتح نقطة البيع.');

        // 🌟 جلب فرع الكاشير الحالي لضمان عزل البيانات بين الفروع 🌟
        $userBranchId = auth()->user()->employee->branch_id ?? 1;

        $students = Student::where('status', 'active')->where('branch_id', $userBranchId)->get();
        $products = Product::where('branch_id', $userBranchId)
                           ->where('is_active', 1)
                           ->where('quantity', '>', 0)
                           ->get(); 
        
        $accounts = FinancialAccount::where('is_active', 1)->get(); // لاختيار وسيلة الدفع (خزنة / بنك)

        return view('backend.store_invoices.create', compact('students', 'products', 'accounts', 'userBranchId'));
    }

    // ==========================================
    // 🔥 2. إنشاء الفاتورة وخصم المخزون وتوريد النقدية 🔥
    // ==========================================
    public function store(Request $request)
    {
        abort_if(!auth()->user()->can('manage_pos'), 403);

        $request->validate([
            'student_id'           => 'required|exists:students,id',
            'financial_account_id' => 'required|exists:financial_accounts,id', // تحديد الخزنة التي ستستلم المبلغ
            'payment_method'       => 'required|in:cash,card',
            'sub_total'            => 'required|numeric|min:0',
            'discount'             => 'required|numeric|min:0',
            'grand_total'          => 'required|numeric|min:0.1',
            'items'                => 'required|array|min:1',
            'items.*.product_id'   => 'required|exists:products,id',
            'items.*.quantity'     => 'required|integer|min:1',
            'items.*.unit_price'   => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $student = Student::findOrFail($request->student_id);
            $userBranchId = auth()->user()->employee->branch_id ?? 1;

            $lastInvoice = StoreInvoice::latest('id')->first();
            $nextId = $lastInvoice ? $lastInvoice->id + 1 : 1;
            $invoiceNumber = 'INV-POS-' . date('Ym') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

            // 1. إنشاء الفاتورة
            $invoice = StoreInvoice::create([
                'invoice_number' => $invoiceNumber,
                'branch_id'      => $userBranchId,
                'student_id'     => $student->id,
                'invoice_date'   => now(),
                'sub_total'      => round($request->sub_total, 3),
                'discount'       => round($request->discount, 3),
                'tax_amount'     => 0, // يمكنك تفعيل الضريبة هنا لاحقاً
                'grand_total'    => round($request->grand_total, 3),
                'payment_method' => $request->payment_method,
                'created_by'     => auth()->id(),
            ]);

            // 2. خصم المنتجات من المخزن وتسجيل البنود
            foreach ($request->items as $item) {
                // استخدام lockForUpdate لمنع بيع نفس القطعة مرتين في نفس اللحظة
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);

                if ($product->quantity < $item['quantity']) {
                    throw new \Exception("الكمية المطلوبة من الصنف ({$product->name_ar}) غير متوفرة. الرصيد الحالي: {$product->quantity}");
                }

                $rowTotal = $item['quantity'] * $item['unit_price'];

                StoreInvoiceItem::create([
                    'store_invoice_id' => $invoice->id,
                    'product_id'       => $product->id,
                    'quantity'         => $item['quantity'],
                    'unit_price'       => round($item['unit_price'], 3),
                    'row_total'        => round($rowTotal, 3),
                ]);

                $product->decrement('quantity', $item['quantity']);
                $product->increment('sold_quantity', $item['quantity']);
            }

            // 3. توريد النقدية إلى الخزنة المركزية 
            $account = FinancialAccount::lockForUpdate()->findOrFail($request->financial_account_id);
            $account->increment('balance', $invoice->grand_total); // 🔥 تم إصلاح عدم زيادة الخزنة 🔥

            Payment::create([
                'financial_account_id' => $account->id, 
                'receipt_number'       => 'REC-POS-' . strtoupper(uniqid()),
                'user_id'              => auth()->id() ?? 1,
                'payment_type'         => 'store_invoice',
                'payable_type'         => Student::class,
                'payable_id'           => $student->id,
                'amount'               => $invoice->grand_total, 
                'payment_date'         => now()->toDateString(),
                'notes'                => 'تم الدفع فورياً عند إصدار الفاتورة عبر الـ POS',
                // تحويل الـ Array إلى نص لحفظه بأمان في قاعدة البيانات
                'transaction_details'  => "مبيعات متجر مدرسي لفاتورة رقم: {$invoice->invoice_number} - طريقة الدفع: {$request->payment_method}",
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'تم إصدار الفاتورة وتوريد المبلغ للحساب المالي بنجاح.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors(['error' => 'حدث خطأ أثناء إتمام عملية البيع: ' . $e->getMessage()]);
        }
    }
}