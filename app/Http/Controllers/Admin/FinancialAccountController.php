<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinancialAccount;
use App\Models\Payment;
use App\Models\JournalEntry;
use App\Models\JournalItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FinancialAccountController extends Controller
{
    // ==========================================
    // 🔥 1. عرض الحسابات مع الفلاتر 🔥
    // ==========================================
    public function index(Request $request)
    {
        abort_if(!auth()->user()->can('view_financial_accounts'), 403);

        $accountType = $request->input('account_type');
        $isActive = $request->input('is_active');
        $search = $request->input('search');

        $query = FinancialAccount::with('parent');

        if ($accountType) {
            $query->where('account_type', $accountType);
        }
        if ($isActive !== null && $isActive !== '') {
            $query->where('is_active', $isActive);
        }
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name_ar', 'like', "%{$search}%")
                  ->orWhere('name_en', 'like', "%{$search}%")
                  ->orWhere('account_code', 'like', "%{$search}%");
            });
        }

        $accounts = $query->orderBy('account_code')->get();

        // إحصائيات
        $totalAccounts = $accounts->count();
        $activeAccounts = $accounts->where('is_active', 1)->count();
        $totalBalance = $accounts->sum('balance');
        $byType = $accounts->groupBy('account_type')->map->count();

        return view('backend.financial_accounts.index', compact(
            'accounts', 'accountType', 'isActive', 'search',
            'totalAccounts', 'activeAccounts', 'totalBalance', 'byType'
        ));
    }

    // ==========================================
    // 🔥 2. إضافة حساب جديد 🔥
    // ==========================================
    public function store(Request $request)
    {
        abort_if(!auth()->user()->can('manage_financial_accounts'), 403);

        $validated = $request->validate([
            'account_code'    => 'required|string|unique:financial_accounts,account_code|max:50',
            'name_ar'         => 'required|string|max:255',
            'name_en'         => 'nullable|string|max:255',
            'account_type'    => 'required|in:asset,liability,equity,revenue,expense',
            'normal_balance'  => 'required|in:debit,credit',
            'parent_id'       => 'nullable|exists:financial_accounts,id',
            'opening_balance' => 'nullable|numeric|min:0',
        ], [
            'account_code.unique' => 'الكود المحاسبي موجود مسبقاً',
            'account_code.required' => 'الكود المحاسبي مطلوب',
            'name_ar.required' => 'اسم الحساب بالعربي مطلوب',
        ]);

        $balance = $validated['opening_balance'] ?? 0;

        try {
            DB::beginTransaction();

            FinancialAccount::create([
                'account_code'    => $validated['account_code'],
                'name_ar'         => $validated['name_ar'],
                'name_en'         => $validated['name_en'] ?? null,
                'account_type'    => $validated['account_type'],
                'normal_balance'  => $validated['normal_balance'],
                'parent_id'       => $validated['parent_id'] ?? null,
                'opening_balance' => $balance,
                'balance'         => $balance,
                'is_active'       => $request->has('is_active') ? 1 : 0,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'تم إضافة الحساب المحاسبي بنجاح.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'خطأ في الإضافة: ' . $e->getMessage())->withInput();
        }
    }

    // ==========================================
    // 🔥 3. تعديل الحساب 🔥
    // ==========================================
    public function update(Request $request, $id)
    {
        abort_if(!auth()->user()->can('manage_financial_accounts'), 403);

        $account = FinancialAccount::findOrFail($id);

        $validated = $request->validate([
            'account_code'   => 'required|string|unique:financial_accounts,account_code,' . $id . '|max:50',
            'name_ar'        => 'required|string|max:255',
            'name_en'        => 'nullable|string|max:255',
            'account_type'   => 'required|in:asset,liability,equity,revenue,expense',
            'normal_balance' => 'required|in:debit,credit',
            'parent_id'      => 'nullable|exists:financial_accounts,id|different:' . $id,
        ], [
            'parent_id.different' => 'لا يمكن أن يكون الحساب أباً لنفسه',
        ]);

        // حماية: منع تغيير النوع إذا كان الحساب مستخدم
        if ($account->account_type !== $validated['account_type']) {
            $hasTransactions = Payment::where('financial_account_id', $id)->exists();
            if ($hasTransactions) {
                return redirect()->back()->with('error', 'لا يمكن تغيير نوع الحساب لأنه يحتوي على حركات مالية سابقة.');
            }
        }

        $account->update([
            'account_code'   => $validated['account_code'],
            'name_ar'        => $validated['name_ar'],
            'name_en'        => $validated['name_en'] ?? null,
            'account_type'   => $validated['account_type'],
            'normal_balance' => $validated['normal_balance'],
            'parent_id'      => $validated['parent_id'] ?? null,
            'is_active'      => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->back()->with('success', 'تم تعديل بيانات الحساب المحاسبي بنجاح.');
    }

    // ==========================================
    // 🔥 4. حذف الحساب 🔥
    // ==========================================
    public function destroy($id)
    {
        abort_if(!auth()->user()->can('manage_financial_accounts'), 403);

        $account = FinancialAccount::findOrFail($id);

        // حماية: منع حذف الحسابات المستخدمة
        $hasTransactions = Payment::where('financial_account_id', $id)->exists();
        if ($hasTransactions) {
            return redirect()->back()->with('error', 'لا يمكن حذف الحساب لأنه يحتوي على حركات مالية. يمكنك تعطيله بدلاً من الحذف.');
        }

        // حماية: منع حذف الحسابات التي لها حسابات فرعية
        $hasChildren = FinancialAccount::where('parent_id', $id)->exists();
        if ($hasChildren) {
            return redirect()->back()->with('error', 'لا يمكن حذف الحساب لأنه يحتوي على حسابات فرعية.');
        }

        $account->delete();

        return redirect()->back()->with('success', 'تم حذف الحساب المحاسبي بنجاح.');
    }

    // ==========================================
    // 🔥 5. التحويل المالي 🔥
    // ==========================================
    public function transferMoney(Request $request)
    {
        abort_if(!auth()->user()->can('manage_financial_accounts'), 403);

        $validated = $request->validate([
            'from_account_id' => 'required|exists:financial_accounts,id',
            'to_account_id'   => 'required|exists:financial_accounts,id|different:from_account_id',
            'amount'          => 'required|numeric|min:0.001',
            'notes'           => 'nullable|string|max:500',
        ], [
            'to_account_id.different' => 'لا يمكن التحويل لنفس الحساب!',
        ]);

        $fromAccount = FinancialAccount::lockForUpdate()->findOrFail($validated['from_account_id']);
        $toAccount = FinancialAccount::lockForUpdate()->findOrFail($validated['to_account_id']);

        if ($fromAccount->balance < $validated['amount']) {
            return redirect()->back()->withInput()->withErrors([
                'amount' => "الرصيد المتاح في حساب ({$fromAccount->name_ar}) هو " . number_format($fromAccount->balance, 3) . " ر.ع ولا يكفي."
            ]);
        }

        try {
            DB::beginTransaction();
            
            $reference = 'TRF-' . strtoupper(uniqid());
            $now = now();

            // 1. قيد السحب
            Payment::create([
                'payable_type'         => get_class($fromAccount),
                'payable_id'           => $fromAccount->id,
                'financial_account_id' => $fromAccount->id,
                'user_id'              => auth()->id(),
                'payment_type'         => 'transfer_out',
                'amount'               => -$validated['amount'],
                'payment_date'         => $now,
                'transaction_details'  => 'تحويل صادر إلى: ' . $toAccount->name_ar . ' | مرجع: ' . $reference,
                'receipt_number'       => $reference . '-OUT',
                'notes'                => $validated['notes'] ?? 'تحويل داخلي',
            ]);
            $fromAccount->decrement('balance', $validated['amount']);
            
            // 2. قيد الإيداع
            Payment::create([
                'payable_type'         => get_class($toAccount),
                'payable_id'           => $toAccount->id,
                'financial_account_id' => $toAccount->id,
                'user_id'              => auth()->id(),
                'payment_type'         => 'transfer_in',
                'amount'               => $validated['amount'],
                'payment_date'         => $now,
                'transaction_details'  => 'تحويل وارد من: ' . $fromAccount->name_ar . ' | مرجع: ' . $reference,
                'receipt_number'       => $reference . '-IN',
                'notes'                => $validated['notes'] ?? 'تحويل داخلي',
            ]);
            $toAccount->increment('balance', $validated['amount']);
            
            DB::commit();
            return redirect()->back()->with('success', 'تم تحويل ' . number_format($validated['amount'], 3) . ' ر.ع بنجاح');
            
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors(['error' => 'خطأ: ' . $e->getMessage()]);
        }
    }

    // ==========================================
    // 🔥 6. سجل التحويلات 🔥
    // ==========================================
    public function history(Request $request)
    {
        abort_if(!auth()->user()->can('view_financial_accounts'), 403);

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $accountId = $request->input('account_id');

        $query = Payment::with(['financialAccount', 'user'])
                        ->whereIn('payment_type', ['transfer_out', 'transfer_in']);

        if ($startDate) {
            $query->whereDate('payment_date', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('payment_date', '<=', $endDate);
        }
        if ($accountId) {
            $query->where('financial_account_id', $accountId);
        }

        $transfers = $query->latest()->paginate(20)->withQueryString();
        $accounts = FinancialAccount::where('is_active', 1)->orderBy('name_ar')->get();

        return view('backend.financial_accounts.transfer_history', compact(
            'transfers', 'accounts', 'startDate', 'endDate', 'accountId'
        ));
    }

 
 
}