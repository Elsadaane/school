<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudentContract;
use App\Models\StudentContractItem;
use App\Models\StudentInstallment;
use App\Models\Payment; 
use App\Models\FinancialAccount; 
use App\Models\Student;
use App\Models\AcademicYear;
use App\Models\Product; 
use App\Models\Branche;
use App\Models\JournalEntry;
use App\Models\JournalItem;
use App\Models\Bus;
use App\Models\TransportRoute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentContractController extends Controller
{
    // ==========================================
    // 1. عرض قائمة العقود مع الفلترة
    // ==========================================
    public function index(Request $request)
    {
        abort_if(!auth()->user()->can('manage_student_contracts'), 403, 'غير مصرح لك بإدارة عقود الطلاب.');

        $academicYears = AcademicYear::latest()->get();
        $grades = \App\Models\Grade::all(); 
        $sections = \App\Models\Section::all();

        $query = StudentContract::with(['student.parent', 'academicYear', 'transportRoute', 'morningBus', 'afternoonBus'])->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('contract_number', 'like', "%{$search}%")
                  ->orWhereHas('student', function ($studentQuery) use ($search) {
                      $studentQuery->where('first_name', 'like', "%{$search}%")
                        ->orWhere('tribe', 'like', "%{$search}%")
                        // ✅ تم الإصلاح: البحث عن الرقم المدني في جدول ولي الأمر
                        ->orWhereHas('parent', function($parentQuery) use ($search) {
                            $parentQuery->where('national_id', 'like', "%{$search}%");
                        });
                  });
            });
        }

        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        if ($request->filled('grade_id')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('grade_id', $request->grade_id);
            });
        }

        if ($request->filled('section_id')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('section_id', $request->section_id);
            });
        }

        $contracts = $query->paginate(15)->withQueryString();

        return view('backend.student_contracts.index', compact('contracts', 'academicYears', 'grades', 'sections'));
    }

    // ==========================================
    // 2. عرض صفحة الإضافة
    // ==========================================
    public function create(Request $request)
    {
        abort_if(!auth()->user()->can('manage_student_contracts'), 403);

        $branches = Branche::where('is_active', 1)->get(); 
        $students = Student::where('status', 'active')->with('parent', 'grade', 'section')->get();
        $academicYears = AcademicYear::all();
        $feeItems = Product::where('is_active', true)->get();
        $financialAccounts = FinancialAccount::where('account_type', 'asset')->where('is_active', 1)->get();

        // 🆕 جلب بيانات النقل من العقد السابق إذا طُلب النقل
        $transportData = [];
        if ($request->has('copy_transport') && $request->copy_transport) {
            $oldContract = StudentContract::with(['transportRoute', 'morningBus', 'afternoonBus'])
                ->find($request->copy_transport);
            
            if ($oldContract) {
                $transportData = [
                    'transport_route_id' => $oldContract->transport_route_id,
                    'morning_bus_id' => $oldContract->morning_bus_id,
                    'afternoon_bus_id' => $oldContract->afternoon_bus_id,
                ];
            }
        }

        return view('backend.student_contracts.create', compact(
            'branches', 'students', 'academicYears', 'feeItems', 'financialAccounts', 'transportData'
        ));
    }

    // ==========================================
    // 3. حفظ العقد
    // ==========================================
    public function store(Request $request)
    {
        abort_if(!auth()->user()->can('manage_student_contracts'), 403);

        $request->validate([
            'student_id'             => 'required|exists:students,id',
            'academic_year_id'       => 'required|exists:academic_years,id',
            'branch_id'              => 'required|exists:branches,id',
            'contract_date'          => 'required|date',
            'transport_route_id'     => 'nullable|exists:transport_routes,id',
            'morning_bus_id'         => 'nullable|exists:buses,id',
            'afternoon_bus_id'       => 'nullable|exists:buses,id',
            'sub_total'              => 'required|numeric|min:0',
            'discount'               => 'required|numeric|min:0',
            'tax_amount'             => 'required|numeric|min:0',
            'grand_total'            => 'required|numeric|min:0',
            'down_payment'           => 'nullable|numeric|min:0',
            'financial_account_id'   => $request->down_payment > 0 ? 'required|exists:financial_accounts,id' : 'nullable|exists:financial_accounts,id',
            'items'                  => 'required|array|min:1',
            'items.*.unit_price'     => 'required|numeric|min:0', 
            'items.*.quantity'       => 'required|integer|min:1', 
            'installments'           => 'nullable|array', 
            'installments.*.amount'  => 'required_with:installments|numeric|min:1',
            'notes'                  => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            // 🚌 التحقق من سعة الباصات
            if ($request->filled('morning_bus_id')) {
                $morningBus = Bus::lockForUpdate()->find($request->morning_bus_id);
                if ($morningBus && $morningBus->is_full_morning) {
                    throw new \Exception("عفواً، لا توجد مقاعد شاغرة للذهاب (صباحاً) في الحافلة رقم ({$morningBus->bus_number}). المقاعد المتاحة: 0 من {$morningBus->capacity}");
                }
            }

            if ($request->filled('afternoon_bus_id')) {
                $afternoonBus = Bus::lockForUpdate()->find($request->afternoon_bus_id);
                if ($afternoonBus && $afternoonBus->is_full_afternoon) {
                    throw new \Exception("عفواً، لا توجد مقاعد شاغرة للعودة (مساءً) في الحافلة رقم ({$afternoonBus->bus_number}). المقاعد المتاحة: 0 من {$afternoonBus->capacity}");
                }
            }

            $downPayment = $request->down_payment ?? 0;

            $contract = StudentContract::create([
                'contract_number'      => 'CON-' . date('Y') . '-' . time() . rand(10,99),
                'branch_id'            => $request->branch_id,
                'student_id'           => $request->student_id,
                'academic_year_id'     => $request->academic_year_id,
                'transport_route_id'   => $request->transport_route_id,
                'morning_bus_id'       => $request->morning_bus_id,
                'afternoon_bus_id'     => $request->afternoon_bus_id,
                'contract_date'        => $request->contract_date,
                'sub_total'            => $request->sub_total,
                'discount'             => $request->discount,
                'total_after_discount' => $request->sub_total - $request->discount,
                'tax_amount'           => $request->tax_amount,
                'grand_total'          => $request->grand_total,
                'paid_amount'          => $downPayment, 
                'total_remaining'      => $request->grand_total - $downPayment,
                'financial_status'     => $downPayment >= $request->grand_total ? 'fully_paid' : ($downPayment > 0 ? 'partially_paid' : 'unpaid'),
                'notes'                => $request->notes,
            ]);

            // 🔄 حفظ بنود الفاتورة
            foreach ($request->items as $key => $item) {
                $isBusRow = ($key === 'bus_row');
                
                StudentContractItem::create([
                    'student_contract_id' => $contract->id,
                    'product_id'          => $isBusRow ? null : ($item['product_id'] ?: null),
                    'unit_price'          => $item['unit_price'],
                    'quantity'            => $item['quantity'],
                    'tax_percent'         => $item['tax_percent'] ?? 0,
                    'row_total'           => $item['row_total'],
                ]);

                if (!$isBusRow && !empty($item['product_id'])) {
                    $product = Product::lockForUpdate()->find($item['product_id']);
                    if ($product && $product->type === 'product') {
                        if ($product->quantity < $item['quantity']) {
                            throw new \Exception("الكمية المتوفرة من '{$product->name_ar}' لا تكفي!");
                        }
                        $product->decrement('quantity', $item['quantity']);
                        $product->increment('sold_quantity', $item['quantity']); 
                    }
                }
            }

            // حفظ الدفعة المقدمة
            if ($downPayment > 0) {
                $dpInstallment = StudentInstallment::create([
                    'student_contract_id' => $contract->id,
                    'student_id'          => $contract->student_id,
                    'amount'              => $downPayment,
                    'due_date'            => date('Y-m-d'),
                    'paid_amount'         => $downPayment,
                    'remaining_amount'    => 0, 
                    'status'              => 'paid', 
                    'paid_date'           => date('Y-m-d'),
                ]);

                $payment = Payment::create([
                    'financial_account_id' => $request->financial_account_id,
                    'receipt_number'       => 'REC-DP-' . time(),
                    'user_id'              => auth()->id() ?? 1,
                    'payment_type'         => 'advance', 
                    'payable_type'         => get_class($dpInstallment), 
                    'payable_id'           => $dpInstallment->id,
                    'amount'               => $downPayment,
                    'payment_date'         => date('Y-m-d'),
                    'transaction_details'  => 'سداد دفعة مقدمة نقداً',
                ]);
                
                $account = FinancialAccount::lockForUpdate()->find($request->financial_account_id);
                if($account) $account->increment('balance', $downPayment);
                
                $this->createJournalEntry($payment, $downPayment, $request->branch_id, 'دفعة مقدمة - عقد: ' . $contract->student->first_name, $request->financial_account_id);
            }

            // حفظ الأقساط
            if ($request->has('installments')) {
                foreach ($request->installments as $installment) {
                    StudentInstallment::create([
                        'student_contract_id' => $contract->id,
                        'student_id'          => $contract->student_id,
                        'amount'              => $installment['amount'],
                        'due_date'            => $installment['due_date'],
                        'paid_amount'         => 0,
                        'remaining_amount'    => $installment['amount'], 
                        'status'              => 'unpaid', 
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.student-contracts.index')->with('success', 'تم إنشاء العقد وتحديث الخزنة بنجاح.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    // ==========================================
    // 4. عرض صفحة التعديل
    // ==========================================
    public function edit($id)
    {
        abort_if(!auth()->user()->can('manage_student_contracts'), 403);

        $contract = StudentContract::with(['items.product', 'installments', 'transportRoute', 'morningBus', 'afternoonBus'])->findOrFail($id);

        if ($contract->paid_amount > 0) {
            return redirect()->route('admin.student-contracts.index')
                ->withErrors(['error' => 'عفواً، لا يمكن تعديل عقد تم الدفع عليه! يرجى عمل تسوية مالية أو إلغاء العقد وإصدار عقد جديد.']);
        }

        $branches = Branche::all(); 
        $students = Student::where('status', 'active')->with('parent', 'grade', 'section')->get();
        $academicYears = AcademicYear::all();
        $feeItems = Product::where('is_active', true)->get();
        $financialAccounts = FinancialAccount::where('account_type', 'asset')->where('is_active', 1)->get();

        return view('backend.student_contracts.edit', compact('contract', 'branches', 'students', 'academicYears', 'feeItems', 'financialAccounts'));
    }

    // ==========================================
    // 5. تحديث العقد 
    // ==========================================
    public function update(Request $request, $id)
    {
        abort_if(!auth()->user()->can('manage_student_contracts'), 403);

        $contract = StudentContract::with('items', 'installments')->findOrFail($id);

        if ($contract->paid_amount > 0) {
            return redirect()->back()->withErrors(['error' => 'لا يمكن تعديل عقد تم الدفع عليه بالفعل!']);
        }

        $request->validate([
            'contract_date'          => 'required|date',
            'transport_route_id'     => 'nullable|exists:transport_routes,id',
            'morning_bus_id'         => 'nullable|exists:buses,id',
            'afternoon_bus_id'       => 'nullable|exists:buses,id',
            'sub_total'              => 'required|numeric|min:0',
            'discount'               => 'required|numeric|min:0',
            'tax_amount'             => 'required|numeric|min:0',
            'grand_total'            => 'required|numeric|min:0',
            'down_payment'           => 'nullable|numeric|min:0',
            'financial_account_id'   => $request->down_payment > 0 ? 'required|exists:financial_accounts,id' : 'nullable|exists:financial_accounts,id',
            'items'                  => 'required|array|min:1',
            'items.*.unit_price'     => 'required|numeric|min:0',
            'items.*.quantity'       => 'required|integer|min:1',
            'installments'           => 'nullable|array',
            'notes'                  => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            // 🚌 التحقق من سعة الباصات الجديدة فقط إذا تم تغييرها
            if ($request->filled('morning_bus_id') && $contract->morning_bus_id != $request->morning_bus_id) {
                $bus = Bus::lockForUpdate()->find($request->morning_bus_id);
                if ($bus && $bus->is_full_morning) {
                    throw new \Exception("عفواً، لا توجد مقاعد شاغرة للذهاب (صباحاً) في الحافلة رقم ({$bus->bus_number}).");
                }
            }

            if ($request->filled('afternoon_bus_id') && $contract->afternoon_bus_id != $request->afternoon_bus_id) {
                $bus = Bus::lockForUpdate()->find($request->afternoon_bus_id);
                if ($bus && $bus->is_full_afternoon) {
                    throw new \Exception("عفواً، لا توجد مقاعد شاغرة للعودة (مساءً) في الحافلة رقم ({$bus->bus_number}).");
                }
            }

            // استرجاع المخزون القديم
            foreach ($contract->items as $oldItem) {
                if ($oldItem->product_id) {
                    $product = Product::lockForUpdate()->find($oldItem->product_id);
                    if ($product && $product->type === 'product') {
                        $product->increment('quantity', $oldItem->quantity);
                        $product->decrement('sold_quantity', $oldItem->quantity);
                    }
                }
            }

            $contract->items()->delete();
            $contract->installments()->delete();

            $downPayment = $request->down_payment ?? 0;

            $contract->update([
                'contract_date'        => $request->contract_date,
                'transport_route_id'   => $request->transport_route_id,
                'morning_bus_id'       => $request->morning_bus_id,
                'afternoon_bus_id'     => $request->afternoon_bus_id,
                'sub_total'            => $request->sub_total,
                'discount'             => $request->discount,
                'total_after_discount' => $request->sub_total - $request->discount,
                'tax_amount'           => $request->tax_amount,
                'grand_total'          => $request->grand_total,
                'paid_amount'          => $downPayment,
                'total_remaining'      => $request->grand_total - $downPayment,
                'financial_status'     => $downPayment >= $request->grand_total ? 'fully_paid' : ($downPayment > 0 ? 'partially_paid' : 'unpaid'),
                'notes'                => $request->notes, 
            ]);

            // حفظ البنود الجديدة
            foreach ($request->items as $key => $item) {
                $isBusRow = ($key === 'bus_row');
                StudentContractItem::create([
                    'student_contract_id' => $contract->id,
                    'product_id'          => $isBusRow ? null : ($item['product_id'] ?: null),
                    'unit_price'          => $item['unit_price'],
                    'quantity'            => $item['quantity'],
                    'tax_percent'         => $item['tax_percent'] ?? 0,
                    'row_total'           => $item['row_total'],
                ]);

                if (!$isBusRow && !empty($item['product_id'])) {
                    $product = Product::lockForUpdate()->find($item['product_id']);
                    if ($product && $product->type === 'product') {
                        if ($product->quantity < $item['quantity']) {
                            throw new \Exception("الكمية المتوفرة من '{$product->name_ar}' لا تكفي!");
                        }
                        $product->decrement('quantity', $item['quantity']);
                        $product->increment('sold_quantity', $item['quantity']); 
                    }
                }
            }

            // حفظ الدفعة المقدمة
            if ($downPayment > 0) {
                $dpInstallment = StudentInstallment::create([
                    'student_contract_id' => $contract->id,
                    'student_id'          => $contract->student_id,
                    'amount'              => $downPayment,
                    'due_date'            => date('Y-m-d'),
                    'paid_amount'         => $downPayment,
                    'remaining_amount'    => 0, 
                    'status'              => 'paid', 
                    'paid_date'           => date('Y-m-d'),
                ]);

                $payment = Payment::create([
                    'financial_account_id' => $request->financial_account_id,
                    'receipt_number'       => 'REC-DP-UP-' . time(),
                    'user_id'              => auth()->id() ?? 1,
                    'payment_type'         => 'advance', 
                    'payable_type'         => get_class($dpInstallment),
                    'payable_id'           => $dpInstallment->id,
                    'amount'               => $downPayment,
                    'payment_date'         => date('Y-m-d'),
                    'transaction_details'  => 'سداد دفعة مقدمة (تعديل عقد)',
                ]);
                
                $account = FinancialAccount::lockForUpdate()->find($request->financial_account_id);
                if($account) $account->increment('balance', $downPayment);
                
                $this->createJournalEntry($payment, $downPayment, $contract->branch_id, 'تعديل عقد - دفعة مقدمة: ' . $contract->student->first_name, $request->financial_account_id);
            }

            // حفظ الأقساط
            if ($request->has('installments')) {
                foreach ($request->installments as $installment) {
                    StudentInstallment::create([
                        'student_contract_id' => $contract->id,
                        'student_id'          => $contract->student_id,
                        'amount'              => $installment['amount'],
                        'due_date'            => $installment['due_date'],
                        'paid_amount'         => 0,
                        'remaining_amount'    => $installment['amount'], 
                        'status'              => 'unpaid', 
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.student-contracts.index')->with('success', 'تم التعديل وتحديث المخازن والقيود بنجاح.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    // ==========================================
    // 6. الحذف النهائي 
    // ==========================================
    public function destroy($id)
    {
        abort_if(!auth()->user()->can('manage_student_contracts'), 403);

        $contract = StudentContract::with('items')->findOrFail($id);

        if ($contract->paid_amount > 0) {
            return redirect()->back()->withErrors(['error' => 'مرفوض أمنياً: لا يمكن حذف عقد تم استلام دفعات مالية عليه.']);
        }

        try {
            DB::beginTransaction();

            foreach ($contract->items as $oldItem) {
                if ($oldItem->product_id) {
                    $product = Product::lockForUpdate()->find($oldItem->product_id);
                    if ($product && $product->type === 'product') {
                        $product->increment('quantity', $oldItem->quantity);
                        $product->decrement('sold_quantity', $oldItem->quantity);
                    }
                }
            }

            $contract->delete();

            DB::commit();
            return redirect()->back()->with('success', 'تم حذف العقد واسترجاع المنتجات المخزنية بنجاح.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors(['error' => 'خطأ أثناء الحذف: ' . $e->getMessage()]);
        }
    }

    // ==============================================================
    // 7. دوال الأجاكس لجلب خطوط السير والباصات 🔥
    // ==============================================================
    
    public function getBranchRoutes(Request $request)
    {
        abort_if(!auth()->user()->can('manage_student_contracts'), 403);

        $routes = TransportRoute::where('branch_id', $request->branch_id)
            ->select('id', 'name', 'monthly_fare_both', 'monthly_fare_single') 
            ->get();

        return response()->json($routes);
    }

    public function getRouteBuses(Request $request)
    {
        abort_if(!auth()->user()->can('manage_student_contracts'), 403);

        $route = TransportRoute::find($request->route_id);
        $fareBoth = $route ? $route->monthly_fare_both : 0;
        $fareSingle = $route ? $route->monthly_fare_single : 0;
        $currentYearId = AcademicYear::where('is_current', true)->value('id');

        // ✅ تحسين الأداء: استخدام withCount بدلاً من الـ Accessors
        $buses = Bus::where('transport_route_id', $request->route_id)
            ->withCount([
                'morningContracts as morning_booked_count' => function ($query) use ($currentYearId) {
                    $query->whereIn('contract_status', ['active', 'draft'])
                          ->where('academic_year_id', $currentYearId);
                },
                'afternoonContracts as afternoon_booked_count' => function ($query) use ($currentYearId) {
                    $query->whereIn('contract_status', ['active', 'draft'])
                          ->where('academic_year_id', $currentYearId);
                }
            ])
            ->get()
            ->map(function($bus) use ($fareBoth, $fareSingle) {
                $availableMorning = max($bus->capacity - $bus->morning_booked_count, 0);
                $availableAfternoon = max($bus->capacity - $bus->afternoon_booked_count, 0);
                
                return [
                    'id'                  => $bus->id,
                    'bus_number'          => $bus->bus_number,
                    'capacity'            => $bus->capacity,
                    'fare_both'           => $fareBoth,
                    'fare_single'         => $fareSingle,
                    'available_morning'   => $availableMorning,
                    'available_afternoon' => $availableAfternoon,
                    'is_full_morning'     => $availableMorning <= 0,
                    'is_full_afternoon'   => $availableAfternoon <= 0,
                    'morning_booked'      => $bus->morning_booked_count,
                    'afternoon_booked'    => $bus->afternoon_booked_count,
                ];
            });

        return response()->json($buses);
    }

    // ==============================================================
    // 8. عرض الطباعة (Show / Print)
    // ==============================================================
    public function show($id)
    {
        abort_if(!auth()->user()->can('manage_student_contracts'), 403);
        $contract = StudentContract::with(['student.parent', 'student.grade', 'academicYear', 'installments', 'transportRoute', 'morningBus.driver', 'afternoonBus.driver', 'items.product'])->findOrFail($id);
        return view('backend.student_contracts.print', compact('contract'));
    }

    // ==============================================================
    // 15. طباعة سند القبض (Payment Receipt)
    // ==============================================================
    public function printReceipt($payment_id)
    {
        abort_if(!auth()->user()->can('manage_payments'), 403);
        $payment = \App\Models\Payment::with(['financialAccount', 'user'])->findOrFail($payment_id);
        
        $installment = null;
        $contract = null;
        $student = null;
        
        if ($payment->payable_type === \App\Models\StudentInstallment::class) {
            $installment = \App\Models\StudentInstallment::with('contract.student.parent')->find($payment->payable_id);
            if ($installment) {
                $contract = $installment->contract;
                $student = $contract->student;
            }
        }

        return view('backend.student_contracts.receipt', compact('payment', 'installment', 'contract', 'student'));
    }

    // ==============================================================
    // 9. التوقيع الإلكتروني (Signature)
    // ==============================================================
    public function signaturePage($id)
    {
        abort_if(!auth()->user()->can('manage_student_contracts'), 403);
        $contract = StudentContract::with(['student.parent', 'items.product', 'installments', 'transportRoute', 'morningBus', 'afternoonBus'])->findOrFail($id);
        return view('backend.student_contracts.signature', compact('contract'));
    }

    public function saveSignature(Request $request, $id)
    {
        abort_if(!auth()->user()->can('manage_student_contracts'), 403);

        $request->validate([
            'signature_data' => 'required|string'
        ]);

        $contract = StudentContract::findOrFail($id);
        
        $contract->update([
            'guardian_signature' => $request->signature_data, 
            'signed_at'          => now(),
            'contract_status'    => 'active', 
        ]);

        return redirect()->route('admin.student-contracts.index')->with('success', 'تم حفظ توقيع ولي الأمر وتفعيل العقد بنجاح.');
    }

    // ==============================================================
    // 10. عرض المدفوعات (Payments Page)
    // ==============================================================
    public function showPayments($id)
    {
        abort_if(!auth()->user()->can('manage_payments'), 403);

        $contract = StudentContract::with([
            'student.parent', 
            'student.grade', 
            'student.branch', 
            'transportRoute',
            'morningBus.driver',
            'afternoonBus.driver',
            'installments.payments'
        ])->findOrFail($id);
        
        $financialAccounts = FinancialAccount::where('account_type', 'asset')->where('is_active', 1)->get();

        return view('backend.student_contracts.payments', compact('contract', 'financialAccounts'));
    }

    // ==============================================================
    // 11. تنفيذ الدفع الجزئي أو الكلي للقسط
    // ==============================================================
    public function payInstallment(Request $request, $id)
    {
        abort_if(!auth()->user()->can('manage_payments'), 403);

        $request->validate([
            'amount_paid'          => 'required|numeric|min:0.1',
            'financial_account_id' => 'required|exists:financial_accounts,id',
            'check_number'         => 'nullable|string|max:255',
        ]);

        $installment = StudentInstallment::findOrFail($id);
        $contract = $installment->contract;
        $student = $contract->student;
        $remaining = round($installment->amount - $installment->paid_amount, 3);

        if (round($request->amount_paid, 3) > $remaining) {
            return redirect()->back()->withErrors(['amount_paid' => 'المبلغ المدفوع أكبر من المتبقي من هذا القسط!']);
        }

        $accountId = $request->financial_account_id;

        try {
            DB::beginTransaction();

            $payment = Payment::create([
                'financial_account_id' => $accountId,
                'receipt_number'       => 'REC-' . strtoupper(uniqid()), 
                'user_id'              => auth()->id() ?? 1,
                'payment_type'         => 'installment',
                'payable_type'         => get_class($installment),
                'payable_id'           => $installment->id,
                'amount'               => $request->amount_paid,
                'payment_date'         => now(),
                'check_number'         => $request->check_number,
                'transaction_details'  => 'سداد قسط دراسي للطالب',
            ]);

            $account = FinancialAccount::lockForUpdate()->find($accountId);
            if($account) $account->increment('balance', $request->amount_paid);

            $newPaidAmount = $installment->paid_amount + $request->amount_paid;
            $isPaid = (round($newPaidAmount, 3) >= round($installment->amount, 3));

            $installment->update([
                'paid_amount'      => $newPaidAmount,
                'remaining_amount' => $installment->amount - $newPaidAmount,
                'status'           => $isPaid ? 'paid' : 'partial', 
                'paid_date'        => $isPaid ? now() : null,
            ]);

            $contract->increment('paid_amount', $request->amount_paid);
            $contract->decrement('total_remaining', $request->amount_paid);
            
            $contractStatus = 'partially_paid';
            if (round($contract->total_remaining, 3) <= 0) {
                $contractStatus = 'fully_paid';
            }
            $contract->update(['financial_status' => $contractStatus]);
            
            $this->createJournalEntry($payment, $request->amount_paid, $contract->branch_id, 'سداد قسط دراسي للطالب: ' . $student->first_name, $accountId);

            DB::commit();
            return redirect()->back()->with('success', 'تم تسجيل الدفعة وإيداعها بالحساب المالي بنجاح.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors(['error' => 'حدث خطأ: ' . $e->getMessage()]);
        }
    }
    
    // ==============================================================
    // 12. دالة مساعدة لتسجيل القيود المحاسبية
    // ==============================================================
    private function createJournalEntry($payment, $amount, $branchId, $description, $debitAccountId)
    {
        if(class_exists('\App\Models\JournalEntry') && class_exists('\App\Models\JournalItem')) {
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
                'line_description'     => 'إيداع بالحساب',
            ]);

            $revenueAccount = FinancialAccount::where('account_type', 'revenue')->first();
            $creditAccountId = $revenueAccount ? $revenueAccount->id : 2; 

            JournalItem::create([
                'journal_entry_id'     => $journalEntry->id,
                'financial_account_id' => $creditAccountId, 
                'debit'                => 0,
                'credit'               => $amount, 
                'line_description'     => 'إثبات إيراد رسوم طلاب',
            ]);
        }
    }

    // ==============================================================
    // 13. إضافة باص سريع 🔥
    // ==============================================================
    public function quickAddBus(Request $request)
    {
        abort_if(!auth()->user()->can('manage_student_contracts'), 403);

        $request->validate([
            'branch_id'          => 'required|exists:branches,id',
            'academic_year_id'   => 'required|exists:academic_years,id',
            'transport_route_id' => 'required|exists:transport_routes,id',
            'bus_number'         => 'required|string|max:50',
            'capacity'           => 'required|integer|min:1',
        ]);

        $bus = Bus::create($request->all());

        return response()->json([
            'status' => 'success',
            'bus'    => $bus
        ]);
    }

    // ==============================================================
    // 14. إضافة رسوم/خدمة سريعة
    // ==============================================================
    public function quickAddFee(Request $request)
    {
        abort_if(!auth()->user()->can('manage_student_contracts'), 403);

        $request->validate([
            'branch_id'     => 'required|exists:branches,id',
            'type'          => 'required|in:service,product',
            'sku'           => 'nullable|string|max:100',
            'name_ar'       => 'required|string|max:255',
            'name_en'       => 'nullable|string|max:255',
            'cost_price'    => 'nullable|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'tax_percent'   => 'nullable|numeric|min:0|max:100',
        ]);

        $fee = Product::create([
            'branch_id'     => $request->branch_id,
            'type'          => $request->type,
            'sku'           => $request->sku ?? 'PRD-' . time(), 
            'name_ar'       => $request->name_ar,
            'name_en'       => $request->name_en,
            'cost_price'    => $request->cost_price ?? 0,
            'selling_price' => $request->selling_price,
            'tax_percent'   => $request->tax_percent ?? 0,
            'is_active'     => 1,
            'quantity'      => 0, 
        ]);

        return response()->json([
            'status' => 'success',
            'fee'    => $fee
        ]);
    }

    // ==============================================================
    // 16. عرض الطلاب المعلقين 
    // ==============================================================
    public function pendingRenewals(Request $request)
    {
        abort_if(!auth()->user()->can('manage_student_contracts'), 403);

        $academicYears = AcademicYear::latest()->get();
        $branches = Branche::where('is_active', 1)->get(); // 🆕 للفلاتر
        $targetYearId = $request->academic_year_id ?? AcademicYear::where('is_current', true)->value('id');

        $query = Student::with(['grade', 'parent', 'branch', 'section', 'contracts' => function($q) {
            $q->select('id', 'student_id', 'academic_year_id', 'paid_amount', 'total_remaining', 'contract_status', 'transport_route_id', 'morning_bus_id', 'afternoon_bus_id'); 
        }])->where('status', 'active'); 

        if ($targetYearId) {
            $query->where('academic_year_id', $targetYearId);

            $query->where(function($q) use ($targetYearId) {
                $q->whereDoesntHave('contracts', function($cq) use ($targetYearId) {
                    $cq->where('academic_year_id', $targetYearId);
                })
                ->orWhereHas('contracts', function($cq) use ($targetYearId) {
                    $cq->where('academic_year_id', $targetYearId)
                       ->where('paid_amount', '<=', 0); 
                });
            });
        }

        // 🆕 فلتر الفرع
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        // 🆕 فلتر نوع النقل
        if ($request->filled('transport_type')) {
            $query->where('transport_type', $request->transport_type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('tribe', 'like', "%{$search}%")
                  // ✅ تم الإصلاح: البحث عن الرقم المدني في جدول ولي الأمر
                  ->orWhereHas('parent', function($parentQuery) use ($search) {
                      $parentQuery->where('national_id', 'like', "%{$search}%");
                  });
            });
        }

        $students = $query->paginate(20)->withQueryString();

        $students->getCollection()->transform(function ($student) use ($targetYearId) {
            $student->total_old_debt = $student->contracts
                ->where('academic_year_id', '!=', $targetYearId)
                ->sum('total_remaining');
            return $student;
        });

        return view('backend.student_contracts.pending_renewals', compact('students', 'academicYears', 'targetYearId', 'branches'));
    }
}