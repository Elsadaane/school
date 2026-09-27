<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FinancialAccount;
use App\Models\StudentContract;
use App\Models\PurchaseInvoice;
use App\Models\Expense;
use App\Models\HrPayroll;
use App\Models\Income;
use App\Models\StudentInstallment;
use App\Models\JournalEntry;
use App\Models\JournalItem;
use Illuminate\Support\Facades\DB;

class AccountingController extends Controller
{
    // ==========================================
    // 🔥 دالة مساعدة لبناء الشجرة المحاسبية بلا حدود (Recursive Tree Builder) 🔥
    // ==========================================
    private function buildAccountTree($items, $parentId = null)
    {
        $branch = [];
        foreach ($items as $item) {
            if ($item->parent_id == $parentId) {
                $children = $this->buildAccountTree($items, $item->id);
                // تعيين الأبناء كـ Relation عشان الـ Blade يتعرف عليهم
                $item->setRelation('children', collect($children)); 
                $branch[] = $item;
            }
        }
        return $branch;
    }

    // ==========================================
    // 1. عرض شجرة الحسابات واللوحة المحاسبية (Dashboard)
    // ==========================================
    public function chartOfAccounts(Request $request)
    {
        $currentYear = date('Y');
        $year1 = $request->input('year1', $currentYear);
        $year2 = $request->input('year2', $currentYear - 1);

        // ✅ جلب كل الحسابات في استعلام واحد (أداء خيالي)
        $allAccounts = FinancialAccount::orderBy('account_code')->get();
        
        // ✅ بناء الشجرة برمجياً لتدعم كل الأعماق بدون N+1
        $accountsTree = collect($this->buildAccountTree($allAccounts));

        // دالة مساعدة لحساب أرقام أي سنة (من المستندات المصدرية)
        $calculateYearStats = function($year) {
            $revenues = StudentContract::whereYear('contract_date', $year)->sum('grand_total') 
                      + Income::whereYear('income_date', $year)->sum('total_amount');

            $expenses = PurchaseInvoice::whereYear('invoice_date', $year)->sum('grand_total')
                      + Expense::whereYear('expense_date', $year)->sum('total_amount')
                      + HrPayroll::whereYear('payment_date', $year)->where('status', 'paid')->sum('net_salary');

            return [
                'revenues' => $revenues,
                'expenses' => $expenses,
                'profit'   => $revenues - $expenses
            ];
        };

        $statsYear1 = $calculateYearStats($year1);
        $statsYear2 = $calculateYearStats($year2);

        $comparison = [
            'year1' => $year1,
            'year2' => $year2,
            'data1' => [$statsYear1['revenues'], $statsYear1['expenses'], $statsYear1['profit']],
            'data2' => [$statsYear2['revenues'], $statsYear2['expenses'], $statsYear2['profit']],
        ];

        // حساب الأرباح المرحلة للسنوات السابقة
        $prevRevenues = StudentContract::whereYear('contract_date', '<', $year1)->sum('grand_total') 
                      + Income::whereYear('income_date', '<', $year1)->sum('total_amount');
        
        $prevExpenses = PurchaseInvoice::whereYear('invoice_date', '<', $year1)->sum('grand_total')
                      + Expense::whereYear('expense_date', '<', $year1)->sum('total_amount')
                      + HrPayroll::whereYear('payment_date', '<', $year1)->where('status', 'paid')->sum('net_salary');

        $retainedEarnings = $prevRevenues - $prevExpenses;

        $outstandingInstallments = StudentInstallment::whereIn('status', ['unpaid', 'partial'])
            ->sum('remaining_amount'); 

        $kpis = [
            'total_revenues' => $statsYear1['revenues'],
            'total_expenses' => $statsYear1['expenses'],
            'net_profit' => $statsYear1['profit'],
            'retained_earnings' => $retainedEarnings,
            'total_equity' => $retainedEarnings + $statsYear1['profit'],
            'outstanding_installments' => $outstandingInstallments, 
        ];

        $availableYears = range(date('Y'), 2023);

        // ✅ تمرير $allAccounts للمودال، و $accountsTree للشجرة
        return view('backend.accounting.chart_of_accounts', compact(
            'accountsTree', 'allAccounts', 'kpis', 'comparison', 'availableYears', 'year1', 'year2'
        ));
    }

    // ==========================================
    // 2. إقفال السنة المالية (Year-End Closing) 
    // ==========================================
    public function closeFinancialYear(Request $request)
    {
        $year = $request->input('year', date('Y'));

        $checkYear = DB::table('financial_years')->where('year', $year)->first();
        if ($checkYear && $checkYear->is_closed) {
            return back()->with('error', 'عفواً، هذه السنة المالية مغلقة بالفعل ولا يمكن إقفالها مرة أخرى!');
        }

        DB::beginTransaction();
        try {
            $revenueAccounts = FinancialAccount::where('account_type', 'revenue')->where('balance', '>', 0)->get();
            $expenseAccounts = FinancialAccount::where('account_type', 'expense')->where('balance', '>', 0)->get();

            $totalRevenues = $revenueAccounts->sum('balance');
            $totalExpenses = $expenseAccounts->sum('balance');
            $netProfit = $totalRevenues - $totalExpenses;

            $retainedEarningsAccount = FinancialAccount::firstOrCreate(
                ['account_code' => '3001'], 
                [
                    'name_ar' => 'الأرباح المرحلة',
                    'name_en' => 'Retained Earnings',
                    'account_type' => 'equity',
                    'normal_balance' => 'credit',
                    'balance' => 0,
                    'is_active' => true
                ]
            );

            // ✅ التصحيح: حذف 'status' لأن الجدول لا يحتوي على هذا العمود
            $journalEntry = JournalEntry::create([
                'branch_id' => 1, 
                'entry_number' => 'CLS-' . $year . '-' . time(),
                'entry_date' => date("$year-12-31"),
                'description' => "قيد إقفال السنة المالية $year وترحيل الأرباح",
                'created_by' => auth()->id(),
            ]);

            foreach($revenueAccounts as $acc) {
                JournalItem::create([
                    'journal_entry_id' => $journalEntry->id,
                    'financial_account_id' => $acc->id,
                    'debit' => $acc->balance,
                    'credit' => 0,
                    'line_description' => 'إقفال حساب إيرادات'
                ]);
            }

            foreach($expenseAccounts as $acc) {
                JournalItem::create([
                    'journal_entry_id' => $journalEntry->id,
                    'financial_account_id' => $acc->id,
                    'debit' => 0,
                    'credit' => $acc->balance,
                    'line_description' => 'إقفال حساب مصروفات'
                ]);
            }

            if ($netProfit > 0) {
                JournalItem::create([
                    'journal_entry_id' => $journalEntry->id,
                    'financial_account_id' => $retainedEarningsAccount->id,
                    'debit' => 0,
                    'credit' => $netProfit,
                    'line_description' => "صافي أرباح عام $year"
                ]);
            } elseif ($netProfit < 0) {
                JournalItem::create([
                    'journal_entry_id' => $journalEntry->id,
                    'financial_account_id' => $retainedEarningsAccount->id,
                    'debit' => abs($netProfit),
                    'credit' => 0,
                    'line_description' => "صافي خسائر عام $year"
                ]);
            }

            // ترحيل الرصيد الفعلي لحساب الأرباح المرحلة
            $retainedEarningsAccount->balance += $netProfit;
            $retainedEarningsAccount->save();

            // تصفير الإيرادات والمصروفات
            FinancialAccount::whereIn('account_type', ['revenue', 'expense'])->update(['balance' => 0]);

            DB::table('financial_years')->insert([
                'year' => $year,
                'start_date' => "$year-01-01",
                'end_date' => "$year-12-31",
                'is_closed' => true,
                'closed_by' => auth()->id(),
                'closed_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();
            return back()->with('success', "تم إقفال السنة المالية $year بنجاح، وترحيل صافي ربح قدره (" . number_format($netProfit, 3) . ") إلى الأرباح المرحلة.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء الإقفال: ' . $e->getMessage());
        }
    }

    // ==========================================
    // 3. إضافة حساب مالي جديد
    // ==========================================
    public function storeAccount(Request $request)
    {
        $request->validate([
            'account_code' => 'required|string|unique:financial_accounts,account_code',
            'name_ar' => 'required|string|max:255',
            'account_type' => 'required|in:asset,liability,equity,revenue,expense',
            'normal_balance' => 'required|in:debit,credit',
            'parent_id' => 'nullable|exists:financial_accounts,id',
            'opening_balance' => 'nullable|numeric' // ✅ تم التعديل
        ]);

        $data = $request->all();
        // ✅ الرصيد الافتتاحي يُحفظ في حقل balance و opening_balance
        $data['balance'] = $request->input('opening_balance', 0);
        $data['opening_balance'] = $request->input('opening_balance', 0);

        FinancialAccount::create($data);

        return back()->with('success', 'تم إضافة الحساب بنجاح ضمن الشجرة المحاسبية.');
    }

    // ==========================================
    // 4. تعديل حساب مالي موجود
    // ==========================================
    public function updateAccount(Request $request, $id)
    {
        $account = FinancialAccount::findOrFail($id);
        
        $request->validate([
            'account_code' => 'required|string|unique:financial_accounts,account_code,' . $id,
            'name_ar' => 'required|string|max:255',
            'account_type' => 'required|in:asset,liability,equity,revenue,expense',
            'normal_balance' => 'required|in:debit,credit',
            'parent_id' => 'nullable|exists:financial_accounts,id',
        ]);

        $account->update($request->except(['balance', 'opening_balance']));

        return back()->with('success', 'تم تحديث بيانات الحساب بنجاح.');
    }

    // ==========================================
    // كشف حساب تفصيلي (Ledger)
    // ==========================================
    // public function ledger($id)
    // {
    //     abort_if(!auth()->user()->can('view_financial_accounts'), 403);

    //     $account = \App\Models\FinancialAccount::findOrFail($id);
        
    //     // هنا هنجيب الحركات المالية المرتبطة بالحساب (لو عندك جدول قيود أو مدفوعات)
    //     // مثال مؤقت لحد ما تصمم صفحة كشف الحساب:
    //     $transactions = []; 

    //     return view('backend.accounting.ledger', compact('account', 'transactions'));
    // }
       // ==========================================
// 🔥 كشف حساب تفصيلي (Ledger) 🔥
// ==========================================
public function ledger($id)
{
    abort_if(!auth()->user()->can('view_financial_accounts'), 403);

    $account = \App\Models\FinancialAccount::findOrFail($id);

    // 🆕 جلب التواريخ من الطلب مع قيم افتراضية
    $startDate = request()->input('start_date', \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d'));
    $endDate = request()->input('end_date', \Carbon\Carbon::now()->format('Y-m-d'));

    // جلب جميع الحركات في الفترة
    $entries = \App\Models\Payment::where('financial_account_id', $id)
        ->whereBetween('payment_date', [$startDate, $endDate])
        ->with('user')
        ->orderBy('payment_date')
        ->orderBy('id')
        ->get();

    // حساب الرصيد المتحرك
    $runningBalance = $account->opening_balance ?? 0;
    $transactions = [];

    foreach ($entries as $entry) {
        $debit = $entry->amount > 0 ? $entry->amount : 0;
        $credit = $entry->amount < 0 ? abs($entry->amount) : 0;

        // تعديل حسب الطبيعة الطبيعية للحساب
        if ($account->normal_balance === 'debit') {
            $runningBalance += $debit - $credit;
        } else {
            $runningBalance += $credit - $debit;
        }

        $transactions[] = [
            'date' => $entry->payment_date,
            'reference' => $entry->receipt_number,
            'description' => $entry->transaction_details,
            'debit' => $debit,
            'credit' => $credit,
            'balance' => $runningBalance,
            'user' => $entry->user->name ?? 'النظام',
        ];
    }

    // 🆕 إحصائيات
    $totalDebit = collect($transactions)->sum('debit');
    $totalCredit = collect($transactions)->sum('credit');

    // 🆕 إرجاع كل المتغيرات للـ view
    return view('backend.accounting.ledger', compact(
        'account', 
        'transactions', 
        'startDate',      // ✅ موجود الآن
        'endDate',        // ✅ موجود الآن
        'totalDebit',     // ✅ موجود الآن
        'totalCredit',    // ✅ موجود الآن
        'runningBalance'  // ✅ موجود الآن
    ));
}
}