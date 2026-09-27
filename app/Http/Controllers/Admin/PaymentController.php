<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Guardian;
use App\Models\FinancialAccount;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    // ==========================================
    // 🔥 عرض قائمة السندات المالية 🔥
    // ==========================================
    public function index()
    {
        // 🔒 حماية الشاشة
        abort_if(!auth()->user()->can('manage_payments'), 403, 'غير مصرح لك بإدارة السندات المالية.');

        // 🌟 استخدام paginate بدل get لحماية السيرفر 🌟
        $payments = Payment::with(['payable', 'user', 'financialAccount'])->latest()->paginate(20);
        
        $guardians = Guardian::all(); 
        $suppliers = Supplier::where('is_active', true)->get();
        $financial_accounts = FinancialAccount::where('is_active', 1)->get(); 

        return view('backend.payments.index', compact('payments', 'guardians', 'suppliers', 'financial_accounts'));
    }

    // ==========================================
    // 🔥 إصدار سند (قبض / صرف) 🔥
    // ==========================================
    public function store(Request $request)
    {
        abort_if(!auth()->user()->can('manage_payments'), 403);

        $request->validate([
            'voucher_type'         => 'required|in:receipt,payment',
            'payable_id'           => 'required|integer',
            'financial_account_id' => 'required|exists:financial_accounts,id',
            'amount'               => 'required|numeric|min:0.001',
            'payment_date'         => 'required|date',
            'transaction_details'  => 'required|string',
            'check_number'         => 'nullable|string',
            'notes'                => 'nullable|string',
        ]);

        if ($request->voucher_type === 'receipt') {
            if (!Guardian::where('id', $request->payable_id)->exists()) {
                return redirect()->back()->withErrors(['error' => 'ولي الأمر المختار غير صحيح.']);
            }
            $payableType = Guardian::class;
            $paymentType = 'installment'; 
            $receiptPrefix = 'REC-';
        } else {
            if (!Supplier::where('id', $request->payable_id)->exists()) {
                return redirect()->back()->withErrors(['error' => 'المورد المختار غير صحيح.']);
            }
            $payableType = Supplier::class;
            $paymentType = 'advance'; 
            $receiptPrefix = 'PAY-';
        }

        try {
            DB::transaction(function () use ($request, $payableType, $paymentType, $receiptPrefix) {
                
                $account = FinancialAccount::findOrFail($request->financial_account_id);

                // 🔥 الحماية ضد عجز الخزنة عند صرف الدفعات 🔥
                if ($request->voucher_type === 'payment' && $account->balance < $request->amount) {
                    throw new \Exception('عفواً، رصيد الحساب المالي المتاح (' . number_format($account->balance, 3) . ') لا يكفي لصرف هذا السند.');
                }

                $lastPayment = Payment::latest('id')->first();
                $nextId = $lastPayment ? $lastPayment->id + 1 : 1;
                $receiptNumber = $receiptPrefix . date('Ymd') . str_pad($nextId, 4, '0', STR_PAD_LEFT);

                Payment::create([
                    'receipt_number'       => $receiptNumber,
                    'payment_type'         => $paymentType,
                    'payable_type'         => $payableType, 
                    'payable_id'           => $request->payable_id, 
                    'financial_account_id' => $request->financial_account_id,
                    'user_id'              => Auth::id() ?? 1,
                    'payment_date'         => $request->payment_date,
                    'amount'               => $request->amount,
                    'check_number'         => $request->check_number,
                    'transaction_details'  => $request->transaction_details,
                    'notes'                => $request->notes,
                ]);

                if ($request->voucher_type === 'receipt') {
                    $account->increment('balance', $request->amount); 
                } else {
                    $account->decrement('balance', $request->amount); 
                    
                    $supplier = Supplier::find($request->payable_id);
                    if ($supplier) {
                        $supplier->decrement('current_balance', $request->amount);
                    }
                }
            });

            return redirect()->back()->with('success', 'تم إصدار السند المالي وتحديث الأرصدة بنجاح!');

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    // ==========================================
    // 🔥 طباعة السند 🔥
    // ==========================================
    public function print($id)
    {
        abort_if(!auth()->user()->can('manage_payments'), 403);

        $payment = Payment::with(['payable', 'financialAccount', 'user'])->findOrFail($id);
        return view('backend.payments.print', compact('payment'));
    }

    // ==========================================
    // 🔥 تعديل السند المالي (المعالجة المحاسبية) 🔥
    // ==========================================
    public function update(Request $request, $id)
    {
        abort_if(!auth()->user()->can('manage_payments'), 403);

        $request->validate([
            'amount'               => 'required|numeric|min:0.001',
            'payment_date'         => 'required|date',
            'financial_account_id' => 'required|exists:financial_accounts,id',
            'transaction_details'  => 'required|string',
        ]);

        try {
            DB::transaction(function () use ($request, $id) {
                $payment = Payment::lockForUpdate()->findOrFail($id);
                $oldAccount = FinancialAccount::lockForUpdate()->findOrFail($payment->financial_account_id);
                $newAccount = FinancialAccount::lockForUpdate()->findOrFail($request->financial_account_id);

                // 1. إرجاع التأثير القديم
                if ($payment->payable_type === Supplier::class) {
                    $oldAccount->increment('balance', $payment->amount);
                    Supplier::where('id', $payment->payable_id)->increment('current_balance', $payment->amount); // 🔥 استرداد مديونية المورد القديمة
                } else {
                    $oldAccount->decrement('balance', $payment->amount);
                }

                // 2. تطبيق التأثير الجديد
                if ($payment->payable_type === Supplier::class) {
                    // حماية عجز الخزنة الجديد
                    if ($newAccount->balance < $request->amount) {
                        throw new \Exception('رصيد الحساب المالي الجديد لا يكفي لتغطية مبلغ الصرف المعدل.');
                    }
                    $newAccount->decrement('balance', $request->amount);
                    Supplier::where('id', $payment->payable_id)->decrement('current_balance', $request->amount); // 🔥 تطبيق مديونية المورد الجديدة
                } else {
                    $newAccount->increment('balance', $request->amount);
                }

                // 3. تحديث بيانات السند
                $payment->update([
                    'amount'               => $request->amount,
                    'payment_date'         => $request->payment_date,
                    'financial_account_id' => $request->financial_account_id,
                    'check_number'         => $request->check_number,
                    'transaction_details'  => $request->transaction_details,
                    'notes'                => $request->notes,
                ]);
            });

            return redirect()->back()->with('success', 'تم تعديل السند المالي وتحديث أرصدة الحسابات والموردين بنجاح!');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    // ==========================================
    // 🔥 حذف السند المالي الذكي 🔥
    // ==========================================
    public function destroy($id)
    {
        abort_if(!auth()->user()->can('manage_payments'), 403);

        try {
            DB::transaction(function () use ($id) {
                $payment = Payment::lockForUpdate()->findOrFail($id);

                if (str_contains($payment->transaction_details, 'فاتورة')) {
                    throw new \Exception('عفواً! لا يمكن حذف هذا السند لأنه مرتبط بفاتورة، يمكنك عمل تسوية أو إلغاء الفاتورة نفسها.');
                }

                $account = FinancialAccount::lockForUpdate()->findOrFail($payment->financial_account_id);

                if ($payment->payable_type === Supplier::class) {
                    $account->increment('balance', $payment->amount); 
                    Supplier::where('id', $payment->payable_id)->increment('current_balance', $payment->amount); // 🔥 إرجاع مديونية المورد
                } else {
                    // لو هنلغي سند قبض، معناه هنسحب فلوس من الخزنة.. هل الرصيد يكفي؟
                    if ($account->balance < $payment->amount) {
                        throw new \Exception('لا يمكن حذف سند القبض لأن رصيد الخزينة الحالي أقل من قيمة السند المُراد استرداده.');
                    }
                    $account->decrement('balance', $payment->amount); 
                }

                $payment->delete();
            });

            return redirect()->back()->with('success', 'تم حذف السند وتحديث كشوف الحسابات بنجاح.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}