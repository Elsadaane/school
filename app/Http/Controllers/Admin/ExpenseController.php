<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\ExpenseSubCategory;
use App\Models\FinancialAccount;
use App\Models\Supplier;
use App\Models\Payment;
use App\Models\Branche; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
 public function index()
{
    $expenses = Expense::with(['category', 'subCategory', 'financialAccount', 'supplier', 'user', 'branch'])->latest()->get();
    $categories = ExpenseCategory::all();
    
    // ✅ هذا السطر مهم جداً
    $subCategories = ExpenseSubCategory::all();
    
    $accounts = FinancialAccount::where('is_active', 1)->get();
    $suppliers = Supplier::all(); 
    $branches = Branche::all(); 

    return view('backend.expenses.index', compact(
        'expenses', 
        'categories', 
        'subCategories', // ✅ تأكد من تمريرها
        'accounts', 
        'suppliers', 
        'branches'
    ));
}

    // ==========================================
    // 2. تسجيل مصروف جديد (Store)
    // ==========================================
    public function store(Request $request)
    {
        $request->validate([
            'branch_id'            => 'required|exists:branches,id', 
            'expense_category_id'  => 'required|exists:expense_categories,id',
            'expense_sub_category_id' => 'nullable|exists:expense_sub_categories,id',
            'financial_account_id' => 'required|exists:financial_accounts,id',
            'amount'               => 'required|numeric|min:0.01',
            'expense_date'         => 'required|date',
            'transaction_details'  => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            $taxPercent = $request->tax_percent ?? 0;
            $taxAmount = $request->amount * ($taxPercent / 100);
            $totalAmount = $request->amount + $taxAmount;

            $account = FinancialAccount::findOrFail($request->financial_account_id);
            if ($account->balance < $totalAmount) {
                throw new \Exception("عفواً، رصيد الخزنة المتاح لا يكفي لسداد هذا المصروف!");
            }

            $attachmentPath = $request->hasFile('attachment') ? $request->file('attachment')->store('expenses_attachments', 'public') : null;

            $lastExpense = Expense::latest('id')->first();
            $expenseCode = 'EXP-' . str_pad(($lastExpense ? $lastExpense->id + 1 : 1), 5, '0', STR_PAD_LEFT);

            $expense = Expense::create([
                'expense_code'            => $expenseCode,
                'branch_id'               => $request->branch_id, 
                'expense_category_id'     => $request->expense_category_id,
                'expense_sub_category_id' => $request->expense_sub_category_id,
                'financial_account_id'    => $request->financial_account_id,
                'supplier_id'             => $request->supplier_id,
                'user_id'                 => auth()->id() ?? 1,
                'amount'                  => $request->amount,
                'tax_percent'             => $taxPercent,
                'tax_amount'              => $taxAmount,
                'total_amount'            => $totalAmount,
                'expense_date'            => $request->expense_date,
                'supplier_invoice_no'     => $request->supplier_invoice_no,
                'check_number'            => $request->check_number,
                'transaction_details'     => $request->transaction_details,
                'notes'                   => $request->notes,
                'attachment'              => $attachmentPath,
            ]);

            $account->decrement('balance', $totalAmount);

            Payment::create([
                'payable_type'         => 'App\Models\Expense',
                'payable_id'           => $expense->id,
                'financial_account_id' => $account->id,
                'user_id'              => auth()->id() ?? 1,
                'amount'               => -$totalAmount, 
                'payment_date'         => $request->expense_date,
                'transaction_details'  => 'مصروفات: ' . $request->transaction_details,
                'receipt_number'       => 'EXP-PAY-' . $expense->id,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'تم تسجيل المصروف وخصم المبلغ من الخزنة بنجاح!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    // ==========================================
    // 3. تعديل بيانات مصروف (Update)
    // ==========================================
    public function update(Request $request, $id)
    {
        $request->validate([
            'branch_id'               => 'required|exists:branches,id',
            'expense_category_id'     => 'required|exists:expense_categories,id',
            'expense_sub_category_id' => 'nullable|exists:expense_sub_categories,id',
            'supplier_id'             => 'nullable|exists:suppliers,id',
            'expense_date'            => 'required|date',
            'transaction_details'     => 'required|string',
        ]);

        try {
            $expense = Expense::findOrFail($id);
            
            $expense->update([
                'branch_id'               => $request->branch_id,
                'expense_category_id'     => $request->expense_category_id,
                'expense_sub_category_id' => $request->expense_sub_category_id,
                'supplier_id'             => $request->supplier_id,
                'supplier_invoice_no'     => $request->supplier_invoice_no,
                'check_number'            => $request->check_number,
                'transaction_details'     => $request->transaction_details,
                'expense_date'            => $request->expense_date,
                'notes'                   => $request->notes,
            ]);

            if ($request->hasFile('attachment')) {
                $expense->update([
                    'attachment' => $request->file('attachment')->store('expenses_attachments', 'public')
                ]);
            }

            return redirect()->back()->with('success', 'تم تحديث بيانات المصروف بنجاح!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء التحديث: ' . $e->getMessage());
        }
    }

    // ==========================================
    // 4. إلغاء المصروف واسترداد المبلغ (Destroy)
    // ==========================================
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $expense = Expense::findOrFail($id);

            $account = FinancialAccount::findOrFail($expense->financial_account_id);
            $account->increment('balance', $expense->total_amount);

            Payment::create([
                'payable_type'         => 'App\Models\Expense',
                'payable_id'           => $expense->id,
                'financial_account_id' => $account->id,
                'user_id'              => auth()->id() ?? 1,
                'amount'               => $expense->total_amount, 
                'payment_date'         => now(),
                'transaction_details'  => 'إلغاء مصروف رقم ' . $expense->expense_code . ' واسترداد المبلغ',
                'receipt_number'       => 'EXP-REV-' . $expense->id,
            ]);

            $expense->delete();

            DB::commit();
            return redirect()->back()->with('success', 'تم إلغاء المصروف واسترداد المبلغ للخزنة.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    // ==========================================
    // 5. إضافة الأقسام (رئيسي وفرعي)
    // ==========================================
    public function storeCategory(Request $request)
    {
        $request->validate(['name_ar' => 'required|string']);
        ExpenseCategory::create(['name_ar' => $request->name_ar]);
        return redirect()->back()->with('success', 'تم إضافة القسم بنجاح');
    }

    public function storeSubCategory(Request $request)
    {
        $request->validate(['expense_category_id' => 'required|exists:expense_categories,id', 'name_ar' => 'required|string']);
        ExpenseSubCategory::create([
            'expense_category_id' => $request->expense_category_id,
            'name_ar'             => $request->name_ar
        ]);
        return redirect()->back()->with('success', 'تم إضافة القسم الفرعي بنجاح');
    }

    // ==========================================
    // 6. عرض شاشة إعدادات أنواع المصروفات وحذفها
    // ==========================================
    public function types()
    {
        $categories = \App\Models\ExpenseCategory::all();
        $subCategories = \App\Models\ExpenseSubCategory::with('category')->get(); 
        
        return view('backend.expenses.types', compact('categories', 'subCategories'));
    }

    public function destroyCategory($id)
    {
        try {
            \App\Models\ExpenseCategory::findOrFail($id)->delete();
            return redirect()->back()->with('success', 'تم حذف القسم الرئيسي بنجاح!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'عفواً، لا يمكن حذف هذا القسم لارتباطه بمصروفات مسجلة!');
        }
    }

    public function destroySubCategory($id)
    {
        try {
            \App\Models\ExpenseSubCategory::findOrFail($id)->delete();
            return redirect()->back()->with('success', 'تم حذف القسم الفرعي بنجاح!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'عفواً، لا يمكن حذف هذا القسم لارتباطه بمصروفات مسجلة!');
        }
    }
    // 7. جلب الأقسام الفرعية (AJAX)
    // ==========================================
    public function getSubCategories($categoryId)
    {
        $subCategories = \App\Models\ExpenseSubCategory::where('expense_category_id', $categoryId)->get();
        return response()->json($subCategories);
    }
    // ==========================================
    // 8. طباعة إيصال الصرف (Print Receipt)
    // ==========================================
    public function printReceipt($id)
    {
        // جلب المصروف مع كل علاقاته المربوطة بيه
        $expense = Expense::with(['category', 'subCategory', 'financialAccount', 'supplier', 'user', 'branch'])->findOrFail($id);
        
        return view('backend.expenses.receipt', compact('expense'));
    }
}