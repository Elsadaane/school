<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\AssetSubCategory;
use App\Models\AssetInstallments;
use App\Models\FinancialAccount;
use App\Models\Payment;
use App\Models\Supplier;
use App\Models\Branche; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AssetController extends Controller
{
    // ==========================================
    // 1. عرض الأصول (Index)
    // ==========================================
    public function index()
    {
        $assets = Asset::with(['category', 'subCategory', 'financialAccount', 'employee', 'supplier', 'branch'])->latest()->get();
        $categories = AssetCategory::all();
        $subCategories = AssetSubCategory::all();
        $accounts = FinancialAccount::where('is_active', 1)->get();
        $suppliers = Supplier::all();
        $branches = Branche::all();

        return view('backend.assets.index', compact('assets', 'categories', 'subCategories', 'accounts', 'suppliers', 'branches'));
    }

    // ==========================================
    // 2. دوال التوقيعات والطباعة
    // ==========================================
    private function saveSignature($base64Data, $prefix)
    {
        if (empty($base64Data)) return null;
        try {
            $image_parts = explode(";base64,", $base64Data);
            if (count($image_parts) < 2) return null;
            $image_base64 = base64_decode($image_parts[1]);
            $fileName = 'sig_' . $prefix . '_' . uniqid() . '.png';
            $path = 'custody_signatures/' . $fileName;
            Storage::disk('public')->put($path, $image_base64);
            return $path;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function updateSignature(Request $request, $id)
    {
        $request->validate([
            'signature_base64' => 'nullable|string',
            'admin_signature_base64' => 'nullable|string',
        ]);

        $custody = \App\Models\HrCustody::findOrFail($id);
        $updatedData = [];

        if ($request->filled('signature_base64')) {
            $updatedData['signature_path'] = $this->saveSignature($request->signature_base64, 'update_emp_' . $custody->employee_id);
        }
        if ($request->filled('admin_signature_base64')) {
            $updatedData['admin_signature_path'] = $this->saveSignature($request->admin_signature_base64, 'update_admin_' . auth()->id());
        }

        if (!empty($updatedData)) {
            $custody->update($updatedData);
            return redirect()->back()->with('success', 'تم تحديث التوقيعات بنجاح!');
        }
        return redirect()->back()->with('error', 'لم يتم إرسال أي توقيع.');
    }

    public function printCustodyReceipt($id)
    {
        $asset = Asset::with('employee', 'category')->findOrFail($id);
        
        $custody = \App\Models\HrCustody::where('asset_id', $id)
                    ->where('employee_id', $asset->current_employee_id)
                    ->where('status', 'with_employee')
                    ->latest()
                    ->first();

        if(!$custody) {
            return redirect()->back()->with('error', 'لا يوجد محضر استلام فعال لهذا الأصل.');
        }

        return view('backend.assets.print_custody', compact('asset', 'custody')); 
    }

    // ==========================================
    // 3. إضافة الأقسام
    // ==========================================
    public function storeCategory(Request $request)
    {
        $request->validate(['name_ar' => 'required|string|max:255', 'name_en' => 'nullable|string|max:255']);
        AssetCategory::create($request->only(['name_ar', 'name_en']));
        return redirect()->back()->with('success', 'تم إضافة القسم الرئيسي بنجاح');
    }

    public function storeSubCategory(Request $request)
    {
        $request->validate([
            'asset_category_id' => 'required|exists:asset_categories,id',
            'name_ar'           => 'required|string|max:255',
            'name_en'           => 'nullable|string|max:255',
        ]);
        AssetSubCategory::create($request->only(['asset_category_id', 'name_ar', 'name_en']));
        return redirect()->back()->with('success', 'تم إضافة القسم الفرعي بنجاح');
    }

    // ==========================================
    // 4. إضافة أصل جديد (Create) - اللوجيك المالي
    // ==========================================
    public function storeAsset(Request $request)
    {
        $request->validate([
            'branch_id'             => 'required|exists:branches,id',
            'asset_category_id'     => 'required|exists:asset_categories,id',
            'supplier_id'           => 'nullable|exists:suppliers,id',
            'payment_type'          => 'required|in:opening,cash,installment',
            'financial_account_id'  => 'nullable|exists:financial_accounts,id', 
            'amount'                => 'required|numeric|min:0',
            'transaction_date'      => 'required|date',
            'transaction_details'   => 'required|string',
        ]);

        // 🔥 التحقق المحاسبي المخصص من الخزنة (Custom Validation) 🔥
        $downPayment = 0;
        if ($request->payment_type == 'installment') {
            $downPayment = $request->schedule_type == 'auto' ? ($request->down_payment_auto ?? 0) : ($request->down_payment_manual ?? 0);
        }

        if ($request->payment_type == 'cash' || ($request->payment_type == 'installment' && $downPayment > 0)) {
            if (empty($request->financial_account_id)) {
                return redirect()->back()->withErrors(['error' => 'يرجى اختيار الحساب المالي (الخزنة) لخصم الدفعة النقدية أو الدفعة المقدمة.'])->withInput();
            }
        }

        DB::beginTransaction();
        try {
            $taxPercent = $request->tax_percent ?? 0;
            $taxAmount = $request->amount * ($taxPercent / 100);
            $totalAmount = $request->amount + $taxAmount;

            $paidAmount = 0;
            $remainingAmount = 0;

            if ($request->payment_type == 'cash') {
                $paidAmount = $totalAmount;
            } elseif ($request->payment_type == 'installment') {
                $paidAmount = $downPayment;
                $remainingAmount = $totalAmount - $paidAmount;
            } elseif ($request->payment_type == 'opening') {
                $paidAmount = 0; 
                $remainingAmount = 0; // الرصيد الافتتاحي لا يسجل كمديونية جديدة هنا
                $request->merge(['financial_account_id' => null]);
            }

            $attachmentPath = $request->hasFile('attachment') ? $request->file('attachment')->store('assets_attachments', 'public') : null;

            $lastAsset = Asset::latest('id')->first();
            $assetCode = 'AST-' . str_pad(($lastAsset ? $lastAsset->id + 1 : 1), 5, '0', STR_PAD_LEFT);

            $asset = Asset::create([
                'asset_code'            => $assetCode,
                'branch_id'             => $request->branch_id,
                'asset_category_id'     => $request->asset_category_id,
                'asset_sub_category_id' => $request->asset_sub_category_id,
                'supplier_id'           => $request->supplier_id,
                'supplier_invoice_no'   => $request->supplier_invoice_no,
                'financial_account_id'  => $request->financial_account_id,
                'user_id'               => auth()->id(),
                'amount'                => $request->amount,
                'tax_percent'           => $taxPercent,
                'tax_amount'            => $taxAmount,
                'total_amount'          => $totalAmount,
                'payment_type'          => $request->payment_type,
                'paid_amount'           => $paidAmount,
                'remaining_amount'      => $remainingAmount,
                'transaction_date'      => $request->transaction_date,
                'depreciation_rate'     => $request->depreciation_rate,
                'check_number'          => $request->check_number,
                'transaction_details'   => $request->transaction_details,
                'notes'                 => $request->notes,
                'attachment'            => $attachmentPath,
            ]);

            // خصم الدفعة (الكاش أو المقدم) من الخزنة
            if ($paidAmount > 0 && $request->filled('financial_account_id')) {
                $account = FinancialAccount::findOrFail($request->financial_account_id);
                if ($account->balance < $paidAmount) throw new \Exception("رصيد الخزنة المتاح لا يكفي للدفع.");

                $account->decrement('balance', $paidAmount);
                Payment::create([
                    'payable_type'         => 'App\Models\Asset',
                    'payable_id'           => $asset->id,
                    'financial_account_id' => $account->id,
                    'user_id'              => auth()->id() ?? 1,
                    'amount'               => -$paidAmount, // بالسالب لأنه صادر
                    'payment_date'         => $request->transaction_date,
                    'transaction_details'  => 'دفعة أصل: ' . $request->transaction_details,
                    'receipt_number'       => 'AST-PAY-' . $asset->id . '-' . time(),
                ]);
            }

            // توليد الأقساط إذا كان النوع تقسيط
            if ($request->payment_type == 'installment' && $remainingAmount > 0) {
                if ($request->schedule_type == 'auto' && $request->months > 0) {
                    $installmentAmount = $remainingAmount / $request->months;
                    $startDate = \Carbon\Carbon::parse($request->installment_start_date);
                    for ($i = 0; $i < $request->months; $i++) {
                        AssetInstallments::create([
                            'asset_id' => $asset->id,
                            'amount'   => $installmentAmount,
                            'due_date' => $startDate->copy()->addMonths($i),
                            'is_paid'  => 0
                        ]);
                    }
                } 
                elseif ($request->schedule_type == 'manual' && $request->has('manual_amount')) {
                    $sumManual = array_sum($request->manual_amount);
                    if (abs($sumManual - $remainingAmount) > 0.1) { 
                        throw new \Exception("إجمالي الأقساط اليدوية لا يتطابق مع المبلغ المتبقي!");
                    }
                    foreach ($request->manual_amount as $index => $amt) {
                        if ($amt > 0 && !empty($request->manual_date[$index])) {
                            AssetInstallments::create([
                                'asset_id' => $asset->id,
                                'amount'   => $amt,
                                'due_date' => $request->manual_date[$index],
                                'is_paid'  => 0
                            ]);
                        }
                    }
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'تم حفظ الأصل وتسوية المعاملة المالية بنجاح!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    // ==========================================
    // 5. تعديل بيانات الأصل (Update)
    // ==========================================
    public function updateAsset(Request $request, $id)
    {
        $request->validate([
            'branch_id'             => 'required|exists:branches,id',
            'asset_category_id'     => 'required|exists:asset_categories,id',
            'asset_sub_category_id' => 'nullable|exists:asset_sub_categories,id',
            'supplier_id'           => 'nullable|exists:suppliers,id',
            'supplier_invoice_no'   => 'nullable|string',
            'transaction_details'   => 'required|string',
        ]);

        try {
            $asset = Asset::findOrFail($id);
            $asset->update([
                'branch_id'             => $request->branch_id,
                'asset_category_id'     => $request->asset_category_id,
                'asset_sub_category_id' => $request->asset_sub_category_id,
                'supplier_id'           => $request->supplier_id,
                'supplier_invoice_no'   => $request->supplier_invoice_no,
                'transaction_details'   => $request->transaction_details,
                'depreciation_rate'     => $request->depreciation_rate,
                'notes'                 => $request->notes,
            ]);

            if ($request->hasFile('attachment')) {
                $asset->update(['attachment' => $request->file('attachment')->store('assets_attachments', 'public')]);
            }

            return redirect()->back()->with('success', 'تم تحديث بيانات الأصل بنجاح!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء التحديث: ' . $e->getMessage());
        }
    }

    // ==========================================
    // 6. حذف الأصل (Destroy) مع التسوية
    // ==========================================
    public function destroy(Request $request, $id)
    {
        $request->validate([
            'return_reason' => 'required|string',
            'refund_action' => 'required|in:refund,suspend',
            'refund_account_id' => 'required_if:refund_action,refund',
        ]);

        DB::beginTransaction();
        try {
            $asset = Asset::findOrFail($id);
            
            if ($request->refund_action == 'refund' && $asset->paid_amount > 0) {
                $account = FinancialAccount::findOrFail($request->refund_account_id);
                $account->increment('balance', $asset->paid_amount);

                Payment::create([
                    'payable_type'         => 'App\Models\Asset',
                    'payable_id'           => $asset->id,
                    'financial_account_id' => $account->id,
                    'user_id'              => auth()->id() ?? 1,
                    'amount'               => $asset->paid_amount, // استرداد (بالموجب)
                    'payment_date'         => now(),
                    'transaction_details'  => 'استرداد أموال إلغاء أصل: ' . $asset->transaction_details,
                    'receipt_number'       => 'AST-REF-' . $asset->id . '-' . time(),
                    'notes'                => 'سبب الإلغاء: ' . $request->return_reason,
                ]);
            }

            if ($asset->remaining_amount > 0) {
                AssetInstallments::where('asset_id', $asset->id)->where('is_paid', 0)->delete();
            }

            $asset->update([
                'status' => 'returned',
                'return_reason' => $request->return_reason,
                'remaining_amount' => 0 
            ]);
            
            $asset->delete(); 

            DB::commit();
            return redirect()->back()->with('success', 'تم إلغاء الأصل، ومعالجة الحسابات والأقساط بنجاح!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    // ==========================================
    // 7. الأقساط والدفع (الخاصة بشاشة الأصول)
    // ==========================================
    public function installments()
    {
        $installments = AssetInstallments::with('asset.supplier')->orderBy('due_date', 'asc')->get();
        $accounts = FinancialAccount::where('is_active', 1)->get();
        return view('backend.assets.installments', compact('installments', 'accounts'));
    }

    public function payInstallment(Request $request, $id)
    {
        $request->validate([
            'financial_account_id' => 'required|exists:financial_accounts,id',
            'payment_date'         => 'required|date',
            'pay_amount'           => 'required|numeric|min:0.01'
        ]);

        DB::beginTransaction();
        try {
            $installment = AssetInstallments::with('asset')->findOrFail($id);
            if ($installment->is_paid) throw new \Exception('هذا القسط مدفوع بالفعل!');

            $payAmount = $request->pay_amount;
            if ($payAmount > $installment->amount) throw new \Exception('المبلغ المراد سداده أكبر من قيمة القسط!');

            $account = FinancialAccount::findOrFail($request->financial_account_id);
            if ($account->balance < $payAmount) throw new \Exception('رصيد الخزنة لا يكفي.');

            $account->decrement('balance', $payAmount);
            Payment::create([
                'payable_type'         => 'App\Models\AssetInstallments',
                'payable_id'           => $installment->id,
                'financial_account_id' => $account->id,
                'user_id'              => auth()->id() ?? 1,
                'amount'               => -$payAmount, 
                'payment_date'         => $request->payment_date,
                'transaction_details'  => 'سداد قسط لأصل: ' . $installment->asset->transaction_details,
                'receipt_number'       => 'INST-PAY-' . $installment->id . '-' . time(),
            ]);

            $installment->asset->increment('paid_amount', $payAmount);
            $installment->asset->decrement('remaining_amount', $payAmount);

            // لوجيك الدفع الكامل مقابل الجزئي
            if (abs($payAmount - $installment->amount) < 0.001) {
                $installment->update(['is_paid' => 1, 'paid_date' => $request->payment_date]);
                $msg = 'تم سداد القسط بالكامل بنجاح!';
            } else {
                $remainingInstallmentAmount = $installment->amount - $payAmount;
                $installment->update(['amount' => $payAmount, 'is_paid' => 1, 'paid_date' => $request->payment_date]);
                
                // ترحيل الباقي لقسط جديد
                AssetInstallments::create([
                    'asset_id' => $installment->asset_id,
                    'amount'   => $remainingInstallmentAmount,
                    'due_date' => $installment->due_date,
                    'is_paid'  => 0
                ]);
                $msg = 'تم سداد جزء من القسط وترحيل المتبقي كقسط مستقل!';
            }

            DB::commit();
            return redirect()->back()->with('success', $msg);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    // ==========================================
    // 8. تسليم واسترجاع العهدة
    // ==========================================
    public function assignCustody(Request $request, $id)
    {
        $request->validate([
            'employee_id' => 'required|exists:hr_employees,id',
            'assigned_date' => 'required|date',
            'asset_condition' => 'required|string',
            'signature_base64' => 'required|string',
            'admin_signature_base64' => 'required|string',
        ]);

        $asset = Asset::findOrFail($id);
        if ($asset->current_employee_id) {
            return redirect()->back()->with('error', 'هذا الأصل مُسلم بالفعل لموظف آخر!');
        }

        DB::beginTransaction();
        try {
            $asset->update([
                'current_employee_id' => $request->employee_id,
                'notes' => 'حالة الأصل عند التسليم: ' . $request->asset_condition
            ]);

            $empSig = $this->saveSignature($request->signature_base64, 'emp_' . $request->employee_id);
            $adminSig = $this->saveSignature($request->admin_signature_base64, 'admin_' . auth()->id());

            \App\Models\HrCustody::create([
                'asset_id'             => $asset->id,
                'employee_id'          => $request->employee_id,
                'item_name'            => $asset->transaction_details,
                'receive_date'         => $request->assigned_date,
                'notes'                => 'حالة الأصل: ' . $request->asset_condition . ' | ' . $request->notes,
                'signature_path'       => $empSig,
                'admin_signature_path' => $adminSig,
                'status'               => 'with_employee'
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'تم تسليم العهدة وحفظ التوقيعات بنجاح.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    public function returnCustody(Request $request, $id)
    {
        $request->validate([
            'return_condition' => 'required|string',
            'apply_deduction' => 'nullable',
            'deduction_amount' => 'required_if:apply_deduction,1|nullable|numeric|min:0'
        ]);

        $asset = Asset::findOrFail($id);
        $employeeId = $asset->current_employee_id;

        if (!$employeeId) {
            return redirect()->back()->with('error', 'هذا الأصل موجود في المدرسة وليس عهدة مع أحد.');
        }

        DB::beginTransaction();
        try {
            $custody = \App\Models\HrCustody::where('asset_id', $asset->id)
                ->where('employee_id', $employeeId)
                ->where('status', 'with_employee')
                ->first();

            if ($custody) {
                $custodyStatus = ($request->return_condition == 'سليم') ? 'returned' : 'damaged';
                $custody->update([
                    'status' => $custodyStatus,
                    'return_date' => now(),
                    'notes' => $custody->notes . ' | حالة الاسترجاع: ' . $request->return_condition . ' - ' . ($request->return_notes ?? '')
                ]);
            }

            if ($request->apply_deduction == '1' && $request->deduction_amount > 0) {
                $financialType = \App\Models\HrFinancialType::firstOrCreate(
                    ['name_ar' => 'خصم إتلاف عهدة'],
                    ['type' => 'deduction', 'value_type' => 'fixed']
                );

                \App\Models\HrEmployeeFinancial::create([
                    'employee_id' => $employeeId,
                    'financial_type_id' => $financialType->id, 
                    'amount' => $request->deduction_amount,
                    'action_date' => now(),
                    'notes' => 'خصم عهدة: ' . $asset->transaction_details
                ]);
            }

            $assetStatus = 'active';
            if ($request->return_condition == 'تالف كليا' || $request->return_condition == 'مفقود') {
                $assetStatus = 'suspended'; 
            }

            $asset->update([
                'current_employee_id' => null,
                'status' => $assetStatus,
                'notes' => 'تم الاسترجاع بحالة: ' . $request->return_condition . ' | ' . ($request->return_notes ?? '')
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'تم استلام الأصل وإخلاء طرف الموظف بنجاح!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    // ==========================================
    // 9. الأقسام الرئيسية والفرعية
    // ==========================================
    public function types()
    {
        $categories = AssetCategory::all();
        $subCategories = AssetSubCategory::with('category')->get(); 
        return view('backend.assets.types', compact('categories', 'subCategories'));
    }

    public function destroyCategory($id)
    {
        try {
            AssetCategory::findOrFail($id)->delete();
            return redirect()->back()->with('success', 'تم حذف القسم الرئيسي بنجاح!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'لا يمكن حذف القسم لارتباطه بأصول!');
        }
    }

    public function destroySubCategory($id)
    {
        try {
            AssetSubCategory::findOrFail($id)->delete();
            return redirect()->back()->with('success', 'تم حذف القسم الفرعي بنجاح!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'لا يمكن حذف القسم لارتباطه بأصول!');
        }
    }
}