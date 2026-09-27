<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Models\Branche;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseReturn;
use App\Models\Payment;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    // ==========================================
    // 1. عرض قائمة الموردين
    // ==========================================
    public function index(Request $request)
    {
        // 🔒 حماية الشاشة بصلاحية الموردين والمشتريات
        abort_if(!auth()->user()->can('manage_suppliers'), 403, 'غير مصرح لك بإدارة بيانات الموردين.');

        $query = Supplier::with('branch')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('company_name', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
        }

        $suppliers = $query->paginate(15)->withQueryString();
        return view('backend.suppliers.index', compact('suppliers'));
    }

    // ==========================================
    // 2. شاشة إضافة مورد جديد
    // ==========================================
    public function create()
    {
        abort_if(!auth()->user()->can('manage_suppliers'), 403);
        $branches = Branche::where('is_active', 1)->get();
        return view('backend.suppliers.create', compact('branches'));
    }

    // ==========================================
    // 3. حفظ بيانات المورد
    // ==========================================
    public function store(Request $request)
    {
        abort_if(!auth()->user()->can('manage_suppliers'), 403);

        $request->validate([
            'branch_id'       => 'required|exists:branches,id',
            'company_name'    => 'required|string|max:255',
            'contact_person'  => 'nullable|string|max:255',
            'phone'           => 'nullable|string|max:50',
            'email'           => 'nullable|email|max:255',
            'tax_number'      => 'nullable|string|max:100',
            'opening_balance' => 'nullable|numeric',
            'address'         => 'nullable|string|max:500',
        ]);

        $openingBalance = $request->opening_balance ?? 0;

        Supplier::create([
            'branch_id'       => $request->branch_id,
            'company_name'    => $request->company_name,
            'contact_person'  => $request->contact_person,
            'phone'           => $request->phone,
            'email'           => $request->email,
            'tax_number'      => $request->tax_number,
            'address'         => $request->address,
            'opening_balance' => $openingBalance,
            'current_balance' => $openingBalance, // يبدأ بنفس الرصيد الافتتاحي
            'is_active'       => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->route('admin.suppliers.index')->with('success', 'تم إضافة المورد بنجاح.');
    }

// ==========================================
    // 4. كشف الحساب والتقييم الذكي (Dashboard المورد)
    // ==========================================
    public function show($id)
    {
        abort_if(!auth()->user()->can('manage_suppliers'), 403);

        $supplier = Supplier::findOrFail($id);

        $purchaseInvoices = PurchaseInvoice::where('supplier_id', $id)->latest()->get();
        $totalPurchases = $purchaseInvoices->sum('grand_total');
        $totalPaidInvoices = $purchaseInvoices->sum('paid_amount'); // للعرض الإحصائي فقط

        $returns = PurchaseReturn::where('supplier_id', $id)->latest()->get();
        $totalReturns = $returns->sum('total_return_amount');

        // 💡 هنجيب كل السندات المرتبطة بالمورد مباشرة
        $payments = Payment::where('payable_type', get_class($supplier))
                           ->where('payable_id', $supplier->id)
                           ->latest()->get();
        
        // 🔥 تجميع المبالغ بالقيمة المطلقة (abs) لعلاج مشكلة السالب المتسجل في الداتابيز 🔥
        $totalPaid = $payments->sum(function($pay) {
            return abs($pay->amount);
        });

        // المديونية الصافية = (مشتريات + رصيد افتتاحي) - (مرتجعات + كل اللي ادفع للمورد)
        $dueAmount = ($totalPurchases + $supplier->opening_balance) - ($totalReturns + $totalPaid);

        // خوارزمية التقييم
        $rating = 5; 
        $ratingText = 'ممتاز (مورد موثوق)';
        $ratingColor = 'success';

        if ($totalPurchases > 0) {
            $returnRatio = ($totalReturns / $totalPurchases) * 100;

            if ($returnRatio > 0 && $returnRatio <= 5) {
                $rating = 4.5;
                $ratingText = 'جيد جداً (مرتجعات طفيفة)';
                $ratingColor = 'primary';
            } elseif ($returnRatio > 5 && $returnRatio <= 15) {
                $rating = 3.5;
                $ratingText = 'جيد (مرتجعات متوسطة)';
                $ratingColor = 'warning';
            } elseif ($returnRatio > 15 && $returnRatio <= 30) {
                $rating = 2;
                $ratingText = 'مقبول (يجب الحذر)';
                $ratingColor = 'danger';
            } elseif ($returnRatio > 30) {
                $rating = 1;
                $ratingText = 'سيء (بضاعة معيبة بكثرة)';
                $ratingColor = 'dark';
            }
        } elseif ($totalPurchases == 0 && $totalReturns == 0) {
            $rating = 0;
            $ratingText = 'لا توجد تعاملات بعد';
            $ratingColor = 'secondary';
        }

        return view('backend.suppliers.show', compact(
            'supplier', 'purchaseInvoices', 'returns', 'payments',
            'totalPurchases', 'totalReturns', 'dueAmount', 'totalPaid', 'totalPaidInvoices',
            'rating', 'ratingText', 'ratingColor'
        ));
    }
    // ==========================================
    // 5. شاشة تعديل بيانات المورد
    // ==========================================
    public function edit($id)
    {
        abort_if(!auth()->user()->can('manage_suppliers'), 403);
        $supplier = Supplier::findOrFail($id);
        $branches = Branche::where('is_active', 1)->get();
        return view('backend.suppliers.edit', compact('supplier', 'branches'));
    }

    // ==========================================
    // 6. تحديث بيانات المورد
    // ==========================================
    public function update(Request $request, $id)
    {
        abort_if(!auth()->user()->can('manage_suppliers'), 403);

        $supplier = Supplier::findOrFail($id);

        $request->validate([
            'branch_id'      => 'required|exists:branches,id',
            'company_name'   => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone'          => 'nullable|string|max:50',
            'email'          => 'nullable|email|max:255',
            'tax_number'     => 'nullable|string|max:100',
            'address'        => 'nullable|string|max:500',
        ]);

        $supplier->update([
            'branch_id'      => $request->branch_id,
            'company_name'   => $request->company_name,
            'contact_person' => $request->contact_person,
            'phone'          => $request->phone,
            'email'          => $request->email,
            'tax_number'     => $request->tax_number,
            'address'        => $request->address,
            'is_active'      => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->route('admin.suppliers.index')->with('success', 'تم تحديث بيانات المورد بنجاح.');
    }

    // ==========================================
    // 7. حذف المورد (الذكي)
    // ==========================================
    public function destroy($id)
    {
        abort_if(!auth()->user()->can('manage_suppliers'), 403);

        $supplier = Supplier::findOrFail($id);
        
        // 🌟 حماية محاسبية: لو لديه فواتير، نقوم بحذف ناعم (Soft Delete) فقط
        if($supplier->purchaseInvoices()->exists() || $supplier->purchaseReturns()->exists()) {
            $supplier->delete(); 
            return redirect()->back()->with('success', 'تم إيقاف المورد، وسيظل موجوداً في الأرشيف المالي لارتباطه بفواتير سابقة.');
        }

        // إذا لم يكن لديه أي تعاملات، يتم حذفه نهائياً لتنظيف قاعدة البيانات
        $supplier->forceDelete();
        return redirect()->back()->with('success', 'تم حذف بيانات المورد نهائياً لعدم وجود تعاملات مالية سابقة معه.');
    }
}