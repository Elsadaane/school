<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudentInstallment;
use App\Models\AssetInstallment;
use App\Models\PurchaseInstallment;
use App\Models\Payment;
use App\Models\FinancialAccount;
use App\Models\JournalEntry;
use App\Models\JournalItem;
use App\Models\Asset;
use App\Models\PurchaseInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InstallmentController extends Controller
{
    // ==========================================
    // 1. عرض قائمة كل الأقساط (طلاب / أصول / مشتريات)
    // ==========================================
    public function index(Request $request)
    {
        abort_if(!auth()->user()->can('manage_installments'), 403, 'غير مصرح لك بإدارة الأقساط والمدفوعات.');

        $search = $request->search;

        // 🟢 أ. أقساط الطلاب (تعتمد على حقل status و الفرز للأقساط غير الخالصة أولاً)
        $studentQuery = StudentInstallment::with(['student.parent', 'contract']);
        if ($search) {
            $studentQuery->whereHas('student', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('tribe', 'like', "%{$search}%");
            })->orWhereHas('contract', function($q) use ($search) {
                $q->where('contract_number', 'like', "%{$search}%");
            });
        }
        // 🔥 سحر الترتيب: يجيب الـ unpaid أولاً، ثم partial، ثم paid في الآخر 🔥
        $studentInstallments = $studentQuery->orderByRaw("FIELD(status, 'unpaid', 'partial', 'paid')")
            ->orderBy('due_date', 'asc')
            ->paginate(10, ['*'], 'students_page')
            ->appends($request->all());


        // 🔵 ب. أقساط الأصول الثابتة (تعتمد على حقل is_paid)
        $assetQuery = AssetInstallment::with(['asset.branch']);
        if ($search) {
            $assetQuery->whereHas('asset', function ($q) use ($search) {
                $q->where('asset_code', 'like', "%{$search}%")
                  ->orWhere('transaction_details', 'like', "%{$search}%");
            });
        }
        // 🔥 ترتيب: الأصول غير المدفوعة أولاً
        $assetInstallments = $assetQuery->orderBy('is_paid', 'asc')
            ->orderBy('due_date', 'asc')
            ->paginate(10, ['*'], 'assets_page')
            ->appends($request->all());


        // 🟡 ج. أقساط فواتير المشتريات والموردين (تعتمد على حقل is_paid)
        $purchaseQuery = PurchaseInstallment::with(['invoice', 'supplier']);
        if ($search) {
            $purchaseQuery->whereHas('supplier', function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%");
            })->orWhereHas('invoice', function($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%");
            });
        }
        // 🔥 ترتيب: أقساط الموردين غير المدفوعة أولاً
        $purchaseInstallments = $purchaseQuery->orderBy('is_paid', 'asc')
            ->orderBy('due_date', 'asc')
            ->paginate(10, ['*'], 'purchases_page')
            ->appends($request->all());


        // جلب حسابات الخزينة والبنوك النشطة لمعالجات السداد
        $financialAccounts = FinancialAccount::where('account_type', 'asset')->where('is_active', 1)->get();

        return view('backend.installments.index', compact(
            'studentInstallments', 
            'assetInstallments', 
            'purchaseInstallments', 
            'financialAccounts'
        ));
    }

    // ==========================================
    // 2. عرض الأقساط المتأخرة فقط (لكل الأقسام)
    // ==========================================
    public function overdue(Request $request)
    {
        abort_if(!auth()->user()->can('manage_installments'), 403);

        $today = now()->toDateString();
        $search = $request->search;

        // 🟢 أقساط الطلاب المتأخرة (تم تعديل الشرط ليتوافق مع حقل status)
        $studentQuery = StudentInstallment::with(['student.parent', 'contract'])
            ->where('status', '!=', 'paid')
            ->where('due_date', '<', $today);
        if ($search) {
            $studentQuery->whereHas('student', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")->orWhere('tribe', 'like', "%{$search}%");
            });
        }
        $studentOverdue = $studentQuery->orderBy('due_date', 'asc')->paginate(10, ['*'], 'students_page')->appends($request->all());

        // 🔵 أقساط الأصول المتأخرة
        $assetQuery = AssetInstallment::with(['asset'])
            ->where('is_paid', false)
            ->where('due_date', '<', $today);
        if ($search) {
            $assetQuery->whereHas('asset', function ($q) use ($search) {
                $q->where('asset_code', 'like', "%{$search}%");
            });
        }
        $assetOverdue = $assetQuery->orderBy('due_date', 'asc')->paginate(10, ['*'], 'assets_page')->appends($request->all());

        // 🟡 أقساط المشتريات المتأخرة
        $purchaseQuery = PurchaseInstallment::with(['invoice', 'supplier'])
            ->where('is_paid', false)
            ->where('due_date', '<', $today);
        if ($search) {
            $purchaseQuery->whereHas('supplier', function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%");
            });
        }
        $purchaseOverdue = $purchaseQuery->orderBy('due_date', 'asc')->paginate(10, ['*'], 'purchases_page')->appends($request->all());

        $financialAccounts = FinancialAccount::where('account_type', 'asset')->where('is_active', 1)->get();

        return view('backend.installments.overdue', compact(
            'studentOverdue', 
            'assetOverdue', 
            'purchaseOverdue', 
            'financialAccounts'
        ));
    }

    // ==========================================
    // 3. سداد قسط طالب (وارد / إيراد)
    // ==========================================
    public function payStudentInstallment(Request $request, $id)
    {
        abort_if(!auth()->user()->can('manage_installments'), 403);

        $request->validate([
            'amount_paid'          => 'required|numeric|min:0.1',
            'financial_account_id' => 'required|exists:financial_accounts,id',
            'check_number'         => 'nullable|string',
            'notes'                => 'nullable|string',
        ]);

        $installment = StudentInstallment::findOrFail($id);
        $contract = $installment->contract;
        $currentRemaining = round($installment->amount - $installment->paid_amount, 3);

        if (round($request->amount_paid, 3) > $currentRemaining) {
            return redirect()->back()->withErrors(['error' => 'عفواً، المبلغ المدخل أكبر من المتبقي من هذا القسط!']);
        }

        try {
            DB::beginTransaction();

            $payment = Payment::create([
                'financial_account_id' => $request->financial_account_id,
                'receipt_number'       => 'REC-STU-' . strtoupper(uniqid()),
                'user_id'              => auth()->id() ?? 1,
                'payment_type'         => 'installment',
                'payable_type'         => StudentInstallment::class,
                'payable_id'           => $installment->id,
                'amount'               => $request->amount_paid,
                'payment_date'         => now()->toDateString(),
                'check_number'         => $request->check_number,
                'transaction_details'  => 'سداد قسط دراسي للطالب: ' . ($installment->student->first_name ?? ''),
                'notes'                => $request->notes,
            ]);

            // 🔥 الحسبة المحمية المتوافقة مع هيكل جدول قسط الطالب الفعلي 🔥
            $newPaidAmount = $installment->paid_amount + $request->amount_paid;
            $newRemaining = round($installment->amount - $newPaidAmount, 3);
            $isFullyPaid = $newRemaining <= 0;

            $installment->update([
                'paid_amount'      => $newPaidAmount,
                'remaining_amount' => $newRemaining,
                'status'           => $isFullyPaid ? 'paid' : 'partial',
                'paid_date'        => $isFullyPaid ? now()->toDateString() : null,
            ]);

            if ($contract) {
                $contract->increment('paid_amount', $request->amount_paid);
            }
            
            FinancialAccount::where('id', $request->financial_account_id)->increment('balance', $request->amount_paid);

            // ترحيل قيد الحسابات المزدوج
            $branchId = $contract ? $contract->branch_id : 1;
            $this->createJournalEntry($payment, $request->amount_paid, $branchId, 'سداد قسط طالب - قيد رقم: ' . $payment->receipt_number, $request->financial_account_id, 'revenue');

            DB::commit();
            return redirect()->back()->with('success', 'تم استلام قسط الطالب وإيداعه في الحساب بنجاح.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    // ==========================================
    // 4. : سداد قسط أصل ثابت (صادر)
    // ==========================================
    public function payAssetInstallment(Request $request, $id)
    {
        abort_if(!auth()->user()->can('manage_installments'), 403);

        $request->validate([
            'financial_account_id' => 'required|exists:financial_accounts,id',
            'check_number'         => 'nullable|string',
            'notes'                => 'nullable|string',
        ]);

        $installment = AssetInstallment::with('asset')->findOrFail($id);
        $asset = $installment->asset;
        $account = FinancialAccount::findOrFail($request->financial_account_id);

        if ($installment->is_paid) {
            return redirect()->back()->withErrors(['error' => 'هذا القسط مدفوع بالفعل!']);
        }

        if ($asset->remaining_amount < $installment->amount) {
            return redirect()->back()->withErrors(['error' => 'المبلغ المتبقي في الأصل أقل من قيمة القسط!']);
        }

        if ($account->balance < $installment->amount) {
            return redirect()->back()->withErrors(['error' => 'عفواً، الرصيد المتاح في الحساب المالي لا يكفي لسداد قيمة هذا القسط.']);
        }

        try {
            DB::beginTransaction();

            $payment = Payment::create([
                'financial_account_id' => $request->financial_account_id,
                'receipt_number'       => 'REC-AST-' . strtoupper(uniqid()),
                'user_id'              => auth()->id() ?? 1,
                'payment_type'         => 'refund', 
                'payable_type'         => AssetInstallment::class,
                'payable_id'           => $installment->id,
                'amount'               => -$installment->amount, 
                'payment_date'         => now()->toDateString(),
                'check_number'         => $request->check_number,
                'transaction_details'  => 'سداد قسط صادر لشراء أصل كود: ' . $asset->asset_code,
                'notes'                => $request->notes,
            ]);

            $installment->update([
                'is_paid'   => true,
                'paid_date' => now()->toDateString(),
            ]);

            $asset->increment('paid_amount', $installment->amount);
            $asset->decrement('remaining_amount', $installment->amount);

            $account->decrement('balance', $installment->amount);

            $this->createAssetJournalEntry($payment, $installment->amount, $asset->branch_id, 'سداد قسط أصل ثابت كود: ' . $asset->asset_code, $request->financial_account_id);

            DB::commit();
            return redirect()->back()->with('success', 'تم سداد قسط الأصل وتحديث رصيد الحساب المالي بنجاح.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    // ==========================================
    // 5. سداد قسط فاتورة مشتريات (صادر / موردين)
    // ==========================================
    public function payPurchaseInstallment(Request $request, $id)
    {
        abort_if(!auth()->user()->can('manage_installments'), 403);

        $request->validate([
            'amount_paid'          => 'required|numeric|min:0.1',
            'financial_account_id' => 'required|exists:financial_accounts,id',
            'check_number'         => 'nullable|string',
            'notes'                => 'nullable|string',
        ]);

        $installment = PurchaseInstallment::with('invoice')->findOrFail($id);
        $invoice = $installment->invoice;
        $account = FinancialAccount::findOrFail($request->financial_account_id);
        
        $remaining = round($installment->amount - $installment->paid_amount, 3);

        if (round($request->amount_paid, 3) > $remaining) {
            return redirect()->back()->withErrors(['error' => 'المبلغ المكتوب أكبر من المتبقي من هذا القسط!']);
        }

        if ($account->balance < $request->amount_paid) {
            return redirect()->back()->withErrors(['error' => 'عفواً، الرصيد المتاح في الحساب المالي لا يكفي لدفع هذا المبلغ للمورد.']);
        }

        try {
            DB::beginTransaction();

            $payment = Payment::create([
                'financial_account_id' => $request->financial_account_id,
                'receipt_number'       => 'REC-PUR-' . strtoupper(uniqid()),
                'user_id'              => auth()->id() ?? 1,
                'payment_type'         => 'refund',
                'payable_type'         => PurchaseInstallment::class,
                'payable_id'           => $installment->id,
                'amount'               => -$request->amount_paid,
                'payment_date'         => now()->toDateString(),
                'check_number'         => $request->check_number,
                'transaction_details'  => 'سداد قسط فاتورة مشتريات رقم: ' . ($invoice->invoice_number ?? ''),
                'notes'                => $request->notes,
            ]);

            $newPaidAmount = $installment->paid_amount + $request->amount_paid;
            $isPaid = round($newPaidAmount, 3) >= round($installment->amount, 3);

            $installment->update([
                'paid_amount' => $newPaidAmount,
                'is_paid'     => $isPaid,
                'paid_date'   => $isPaid ? now()->toDateString() : null,
            ]);

            if ($invoice) {
                $invoice->increment('paid_amount', $request->amount_paid);
                $invoice->decrement('due_amount', $request->amount_paid);
                $invoice->update(['payment_status' => $invoice->due_amount <= 0 ? 'paid' : 'partial']);
            }

            $account->decrement('balance', $request->amount_paid);

            $supplierName = $installment->supplier ? $installment->supplier->company_name : 'المورد';
            $branchId = $invoice ? $invoice->branch_id : 1;
            $this->createPurchaseJournalEntry($payment, $request->amount_paid, $branchId, 'سداد قسط فاتورة مشتريات للمورد: ' . $supplierName, $request->financial_account_id);

            DB::commit();
            return redirect()->back()->with('success', 'تم سداد قسط المورد وخصم المبلغ من الحساب المالي.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    // ==========================================
    // دوال مساعدة لإنشاء القيود المحاسبية الآلية
    // ==========================================
    private function createJournalEntry($payment, $amount, $branchId, $description, $debitAccountId, $creditType)
    {
        $journalEntry = JournalEntry::create([
            'branch_id'      => $branchId,
            'entry_number'   => 'JE-' . date('Y') . '-' . time() . rand(10,99),
            'entry_date'     => now()->toDateString(),
            'description'    => $description,
            'reference_type' => Payment::class, 
            'reference_id'   => $payment->id,
            'created_by'     => auth()->id() ?? 1,
        ]);

        JournalItem::create([
            'journal_entry_id'     => $journalEntry->id,
            'financial_account_id' => $debitAccountId, 
            'debit'                => $amount,
            'credit'               => 0,
            'line_description'     => 'إيداع نقدية مستلمة بالحساب',
        ]);

        $creditAccount = FinancialAccount::where('account_type', $creditType)->first();
        $creditAccountId = $creditAccount ? $creditAccount->id : 2; 

        JournalItem::create([
            'journal_entry_id'     => $journalEntry->id,
            'financial_account_id' => $creditAccountId, 
            'debit'                => 0,
            'credit'               => $amount, 
            'line_description'     => 'إثبات إيراد الرسوم المستحقة',
        ]);
    }

    private function createAssetJournalEntry($payment, $amount, $branchId, $description, $creditAccountId)
    {
        $journalEntry = JournalEntry::create([
            'branch_id'      => $branchId,
            'entry_number'   => 'JE-AST-' . date('Y') . '-' . time() . rand(10,99),
            'entry_date'     => now()->toDateString(),
            'description'    => $description,
            'reference_type' => Payment::class,
            'reference_id'   => $payment->id,
            'created_by'     => auth()->id() ?? 1,
        ]);

        $assetAccount = FinancialAccount::where('account_type', 'liability')->first(); 
        $assetAccountId = $assetAccount ? $assetAccount->id : 1;

        JournalItem::create([
            'journal_entry_id'     => $journalEntry->id,
            'financial_account_id' => $assetAccountId,
            'debit'                => $amount,
            'credit'               => 0,
            'line_description'     => 'تخفيض التزامات الأصول الثابتة',
        ]);

        JournalItem::create([
            'journal_entry_id'     => $journalEntry->id,
            'financial_account_id' => $creditAccountId,
            'debit'                => 0,
            'credit'               => $amount,
            'line_description'     => 'صرف نقدية سداد قسط الأصل',
        ]);
    }

    private function createPurchaseJournalEntry($payment, $amount, $branchId, $description, $creditAccountId)
    {
        $journalEntry = JournalEntry::create([
            'branch_id'      => $branchId,
            'entry_number'   => 'JE-PUR-' . date('Y') . '-' . time() . rand(10,99),
            'entry_date'     => now()->toDateString(),
            'description'    => $description,
            'reference_type' => Payment::class,
            'reference_id'   => $payment->id,
            'created_by'     => auth()->id() ?? 1,
        ]);

        $supplierAccount = FinancialAccount::where('account_type', 'liability')->first(); 
        $supplierAccountId = $supplierAccount ? $supplierAccount->id : 1;

        JournalItem::create([
            'journal_entry_id'     => $journalEntry->id,
            'financial_account_id' => $supplierAccountId,
            'debit'                => $amount,
            'credit'               => 0,
            'line_description'     => 'تخفيض حساب المورد / التزامات دائنة',
        ]);

        JournalItem::create([
            'journal_entry_id'     => $journalEntry->id,
            'financial_account_id' => $creditAccountId,
            'debit'                => 0,
            'credit'               => $amount,
            'line_description'     => 'صرف نقدية سداد قسط المورد من الحساب',
        ]);
    }
}