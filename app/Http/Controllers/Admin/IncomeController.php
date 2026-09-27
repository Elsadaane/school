<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Income;
use App\Models\IncomeCategory;
use App\Models\IncomeSubCategory;
use App\Models\FinancialAccount;
use App\Models\Payment;
use App\Models\Branche;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage; 

class IncomeController extends Controller
{
    // ==========================================
    // 1. عرض الإيرادات (Index)
    // ==========================================
    public function index()
    {
        abort_if(!auth()->user()->can('manage_incomes'), 403, 'غير مصرح لك بإدارة الإيرادات.');

        $incomes = Income::with(['category', 'subCategory', 'financialAccount', 'user', 'branch'])->latest()->paginate(20);
        
        $categories = IncomeCategory::all();
        $subCategories = IncomeSubCategory::all();
        $accounts = FinancialAccount::where('is_active', 1)->get();
        $branches = Branche::where('is_active', 1)->get(); 

        return view('backend.incomes.index', compact('incomes', 'categories', 'subCategories', 'accounts', 'branches'));
    }

    // ==========================================
    // 2. تسجيل إيراد جديد (Store)
    // ==========================================
    public function store(Request $request)
    {
        abort_if(!auth()->user()->can('manage_incomes'), 403);

        $request->validate([
            'branch_id'            => 'required|exists:branches,id',
            'income_category_id'   => 'required|exists:income_categories,id',
            'financial_account_id' => 'required|exists:financial_accounts,id',
            'amount'               => 'required|numeric|min:0.01',
            'income_date'          => 'required|date',
            'transaction_details'  => 'required|string',
            'attachment'           => 'nullable|mimes:pdf,jpg,jpeg,png|max:2048' 
        ]);

        DB::beginTransaction();
        try {
            $taxPercent = $request->tax_percent ?? 0;
            $taxAmount = $request->amount * ($taxPercent / 100);
            $totalAmount = $request->amount + $taxAmount;

            $attachmentPath = $request->hasFile('attachment') ? $request->file('attachment')->store('incomes_attachments', 'public') : null;

            $lastIncome = Income::latest('id')->first();
            $incomeCode = 'INC-' . str_pad(($lastIncome ? $lastIncome->id + 1 : 1), 5, '0', STR_PAD_LEFT);

            // 1. تسجيل الإيراد
            $income = Income::create([
                'income_code'            => $incomeCode,
                'branch_id'              => $request->branch_id,
                'income_category_id'     => $request->income_category_id,
                'income_sub_category_id' => $request->income_sub_category_id,
                'financial_account_id'   => $request->financial_account_id,
                'user_id'                => auth()->id() ?? 1,
                'source_name'            => $request->source_name,
                'reference_no'           => $request->reference_no,
                'amount'                 => $request->amount,
                'tax_percent'            => $taxPercent,
                'tax_amount'             => $taxAmount,
                'total_amount'           => $totalAmount,
                'income_date'            => $request->income_date,
                'check_number'           => $request->check_number,
                'transaction_details'    => $request->transaction_details,
                'notes'                  => $request->notes,
                'attachment'             => $attachmentPath,
            ]);

            // 2. تزويد رصيد الخزنة (إيداع)
            $account = FinancialAccount::findOrFail($request->financial_account_id);
            $account->increment('balance', $totalAmount);

            // 3. تسجيل قيد اليومية (سند قبض بالموجب)
            Payment::create([
                'payable_type'         => 'App\Models\Income',
                'payable_id'           => $income->id,
                'financial_account_id' => $account->id,
                'user_id'              => auth()->id() ?? 1,
                'amount'               => $totalAmount,
                'payment_date'         => $request->income_date,
                'transaction_details'  => 'إيراد: ' . $request->transaction_details,
                'receipt_number'       => 'INC-REC-' . $income->id,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'تم تسجيل الإيراد وإيداع المبلغ بالخزنة بنجاح!');
        } catch (\Exception $e) {
            DB::rollBack();
            if (isset($attachmentPath)) Storage::disk('public')->delete($attachmentPath);
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    // ==========================================
    // 3. دالة التعديل (Update)
    // ==========================================
    public function update(Request $request, $id)
    {
        abort_if(!auth()->user()->can('manage_incomes'), 403);

        $request->validate([
            'branch_id'              => 'required|exists:branches,id',
            'income_category_id'     => 'required|exists:income_categories,id',
            'income_sub_category_id' => 'nullable|exists:income_sub_categories,id',
            'income_date'            => 'required|date',
            'transaction_details'    => 'required|string',
            'attachment'             => 'nullable|mimes:pdf,jpg,jpeg,png|max:2048'
        ]);

        try {
            $income = Income::findOrFail($id);
            
            $income->update([
                'branch_id'              => $request->branch_id,
                'income_category_id'     => $request->income_category_id,
                'income_sub_category_id' => $request->income_sub_category_id,
                'source_name'            => $request->source_name,
                'reference_no'           => $request->reference_no,
                'check_number'           => $request->check_number,
                'transaction_details'    => $request->transaction_details,
                'income_date'            => $request->income_date,
                'notes'                  => $request->notes,
            ]);

            if ($request->hasFile('attachment')) {
                if ($income->attachment) {
                    Storage::disk('public')->delete($income->attachment);
                }
                $income->update([
                    'attachment' => $request->file('attachment')->store('incomes_attachments', 'public')
                ]);
            }

            return redirect()->back()->with('success', 'تم تحديث بيانات الإيراد الوصفية بنجاح!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء التحديث: ' . $e->getMessage());
        }
    }

    // ==========================================
    // 4. إلغاء الإيراد وسحب المبلغ من الخزنة (Destroy)
    // ==========================================
    public function destroy($id)
    {
        abort_if(!auth()->user()->can('manage_incomes'), 403);

        DB::beginTransaction();
        try {
            $income = Income::findOrFail($id);

            $account = FinancialAccount::findOrFail($income->financial_account_id);
            $account->decrement('balance', $income->total_amount);

            Payment::create([
                'payable_type'         => 'App\Models\Income',
                'payable_id'           => $income->id,
                'financial_account_id' => $account->id,
                'user_id'              => auth()->id() ?? 1,
                'amount'               => -$income->total_amount,
                'payment_date'         => now(),
                'transaction_details'  => 'إلغاء إيراد رقم ' . $income->income_code . ' وسحب المبلغ',
                'receipt_number'       => 'INC-REV-' . $income->id,
            ]);

            if ($income->attachment) {
                Storage::disk('public')->delete($income->attachment);
            }

            $income->delete();

            DB::commit();
            return redirect()->back()->with('success', 'تم إلغاء الإيراد وسحب المبلغ من الخزنة لضبط الحسابات.');
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
        abort_if(!auth()->user()->can('manage_incomes'), 403);
        $request->validate(['name_ar' => 'required|string|max:255']);
        IncomeCategory::create(['name_ar' => $request->name_ar]);
        return redirect()->back()->with('success', 'تم إضافة نوع الإيراد الرئيسي بنجاح');
    }

    public function storeSubCategory(Request $request)
    {
        abort_if(!auth()->user()->can('manage_incomes'), 403);
        $request->validate([
            'income_category_id' => 'required|exists:income_categories,id',
            'name_ar'            => 'required|string|max:255',
        ]);
        IncomeSubCategory::create($request->only(['income_category_id', 'name_ar']));
        return redirect()->back()->with('success', 'تم إضافة نوع الإيراد الفرعي بنجاح');
    }

    // ==========================================
    // 6. عرض شاشة إعدادات أنواع الإيرادات وحذفها
    // ==========================================
    public function types()
    {
        abort_if(!auth()->user()->can('manage_incomes'), 403);
        $categories = \App\Models\IncomeCategory::all();
        $subCategories = \App\Models\IncomeSubCategory::with('category')->get(); 
        return view('backend.incomes.types', compact('categories', 'subCategories'));
    }

    public function destroyCategory($id)
    {
        abort_if(!auth()->user()->can('manage_incomes'), 403);
        try {
            \App\Models\IncomeCategory::findOrFail($id)->delete();
            return redirect()->back()->with('success', 'تم حذف القسم الرئيسي بنجاح!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'عفواً، لا يمكن حذف هذا القسم لارتباطه بإيرادات أخرى!');
        }
    }

    public function destroySubCategory($id)
    {
        abort_if(!auth()->user()->can('manage_incomes'), 403);
        try {
            \App\Models\IncomeSubCategory::findOrFail($id)->delete();
            return redirect()->back()->with('success', 'تم حذف القسم الفرعي بنجاح!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'عفواً، لا يمكن حذف هذا القسم لارتباطه بإيرادات أخرى!');
        }
    }

    // ==========================================
    // 7. طباعة سند القبض (الإيصال)
    // ==========================================
    public function printReceipt($id)
    {
        $income = Income::with(['category', 'subCategory', 'financialAccount', 'user', 'branch'])->findOrFail($id);
        return view('backend.incomes.receipt', compact('income'));
    }
}