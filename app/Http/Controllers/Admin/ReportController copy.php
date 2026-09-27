<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Student;
use App\Models\StudentContract;
use App\Models\StoreInvoice;
use App\Models\PurchaseInvoice;
use App\Models\Expense;
use App\Models\Income;
use App\Models\HrPayroll;
use App\Models\Product;

class ReportController extends Controller
{
    // ==========================================
    // 🔥 1. التقرير المالي للطلاب (Students Financial Report) 🔥
    // ==========================================
    public function studentFinancialReports(Request $request)
    {
        abort_if(!auth()->user()->can('view_reports'), 403, 'غير مصرح لك بالاطلاع على التقارير المالية.');

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $students = Student::query()
            ->with(['grade', 'parent'])
            ->withSum(['contracts as total_contracts_value' => function ($q) use ($startDate, $endDate) {
                if ($startDate) $q->whereDate('contract_date', '>=', $startDate);
                if ($endDate) $q->whereDate('contract_date', '<=', $endDate);
            }], 'grand_total')
            ->withSum(['contracts as total_paid' => function ($q) use ($startDate, $endDate) {
                if ($startDate) $q->whereDate('contract_date', '>=', $startDate);
                if ($endDate) $q->whereDate('contract_date', '<=', $endDate);
            }], 'paid_amount')
            ->get();

        // أكثر الطلاب مديونية (Top Debtors)
        $topDebtors = $students->map(function($s) {
            return [
                'name' => $s->first_name . ' ' . $s->tribe,
                'grade' => $s->grade->name_ar ?? '-',
                'parent_phone' => $s->parent->phone ?? '',
                'debt' => ($s->total_contracts_value ?? 0) - ($s->total_paid ?? 0)
            ];
        })->where('debt', '>', 0)->sortByDesc('debt')->take(10)->values();

        return view('backend.reports.students_financial', compact('students', 'startDate', 'endDate', 'topDebtors'));
    }
    // تم

    // ==========================================
    // 🔥 2. شاشة المركز المالي الشامل للمدرسة (Overall P&L) 🔥
    // ==========================================
    public function overallReports(Request $request)
    {
        abort_if(!auth()->user()->can('view_reports'), 403);

        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfMonth();

        // 💰 الإيرادات
        $studentPayments = DB::table('installment_payments')
            ->whereBetween('payment_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->sum('amount_paid'); 

        $storeSales = DB::table('store_invoices')
            ->whereNull('deleted_at')
            ->whereBetween('invoice_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->sum('grand_total');

        $otherIncomes = DB::table('incomes')
            ->whereNull('deleted_at')
            ->whereBetween('income_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->sum('total_amount');

        $totalRevenue = $studentPayments + $storeSales + $otherIncomes;

        // 💸 المصروفات
        $purchases = DB::table('purchase_invoices')
            ->whereNull('deleted_at')
            ->whereBetween('invoice_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->sum('grand_total');

        $payrolls = DB::table('hr_payrolls')
            ->where('status', 'paid') 
            ->whereBetween('payment_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->sum('net_salary');

        $generalExpenses = DB::table('expenses')
            ->whereNull('deleted_at')
            ->whereBetween('expense_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->sum('total_amount');

        $totalExpenses = $purchases + $payrolls + $generalExpenses;
        $netProfit = $totalRevenue - $totalExpenses;

        return view('backend.reports.overall', compact(
            'startDate', 'endDate',
            'studentPayments', 'storeSales', 'otherIncomes', 'totalRevenue',
            'purchases', 'payrolls', 'generalExpenses', 'totalExpenses',
            'netProfit'
        ));
    }
    // تم

    // ==========================================
    // 🔥 3. تقرير أداء الفروع الشامل (Branch Performance) 🔥
    // ==========================================
    public function branchPerformance(Request $request)
    {
        abort_if(!auth()->user()->can('view_reports'), 403);

        $user = auth()->user();
        $isAdmin = $user->hasRole('Admin') || $user->id === 1;

        if ($isAdmin) {
            $branches = \App\Models\Branche::all();
            $branchId = $request->input('branch_id', 'all'); 
        } else {
            $employee = \App\Models\HrEmployee::where('user_id', $user->id)->first();
            if (!$employee || !$employee->branch_id) abort(403, 'حسابك غير مربوط بفرع محدد.');
            
            $branches = \App\Models\Branche::where('id', $employee->branch_id)->get();
            $branchId = $employee->branch_id; 
        }

        $startDate = $request->input('from_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('to_date', Carbon::now()->endOfDay()->format('Y-m-d'));

        $branchesComparison = collect();
        
        foreach ($branches as $b) {
            $sales = DB::table('store_invoices')->where('branch_id', $b->id)->whereBetween('invoice_date', [$startDate, $endDate])->whereNull('deleted_at')->sum('grand_total');
            $incomes = DB::table('incomes')->where('branch_id', $b->id)->whereBetween('income_date', [$startDate, $endDate])->whereNull('deleted_at')->sum('total_amount');
            $expenses = DB::table('expenses')->where('branch_id', $b->id)->whereBetween('expense_date', [$startDate, $endDate])->whereNull('deleted_at')->sum('total_amount');

            $totalRev = $sales + $incomes;
            $branchesComparison->push([
                'id'       => $b->id,
                'name'     => $b->name_ar,
                'revenue'  => $totalRev,
                'expenses' => $expenses,
                'net'      => $totalRev - $expenses
            ]);
        }

        $branchesComparison = $branchesComparison->sortByDesc('net')->values();
        $topBranch = $branchesComparison->first(); 

        return view('backend.reports.branch_performance', compact('branches', 'branchesComparison', 'topBranch', 'startDate', 'endDate', 'branchId', 'isAdmin'));
    }

    // ==========================================
    // 🔥 4. تقرير الموارد البشرية والرواتب (HR Reports) 🔥
    // ==========================================
    public function hrReports(Request $request)
    {
        abort_if(!auth()->user()->can('view_reports'), 403);

        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));
        $departmentId = $request->input('department_id');

        $query = \App\Models\HrEmployee::with('department')->whereNull('deleted_at');
        if ($departmentId && $departmentId != 'all') {
            $query->where('hr_department_id', $departmentId);
        }
        $employees = $query->get();

        $payrolls = \App\Models\HrPayroll::where('month', $month)
            ->where('year', $year)
            ->whereIn('employee_id', $employees->pluck('id'))
            ->get();

        $activeLoans = \App\Models\HrLoan::whereIn('employee_id', $employees->pluck('id'))->where('status', 'active')->get();
        $remainingLoans = $activeLoans->sum('loan_amount') - $activeLoans->sum('paid_amount');

        $kpis = [
            'active_employees' => $employees->where('status', 'active')->count(),
            'total_net_salaries' => $payrolls->sum('net_salary'),
            'total_deductions' => $payrolls->sum('total_deductions') + $payrolls->sum('loan_deduction'), 
            'remaining_loans' => $remainingLoans,
        ];

        $deptData = $employees->groupBy(function($emp) {
            return $emp->department ? $emp->department->name_ar : 'بدون قسم';
        })->map->count();
        
        $deptLabels = $deptData->keys()->toArray();
        $deptCounts = $deptData->values()->toArray();

        $payrollTrend = \App\Models\HrPayroll::selectRaw('month, SUM(net_salary) as total')
            ->where('year', $year)
            ->groupBy('month')
            ->pluck('total', 'month');
        
        $trendData = [];
        for($i = 1; $i <= 12; $i++) {
            $trendData[] = round($payrollTrend[str_pad($i, 2, '0', STR_PAD_LEFT)] ?? 0, 3);
        }

        $departments = \App\Models\HrDepartment::all();

        $employees->map(function($emp) use ($payrolls) {
            $empPayroll = $payrolls->where('employee_id', $emp->id)->first();
            $emp->current_net = $empPayroll ? $empPayroll->net_salary : 0;
            $emp->current_allowances = $empPayroll ? $empPayroll->total_allowances : 0;
            $emp->current_deductions = $empPayroll ? ($empPayroll->total_deductions + $empPayroll->loan_deduction) : 0;
            $emp->payroll_status = $empPayroll ? $empPayroll->status : 'not_generated';
            return $emp;
        });

        return view('backend.reports.hr', compact(
            'employees', 'departments', 'month', 'year', 'departmentId', 'kpis', 
            'deptLabels', 'deptCounts', 'trendData'
        ));
    }

    // ==========================================
    // 🔥 5. نواقص مخزون الكتب والزي 🔥
    // ==========================================
    public function inventoryShortages(Request $request)
    {
        abort_if(!auth()->user()->can('view_reports'), 403);

        $products = Product::where('is_active', 1)->get();
        
        $shortages = $products->filter(function($p) {
            return $p->quantity <= $p->alert_quantity;
        })->sortBy('quantity')->values();

        $restockCost = $shortages->sum(function($p) {
            $deficit = $p->alert_quantity - $p->quantity; 
            return $deficit > 0 ? ($deficit * $p->cost_price) : 0;
        });

        $kpis = [
            'total_shortages' => $shortages->count(),
            'out_of_stock' => $shortages->where('quantity', '<=', 0)->count(),
            'restock_cost' => $restockCost,
        ];

        return view('backend.reports.inventory_shortages', compact('shortages', 'kpis'));
    }

    // ==========================================
    // 🔥 6. رادار التنبيهات الذكي (Smart Alerts) 🔥
    // ==========================================
    public function smartAlerts()
    {
        abort_if(!auth()->user()->can('view_reports'), 403);

        $today = Carbon::now()->startOfDay();
        $next30Days = Carbon::now()->addDays(30)->endOfDay();

        $expiringDocs = \App\Models\HrEmployeeDocument::with('employee')
            ->where('expiry_date', '<=', $next30Days)
            ->orderBy('expiry_date', 'ASC')->get();

        $lowStock = Product::where('is_active', 1)->whereColumn('quantity', '<=', 'alert_quantity')->get();

        $dueInstallments = \App\Models\StudentInstallment::with(['contract.student'])
            ->whereColumn('paid_amount', '<', 'amount')
            ->where('due_date', '<=', $today)
            ->orderBy('due_date', 'ASC')->get();

        $dueInvoices = PurchaseInvoice::with('supplier')
            ->whereIn('payment_status', ['unpaid', 'partial'])
            ->orderBy('invoice_date', 'ASC')->get();

        $expiringContracts = \App\Models\HrContract::with('employee')
            ->whereIn('status', ['valid', 'expired']) 
            ->whereNotNull('end_date')
            ->where('end_date', '<=', $next30Days)
            ->orderBy('end_date', 'ASC')
            ->get();

        return view('backend.reports.smart_alerts', compact(
            'expiringDocs', 'lowStock', 'dueInstallments', 'dueInvoices', 'expiringContracts', 'today'
        ));
    }
    // تم

    // ==========================================
    // 🔥 7. صرف نثريات سريعة (Petty Cash) 🔥
    // ==========================================
    public function addExpense(Request $request)
    {
        abort_if(!auth()->user()->can('manage_expenses'), 403, 'غير مصرح لك بإضافة مصروفات.');

        $request->validate([
            'amount' => 'required|numeric|min:0.001',
            'expense_category_id' => 'required|exists:expense_categories,id',
            'transaction_details' => 'required|string|max:255',
            'financial_account_id' => 'required|exists:financial_accounts,id',
        ]);

        try {
            DB::beginTransaction();

            Expense::create([
                'expense_code' => 'EXP-Q-' . time(),
                'branch_id' => 1, 
                'expense_category_id' => $request->expense_category_id,
                'financial_account_id' => $request->financial_account_id,
                'user_id' => auth()->id(),
                'amount' => $request->amount,
                'total_amount' => $request->amount,
                'expense_date' => now()->toDateString(),
                'transaction_details' => $request->transaction_details,
            ]);

            // خصم من رصيد الخزنة الفعلي
            $account = \App\Models\FinancialAccount::findOrFail($request->financial_account_id);
            if ($account->balance < $request->amount) {
                throw new \Exception('رصيد الحساب المالي غير كافٍ لسحب هذا المصروف.');
            }
            $account->decrement('balance', $request->amount);

            DB::commit();
            return back()->with('success', 'تم تسجيل المصروف וخصمه من الحساب المالي بنجاح.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    // ==========================================
    // 🔥 8. التقرير الضريبي للإقرارات (VAT Report) 🔥
    // ==========================================
    public function taxReport(Request $request)
    {
        abort_if(!auth()->user()->can('view_reports'), 403);

        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        // Output Tax
        $salesTax = DB::table('store_invoices')->whereNull('deleted_at')->whereBetween('invoice_date', [$startDate, $endDate])->sum('tax_amount');
        $incomesTax = DB::table('incomes')->whereNull('deleted_at')->whereBetween('income_date', [$startDate, $endDate])->sum('tax_amount');
        $contractsTax = DB::table('student_contracts')->whereBetween('contract_date', [$startDate, $endDate])->sum('tax_amount');
        $totalOutputTax = $salesTax + $incomesTax + $contractsTax;

        // Input Tax
        $purchasesTax = DB::table('purchase_invoices')->whereNull('deleted_at')->whereBetween('invoice_date', [$startDate, $endDate])->sum('tax_amount');
        $expensesTax = DB::table('expenses')->whereNull('deleted_at')->whereBetween('expense_date', [$startDate, $endDate])->sum('tax_amount');
        $assetsTax = DB::table('assets')->whereNull('deleted_at')->whereBetween('transaction_date', [$startDate, $endDate])->sum('tax_amount');
        $totalInputTax = $purchasesTax + $expensesTax + $assetsTax;

        $netVat = $totalOutputTax - $totalInputTax;

        return view('backend.reports.tax_report', compact(
            'startDate', 'endDate', 'totalOutputTax', 'totalInputTax', 'netVat',
            'salesTax', 'incomesTax', 'contractsTax', 'purchasesTax', 'expensesTax', 'assetsTax'
        ));
    }

    // ==========================================
    // 🔥 9. كشف حساب ولي الأمر الشامل 🔥
    // ==========================================
    public function guardianStatement(Request $request)
    {
        abort_if(!auth()->user()->can('view_reports'), 403);

        $guardianId = $request->input('guardian_id');
        // 🔥 تحسين الأداء بجلب الحقول الضرورية فقط
        $guardians = \App\Models\Guardian::select('id', 'name_ar', 'name_en', 'phone')->get();
        $statementData = null;

        if ($guardianId) {
            $guardian = \App\Models\Guardian::with(['students.contracts.installments.payments'])->findOrFail($guardianId);
            
            $allContracts = collect();
            $totalDebt = 0;
            $totalPaid = 0;

            foreach ($guardian->students as $student) {
                foreach ($student->contracts as $contract) {
                    $allContracts->push($contract);
                    $totalDebt += $contract->grand_total;
                    $totalPaid += $contract->paid_amount;
                }
            }

            $statementData = [
                'guardian' => $guardian,
                'total_debt' => $totalDebt,
                'total_paid' => $totalPaid,
                'remaining' => $totalDebt - $totalPaid,
                'contracts' => $allContracts
            ];
        }

        return view('backend.reports.guardian_statement', compact('guardians', 'guardianId', 'statementData'));
    }
    // تم


    // ==========================================
    // 🔥 10. تقرير حركة الخزينة وتدفق الأموال (Cash Flow) 🔥
    // ==========================================
    public function cashFlowReport(Request $request)
    {
        abort_if(!auth()->user()->can('view_reports'), 403);

        $accountId = $request->input('financial_account_id');
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $accounts = \App\Models\FinancialAccount::where('is_active', 1)->get();
        $transactions = collect();
        $openingBalance = 0;

        if ($accountId) {
            $account = \App\Models\FinancialAccount::findOrFail($accountId);
            $openingBalance = $account->balance; 

            // 1. الإيرادات العامة
            $incomes = DB::table('incomes')
                ->where('financial_account_id', $accountId)
                ->whereBetween('income_date', [$startDate, $endDate])
                ->select('income_date as date', DB::raw("'سند إيراد' as type"), 'total_amount as inflow', DB::raw("0 as outflow"), 'transaction_details as notes')
                ->get();
            
            // 2. المصروفات العامة
            $expenses = DB::table('expenses')
                ->where('financial_account_id', $accountId)
                ->whereBetween('expense_date', [$startDate, $endDate])
                ->select('expense_date as date', DB::raw("'سند مصروف' as type"), DB::raw("0 as inflow"), 'total_amount as outflow', 'transaction_details as notes')
                ->get();

            // 3. السندات والمدفوعات الشاملة من جدول (payments)
            // هذا الجدول يضم (المشتريات، أقساط الطلاب، مرتجعات) المرتبطة بهذه الخزنة
            $generalPayments = DB::table('payments')
                ->where('financial_account_id', $accountId)
                ->whereBetween('payment_date', [$startDate, $endDate])
                ->select(
                    'payment_date as date', 
                    DB::raw("CASE WHEN amount > 0 THEN 'مقبوضات' ELSE 'مدفوعات' END as type"), 
                    DB::raw("CASE WHEN amount > 0 THEN amount ELSE 0 END as inflow"), 
                    DB::raw("CASE WHEN amount < 0 THEN ABS(amount) ELSE 0 END as outflow"), 
                    'transaction_details as notes'
                )->get();

            // 4. دمج كل الحركات وترتيبها بالتاريخ
            $transactions = $transactions->concat($incomes)
                                         ->concat($expenses)
                                         ->concat($generalPayments)
                                         ->sortBy('date')
                                         ->values();
        }

        return view('backend.reports.cash_flow', compact('accounts', 'accountId', 'startDate', 'endDate', 'transactions', 'openingBalance'));
    }

    // ==========================================
    // 🔥 11. تقرير خصومات الطلاب 🔥
    // ==========================================
    public function discountsReport(Request $request)
    {
        abort_if(!auth()->user()->can('view_reports'), 403);

        $yearId = $request->input('academic_year_id');
        $years = \App\Models\AcademicYear::all();

        $query = StudentContract::with(['student.grade', 'branch'])->where('discount', '>', 0);
        
        if ($yearId) {
            $query->where('academic_year_id', $yearId);
        }

        $discountContracts = $query->orderByDesc('discount')->get();
        $totalDiscounts = $discountContracts->sum('discount');

        return view('backend.reports.discounts_report', compact('years', 'yearId', 'discountContracts', 'totalDiscounts'));
    }

    // ==========================================
    // 🔥 12. تقرير الحضور والغياب الإحصائي 🔥
    // ==========================================
    public function attendanceReport(Request $request)
    {
        abort_if(!auth()->user()->can('view_reports'), 403);

        $startDate = $request->input('start_date', Carbon::now()->startOfWeek()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfWeek()->format('Y-m-d'));
        
        $compareYear1 = $request->input('compare_year1', date('Y'));
        $compareYear2 = $request->input('compare_year2', date('Y') - 1);

        $stats = DB::table('student_attendances')
            ->whereBetween('attendance_date', [$startDate, $endDate])
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $totalRecords = $stats->sum();
        $present = $stats->get('present', 0);
        $absent = $stats->get('absent', 0);
        $late = $stats->get('late', 0);
        $excused = $stats->get('excused', 0);

        $topAbsentees = DB::table('student_attendances')
            ->join('students', 'students.id', '=', 'student_attendances.student_id')
            ->leftJoin('branches', 'branches.id', '=', 'students.branch_id')
            ->whereBetween('student_attendances.attendance_date', [$startDate, $endDate]) 
            ->where('student_attendances.status', 'absent')
            ->select('students.first_name', 'students.tribe', 'branches.name_ar as branch_name', DB::raw('count(student_attendances.id) as absent_days'))
            ->groupBy('students.id', 'students.first_name', 'students.tribe', 'branches.name_ar')
            ->orderByDesc('absent_days')
            ->limit(10)->get();

        $branches = \App\Models\Branche::all();
        $comparisonData = collect();

        foreach ($branches as $branch) {
            $absentY1 = DB::table('student_attendances')
                ->join('students', 'students.id', '=', 'student_attendances.student_id')
                ->where('students.branch_id', $branch->id)
                ->whereYear('student_attendances.attendance_date', $compareYear1)
                ->where('student_attendances.status', 'absent')
                ->count();

            $absentY2 = DB::table('student_attendances')
                ->join('students', 'students.id', '=', 'student_attendances.student_id')
                ->where('students.branch_id', $branch->id)
                ->whereYear('student_attendances.attendance_date', $compareYear2)
                ->where('student_attendances.status', 'absent')
                ->count();

            $difference = 0;
            if ($absentY2 > 0) {
                $difference = (($absentY1 - $absentY2) / $absentY2) * 100;
            } elseif ($absentY1 > 0) {
                $difference = 100;
            }

            $comparisonData->push([
                'branch_name' => $branch->name_ar,
                'absent_y1' => $absentY1,
                'absent_y2' => $absentY2,
                'difference' => $difference
            ]);
        }

        $availableYears = range(date('Y') + 1, 2022);

        return view('backend.reports.attendance_report', compact(
            'startDate', 'endDate', 'present', 'absent', 'late', 'excused', 'totalRecords', 'topAbsentees',
            'compareYear1', 'compareYear2', 'comparisonData', 'availableYears'
        ));
    }

    // ==========================================
    // 🔥 13. تقرير أرباح وإشغال الحافلات 🔥
    // ==========================================
    public function busReport(Request $request)
    {
        abort_if(!auth()->user()->can('view_reports'), 403);

        $buses = \App\Models\Bus::withCount('contracts')->get();

        $buses->map(function($bus) {
            $bus->total_revenue = $bus->contracts_count * ($bus->agreed_price ?? 0);
            return $bus;
        });

        $totalStudentsInBuses = $buses->sum('contracts_count');
        $expectedBusRevenue = $buses->sum('total_revenue');

        return view('backend.reports.bus_report', compact('buses', 'totalStudentsInBuses', 'expectedBusRevenue'));
    }
    // تم

    // ==========================================
    // 🔥 14. تقرير مقارنة الأداء (Year over Year Comparison) 🔥
    // ==========================================
    public function comparisonReport(Request $request)
    {
        abort_if(!auth()->user()->can('view_reports'), 403);

        $year1 = $request->input('year1', date('Y') - 1);
        $year2 = $request->input('year2', date('Y')); 

        $getYearData = function($year) {
            $contracts = DB::table('student_contracts')->whereYear('contract_date', $year)->sum('grand_total');
            $storeSales = DB::table('store_invoices')->whereNull('deleted_at')->whereYear('invoice_date', $year)->sum('grand_total');
            $otherIncomes = DB::table('incomes')->whereNull('deleted_at')->whereYear('income_date', $year)->sum('total_amount');
            
            $payrolls = DB::table('hr_payrolls')->where('status', 'paid')->where('year', $year)->sum('net_salary');
            $purchases = DB::table('purchase_invoices')->whereNull('deleted_at')->whereYear('invoice_date', $year)->sum('grand_total');
            $expenses = DB::table('expenses')->whereNull('deleted_at')->whereYear('expense_date', $year)->sum('total_amount');
            
            $totalRevenue = $contracts + $storeSales + $otherIncomes;
            $totalExpenses = $payrolls + $purchases + $expenses;
            
            return [
                'contracts' => $contracts,
                'storeSales' => $storeSales,
                'totalRevenue' => $totalRevenue,
                'payrolls' => $payrolls,
                'purchases' => $purchases,
                'expenses' => $expenses,
                'totalExpenses' => $totalExpenses,
                'netProfit' => $totalRevenue - $totalExpenses,
            ];
        };

        $dataY1 = $getYearData($year1);
        $dataY2 = $getYearData($year2);

        $profitGrowth = 0;
        if ($dataY1['netProfit'] > 0) {
            $profitGrowth = (($dataY2['netProfit'] - $dataY1['netProfit']) / $dataY1['netProfit']) * 100;
        } elseif ($dataY1['netProfit'] <= 0 && $dataY2['netProfit'] > 0) {
            $profitGrowth = 100; 
        }

        $revenueGrowth = 0;
        if ($dataY1['totalRevenue'] > 0) {
            $revenueGrowth = (($dataY2['totalRevenue'] - $dataY1['totalRevenue']) / $dataY1['totalRevenue']) * 100;
        }

        $availableYears = range(date('Y') + 1, 2022); 

        return view('backend.reports.comparison', compact('year1', 'year2', 'dataY1', 'dataY2', 'profitGrowth', 'revenueGrowth', 'availableYears'));
    }

    // ==========================================
    // 🔥 15. تقرير مقارنة الفروع (Branch Comparison) 🔥
    // ==========================================
    public function branchComparisonReport(Request $request)
    {
        abort_if(!auth()->user()->can('view_reports'), 403);

        $branches = \App\Models\Branche::all();
        
        $branch1 = $request->input('branch1', $branches->first()->id ?? null);
        $branch2 = $request->input('branch2', $branches->skip(1)->first()->id ?? null);
        $year = $request->input('year', date('Y')); 

        // 🔥 نقل Schema::hasColumn خارج الـ Closure لتحسين الأداء وتجنب الاستعلام المتكرر
        $hasBranchInPurchases = \Illuminate\Support\Facades\Schema::hasColumn('purchase_invoices', 'branch_id');

        $getBranchData = function($bId, $y) use ($hasBranchInPurchases) {
            if (!$bId) return ['contracts'=>0, 'storeSales'=>0, 'otherIncomes'=>0, 'totalRevenue'=>0, 'payrolls'=>0, 'purchases'=>0, 'expenses'=>0, 'totalExpenses'=>0, 'netProfit'=>0];

            $contracts = DB::table('student_contracts')->where('branch_id', $bId)->whereYear('contract_date', $y)->sum('grand_total');
            $storeSales = DB::table('store_invoices')->where('branch_id', $bId)->whereNull('deleted_at')->whereYear('invoice_date', $y)->sum('grand_total');
            $otherIncomes = DB::table('incomes')->where('branch_id', $bId)->whereNull('deleted_at')->whereYear('income_date', $y)->sum('total_amount');
            
            $employees = DB::table('hr_employees')->where('branch_id', $bId)->pluck('id');
            $payrolls = DB::table('hr_payrolls')->whereIn('employee_id', $employees)->where('status', 'paid')->where('year', $y)->sum('net_salary');
            
            $purchases = $hasBranchInPurchases 
                ? DB::table('purchase_invoices')->where('branch_id', $bId)->whereNull('deleted_at')->whereYear('invoice_date', $y)->sum('grand_total') : 0;
            
            $expenses = DB::table('expenses')->where('branch_id', $bId)->whereNull('deleted_at')->whereYear('expense_date', $y)->sum('total_amount');

            $totalRevenue = $contracts + $storeSales + $otherIncomes;
            $totalExpenses = $payrolls + $purchases + $expenses;

            return [
                'contracts' => $contracts,
                'storeSales' => $storeSales,
                'otherIncomes' => $otherIncomes,
                'totalRevenue' => $totalRevenue,
                'payrolls' => $payrolls,
                'purchases' => $purchases,
                'expenses' => $expenses,
                'totalExpenses' => $totalExpenses,
                'netProfit' => $totalRevenue - $totalExpenses,
            ];
        };

        $dataB1 = $getBranchData($branch1, $year);
        $dataB2 = $getBranchData($branch2, $year);

        $b1Name = \App\Models\Branche::find($branch1)->name_ar ?? 'الفرع الأول';
        $b2Name = \App\Models\Branche::find($branch2)->name_ar ?? 'الفرع الثاني';

        $winner = null;
        $profitDiff = 0;
        
        if ($dataB1['netProfit'] > $dataB2['netProfit']) {
            $winner = 'b1';
            $profitDiff = $dataB1['netProfit'] - $dataB2['netProfit'];
        } elseif ($dataB2['netProfit'] > $dataB1['netProfit']) {
            $winner = 'b2';
            $profitDiff = $dataB2['netProfit'] - $dataB1['netProfit'];
        }

        $availableYears = range(date('Y') + 1, 2022);

        return view('backend.reports.branch_comparison', compact(
            'branches', 'branch1', 'branch2', 'year', 'dataB1', 'dataB2', 'b1Name', 'b2Name', 'winner', 'profitDiff', 'availableYears'
        ));
    }
}