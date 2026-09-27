<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Student;
use App\Models\StudentContract;
use App\Models\StudentInstallment;
use App\Models\StoreInvoice;
use App\Models\PurchaseInvoice;
use App\Models\Expense;
use App\Models\Income;
use App\Models\HrPayroll;
use App\Models\HrEmployee;
use App\Models\HrDepartment;
use App\Models\HrLoan;
use App\Models\HrContract;
use App\Models\Product;
use App\Models\Branch;
use App\Models\FinancialAccount;
use App\Models\AcademicYear;
use App\Models\Branche;
use App\Models\Guardian;
use App\Models\Bus;
use App\Models\Subject;
use App\Models\StudentMark;
use App\Models\HrEmployeeDocument;
use App\Models\HrAttendance;

class ReportController extends Controller
{
    // ==========================================
    // 🔥 0. التحليل الذكي لمستوى الطلاب (AI Analysis) 🔥
    // ==========================================
    public function aiAnalysis()
    {
        abort_if(!auth()->user()->can('view_reports'), 403, 'غير مصرح لك بالاطلاع على هذا التقرير.');

        // 1. إجمالي الطلاب النشطين
        $totalStudents = Student::where('status', 'active')->count();

        // 2. الطلاب المتعثرين (At-Risk) - غياب 5 أيام أو أكثر
        $atRiskStudents = Student::with(['grade', 'section'])
            ->where('status', 'active')
            ->withCount(['attendances as absent_count' => function ($q) {
                $q->where('status', 'absent');
            }])
            ->having('absent_count', '>=', 5)
            ->orderByDesc('absent_count')
            ->take(20)
            ->get();

        // 3. معدل الحضور العام (للشارت)
        $attendanceRates = [];
        if (Schema::hasTable('student_attendances')) {
            $attendanceRates = DB::table('student_attendances')
                ->select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->pluck('total', 'status')
                ->toArray();
        }

        // 4. لوحة الشرف (أعلى 5 طلاب)
        $topStudents = collect();
        if (Schema::hasTable('student_marks') && method_exists(Student::class, 'marks')) {
            $topStudents = Student::with('grade')
                ->where('status', 'active')
                ->withSum('marks', 'mark_obtained')
                ->orderByDesc('marks_sum_mark_obtained')
                ->take(5)
                ->get();
        }

        // 5. تحليل المواد الدراسية
        $subjectAnalysis = collect();
        if (Schema::hasTable('student_marks') && Schema::hasTable('exam_categories')) {
            $subjectAnalysis = Subject::select('subjects.*')
                ->join('student_marks', 'student_marks.subject_id', '=', 'subjects.id')
                ->join('exam_categories', 'exam_categories.id', '=', 'student_marks.exam_category_id')
                ->where('student_marks.is_absent', false)
                ->whereNotNull('student_marks.mark_obtained')
                ->selectRaw('AVG((student_marks.mark_obtained / exam_categories.max_mark) * 100) as avg_mark')
                ->selectRaw('SUM(CASE WHEN ((student_marks.mark_obtained / exam_categories.max_mark) * 100) < 50 THEN 1 ELSE 0 END) as failed_count')
                ->groupBy('subjects.id', 'subjects.name_ar')
                ->orderBy('avg_mark', 'ASC')
                ->get();
        }

        return view('backend.reports.ai_analizes', compact(
            'totalStudents',
            'atRiskStudents',
            'attendanceRates',
            'topStudents',
            'subjectAnalysis'
        ));
    }

    // ==========================================
    // 🔥 1. التقرير المالي للطلاب 🔥
    // ==========================================
    public function studentFinancialReports(Request $request)
    {
        abort_if(!auth()->user()->can('view_reports'), 403, 'غير مصرح لك بالاطلاع على التقارير المالية.');
        
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        
        // جلب المديونية الكلية
        $students = Student::query()
            ->with(['grade', 'parent'])
            ->withSum('contracts', 'grand_total') 
            ->withSum('contracts', 'paid_amount')
            ->get();

        // ترتيب الطلاب حسب المديونية الكلية
        $topDebtors = $students->map(function($s) {
            $totalDebt = ($s->contracts_sum_grand_total ?? 0) - ($s->contracts_sum_paid_amount ?? 0);
            return [
                'name' => $s->first_name . ' ' . $s->tribe,
                'grade' => $s->grade->name_ar ?? '-',
                'parent_phone' => $s->parent->phone ?? '',
                'debt' => $totalDebt
            ];
        })->where('debt', '>', 0)->sortByDesc('debt')->take(10)->values();

        // تمرير المجاميع الكلية للـ Blade
        $totalContracts = $students->sum('contracts_sum_grand_total');
        $totalCollected = $students->sum('contracts_sum_paid_amount');

        return view('backend.reports.students_financial', compact(
            'students', 'startDate', 'endDate', 'topDebtors', 'totalContracts', 'totalCollected'
        ));
    }

  public function overallReports(Request $request)
{
    abort_if(!auth()->user()->can('view_reports'), 403);
    
    $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->startOfMonth();
    $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfMonth();

    // 🔥 الإيرادات 🔥
    
    // 1. مدفوعات الطلاب (من جدول payments - الأعم والأشمل)
    $studentPayments = 0;
    if (Schema::hasTable('payments')) {
        $studentPayments = DB::table('payments')
            ->where('payable_type', 'App\\Models\\StudentInstallment')
            ->where('amount', '>', 0) // المقبوضات فقط
            ->whereBetween('payment_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->sum('amount');
    }
    
    // 2. مبيعات المتجر
    $storeSales = 0;
    if (Schema::hasTable('store_invoices')) {
        $storeSales = DB::table('store_invoices')
            ->whereNull('deleted_at')
            ->whereBetween('invoice_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->sum('grand_total');
    }
        
    // 3. الإيرادات الأخرى
    $otherIncomes = 0;
    if (Schema::hasTable('incomes')) {
        // محاولة استخدام total_amount أولاً، ثم amount
        if (Schema::hasColumn('incomes', 'total_amount')) {
            $otherIncomes = DB::table('incomes')
                ->whereNull('deleted_at')
                ->whereBetween('income_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->sum('total_amount');
        } else {
            $otherIncomes = DB::table('incomes')
                ->whereNull('deleted_at')
                ->whereBetween('income_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->sum('amount');
        }
    }
        
    $totalRevenue = $studentPayments + $storeSales + $otherIncomes;

    // 🔥 المصروفات 🔥
    
    // 1. المشتريات
    $purchases = 0;
    if (Schema::hasTable('purchase_invoices')) {
        $purchases = DB::table('purchase_invoices')
            ->whereNull('deleted_at')
            ->whereBetween('invoice_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->sum('grand_total');
    }
        
    // 2. رواتب الموظفين
    $payrolls = 0;
    if (Schema::hasTable('hr_payrolls')) {
        $payrolls = DB::table('hr_payrolls')
            ->where('status', 'paid')
            ->whereBetween('payment_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->sum('net_salary');
    }
        
    // 3. المصروفات العامة
    $generalExpenses = 0;
    if (Schema::hasTable('expenses')) {
        // محاولة استخدام total_amount أولاً، ثم amount
        if (Schema::hasColumn('expenses', 'total_amount')) {
            $generalExpenses = DB::table('expenses')
                ->whereNull('deleted_at')
                ->whereBetween('expense_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->sum('total_amount');
        } else {
            $generalExpenses = DB::table('expenses')
                ->whereNull('deleted_at')
                ->whereBetween('expense_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->sum('amount');
        }
    }
        
    $totalExpenses = $purchases + $payrolls + $generalExpenses;
    $netProfit = $totalRevenue - $totalExpenses;

    // 🔥 إحصائيات إضافية 🔥
    $profitMargin = $totalRevenue > 0 ? round(($netProfit / $totalRevenue) * 100, 2) : 0;
    $expenseRatio = $totalRevenue > 0 ? round(($totalExpenses / $totalRevenue) * 100, 2) : 0;

    return view('backend.reports.overall', compact(
        'startDate', 'endDate', 
        'studentPayments', 'storeSales', 'otherIncomes', 'totalRevenue',
        'purchases', 'payrolls', 'generalExpenses', 'totalExpenses', 
        'netProfit', 'profitMargin', 'expenseRatio'
    ));
}

    // ==========================================
    // 🔥 3. تقرير أداء الفروع الشامل 🔥
    // ==========================================
    public function branchPerformance(Request $request)
    {
        abort_if(!auth()->user()->can('view_reports'), 403);
        
        $user = auth()->user();
        $isAdmin = $user->hasRole('Admin') || $user->id === 1;
        
        if ($isAdmin) {
            $branches = Branche::all();
            $branchId = $request->input('branch_id', 'all');
        } else {
            $employee = HrEmployee::where('user_id', $user->id)->first();
            if (!$employee || !$employee->branch_id) abort(403, 'حسابك غير مربوط بفرع محدد.');
            
            $branches = Branche::where('id', $employee->branch_id)->get();
            $branchId = $employee->branch_id;
        }

        $startDate = $request->input('from_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('to_date', Carbon::now()->endOfDay()->format('Y-m-d'));

        $branchesComparison = collect();
        foreach ($branches as $b) {
            $sales = 0;
            if (Schema::hasTable('store_invoices')) {
                $sales = DB::table('store_invoices')
                    ->where('branch_id', $b->id)
                    ->whereBetween('invoice_date', [$startDate, $endDate])
                    ->whereNull('deleted_at')
                    ->sum('grand_total');
            }
            
            $incomes = 0;
            if (Schema::hasTable('incomes')) {
                $incomes = DB::table('incomes')
                    ->where('branch_id', $b->id)
                    ->whereBetween('income_date', [$startDate, $endDate])
                    ->whereNull('deleted_at')
                    ->sum('total_amount');
            }
            
            $expenses = 0;
            if (Schema::hasTable('expenses')) {
                $expenses = DB::table('expenses')
                    ->where('branch_id', $b->id)
                    ->whereBetween('expense_date', [$startDate, $endDate])
                    ->whereNull('deleted_at')
                    ->sum('total_amount');
            }
            
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

        return view('backend.reports.branch_performance', compact(
            'branches', 'branchesComparison', 'topBranch', 'startDate', 'endDate', 'branchId', 'isAdmin'
        ));
    }

    // ==========================================
    // 🔥 4. تقرير الموارد البشرية والرواتب 🔥
    // ==========================================
    public function hrReports(Request $request)
    {
        abort_if(!auth()->user()->can('view_reports'), 403);
        
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));
        $departmentId = $request->input('department_id');

        $query = HrEmployee::with('department')->whereNull('deleted_at');
        if ($departmentId && $departmentId != 'all') {
            $query->where('hr_department_id', $departmentId);
        }
        $employees = $query->get();

        $payrolls = HrPayroll::where('month', $month)
            ->where('year', $year)
            ->whereIn('employee_id', $employees->pluck('id'))
            ->get();

        $activeLoans = HrLoan::whereIn('employee_id', $employees->pluck('id'))
            ->where('status', 'active')
            ->get();
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

        $payrollTrend = HrPayroll::selectRaw('month, SUM(net_salary) as total')
            ->where('year', $year)
            ->groupBy('month')
            ->pluck('total', 'month');

        $trendData = [];
        for($i = 1; $i <= 12; $i++) {
            $trendData[] = round($payrollTrend[str_pad($i, 2, '0', STR_PAD_LEFT)] ?? 0, 3);
        }

        $departments = HrDepartment::all();
        
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
            return isset($p->alert_quantity) && $p->quantity <= $p->alert_quantity;
        })->sortBy('quantity')->values();

        $restockCost = $shortages->sum(function($p) {
            $deficit = ($p->alert_quantity ?? 0) - $p->quantity;
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
    // 🔥 6. رادار التنبيهات الذكي 🔥
    // ==========================================
    public function smartAlerts()
    {
        abort_if(!auth()->user()->can('view_reports'), 403);
        
        $today = Carbon::now()->startOfDay();
        $next30Days = Carbon::now()->addDays(30)->endOfDay();

        // 1. أقساط الطلاب المتأخرة
        $dueInstallments = StudentInstallment::with(['studentContract.student.grade', 'studentContract.student.parent'])
            ->whereColumn('paid_amount', '<', 'amount')
            ->where('due_date', '<=', $today)
            ->orderBy('due_date', 'ASC')
            ->get();

        // 2. الطلاب كثيرو الغياب
        $absentStudents = Student::with(['grade', 'section', 'parent'])
            ->where('status', 'active')
            ->withCount(['attendances as absent_count' => function ($q) {
                $q->where('status', 'absent');
            }])
            ->having('absent_count', '>=', 3)
            ->orderByDesc('absent_count')
            ->take(20)
            ->get();

        // 3. نواقص المخزون
        $lowStock = Product::where('is_active', 1)
            ->whereColumn('quantity', '<=', 'alert_quantity')
            ->get();

        // 4. وثائق الموظفين القريبة من الانتهاء
        $expiringDocs = collect();
        if (Schema::hasTable('hr_employee_documents')) {
            $expiringDocs = HrEmployeeDocument::with('employee')
                ->where('expiry_date', '<=', $next30Days)
                ->orderBy('expiry_date', 'ASC')
                ->get();
        }

        // 5. فواتير الموردين غير المسددة
        $dueInvoices = PurchaseInvoice::with('supplier')
            ->whereIn('payment_status', ['unpaid', 'partial'])
            ->orderBy('invoice_date', 'ASC')
            ->get();

        // 6. عقود الموظفين القريبة من الانتهاء
        $expiringContracts = HrContract::with('employee')
            ->whereIn('status', ['valid', 'expired'])
            ->whereNotNull('end_date')
            ->where('end_date', '<=', $next30Days)
            ->orderBy('end_date', 'ASC')
            ->get();

        return view('backend.reports.smart_alerts', compact(
            'dueInstallments', 'absentStudents', 'lowStock', 'expiringDocs', 
            'dueInvoices', 'expiringContracts', 'today'
        ));
    }

    // ==========================================
    // 🔥 7. كشف حساب الطالب 🔥
    // ==========================================
    public function generateStudentStatement(Request $request)
    {
        $request->validate(['student_id' => 'required|exists:students,id']);

        $student = Student::with([
            'parent', 'grade', 'section', 
            'contracts.academicYear', 
            'contracts.installments.payments'
        ])->findOrFail($request->student_id);

        $transactions = collect();
        $totalDebit = 0;
        $totalCredit = 0;

        // 1. جلب العقود (مدين)
        foreach ($student->contracts as $contract) {
            $transactions->push([
                'date' => $contract->contract_date,
                'type' => 'contract',
                'reference' => $contract->contract_number,
                'description' => 'عقد دراسي - ' . ($contract->academicYear->name ?? '---'),
                'debit' => (float) $contract->grand_total,
                'credit' => 0,
            ]);
            $totalDebit += $contract->grand_total;
        }

        // 2. جلب المدفوعات (دائن)
        foreach ($student->contracts as $contract) {
            foreach ($contract->installments as $installment) {
                foreach ($installment->payments as $payment) {
                    $transactions->push([
                        'date' => $payment->payment_date,
                        'type' => 'payment',
                        'reference' => $payment->receipt_number ?? '---',
                        'description' => 'سداد قسط - ' . ($payment->payment_method ?? 'نقدي'),
                        'debit' => 0,
                        'credit' => (float) $payment->amount_paid,
                    ]);
                    $totalCredit += $payment->amount_paid;
                }
            }
        }

        // 3. ترتيب الحركات وحساب الرصيد التراكمي
        $transactions = $transactions->sortBy('date')->values();
        $runningBalance = 0;
        foreach ($transactions as &$trans) {
            $runningBalance += $trans['debit'] - $trans['credit'];
            $trans['balance'] = $runningBalance;
        }

        return view('backend.reports.student_statement', compact(
            'student', 'transactions', 'totalDebit', 'totalCredit', 'runningBalance'
        ));
    }

    // ==========================================
    // 🔥 8. صرف نثريات سريعة 🔥
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
            
            $userBranchId = auth()->user()->employee?->branch_id ?? 1;

            Expense::create([
                'expense_code' => 'EXP-Q-' . time(),
                'branch_id' => $userBranchId,
                'expense_category_id' => $request->expense_category_id,
                'financial_account_id' => $request->financial_account_id,
                'user_id' => auth()->id(),
                'amount' => $request->amount,
                'total_amount' => $request->amount,
                'expense_date' => now()->toDateString(),
                'transaction_details' => $request->transaction_details,
            ]);

            $account = FinancialAccount::findOrFail($request->financial_account_id);
            if ($account->balance < $request->amount) {
                throw new \Exception('رصيد الحساب المالي غير كافٍ لسحب هذا المصروف.');
            }
            
            $account->decrement('balance', $request->amount);
            DB::commit();
            
            return back()->with('success', 'تم تسجيل المصروف وخصمه من الحساب المالي بنجاح.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    // ==========================================
    // 🔥 9. التقرير الضريبي 🔥
    // ==========================================
    public function taxReport(Request $request)
    {
        abort_if(!auth()->user()->can('view_reports'), 403);
        
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $salesTax = 0;
        if (Schema::hasTable('store_invoices') && Schema::hasColumn('store_invoices', 'tax_amount')) {
            $salesTax = DB::table('store_invoices')
                ->whereNull('deleted_at')
                ->whereBetween('invoice_date', [$startDate, $endDate])
                ->sum('tax_amount');
        }
        
        $incomesTax = 0;
        if (Schema::hasTable('incomes') && Schema::hasColumn('incomes', 'tax_amount')) {
            $incomesTax = DB::table('incomes')
                ->whereNull('deleted_at')
                ->whereBetween('income_date', [$startDate, $endDate])
                ->sum('tax_amount');
        }
        
        $contractsTax = 0;
        if (Schema::hasTable('student_contracts') && Schema::hasColumn('student_contracts', 'tax_amount')) {
            $contractsTax = DB::table('student_contracts')
                ->whereBetween('contract_date', [$startDate, $endDate])
                ->sum('tax_amount');
        }
        
        $totalOutputTax = $salesTax + $incomesTax + $contractsTax;

        $purchasesTax = 0;
        if (Schema::hasTable('purchase_invoices') && Schema::hasColumn('purchase_invoices', 'tax_amount')) {
            $purchasesTax = DB::table('purchase_invoices')
                ->whereNull('deleted_at')
                ->whereBetween('invoice_date', [$startDate, $endDate])
                ->sum('tax_amount');
        }
        
        $expensesTax = 0;
        if (Schema::hasTable('expenses') && Schema::hasColumn('expenses', 'tax_amount')) {
            $expensesTax = DB::table('expenses')
                ->whereNull('deleted_at')
                ->whereBetween('expense_date', [$startDate, $endDate])
                ->sum('tax_amount');
        }
        
        $assetsTax = 0;
        if (Schema::hasTable('assets') && Schema::hasColumn('assets', 'tax_amount')) {
            $assetsTax = DB::table('assets')
                ->whereNull('deleted_at')
                ->whereBetween('transaction_date', [$startDate, $endDate])
                ->sum('tax_amount');
        }
        
        $totalInputTax = $purchasesTax + $expensesTax + $assetsTax;

        $netVat = $totalOutputTax - $totalInputTax;

        return view('backend.reports.tax_report', compact(
            'startDate', 'endDate', 'totalOutputTax', 'totalInputTax', 'netVat',
            'salesTax', 'incomesTax', 'contractsTax', 'purchasesTax', 'expensesTax', 'assetsTax'
        ));
    }

    // ==========================================
    // 🔥 10. كشف حساب ولي الأمر الشامل 🔥
    // ==========================================
    public function guardianStatement(Request $request)
    {
        abort_if(!auth()->user()->can('view_reports'), 403);
        $guardianId = $request->input('guardian_id');
        
        $guardians = Guardian::select('id', 'name_ar', 'name_en', 'phone', 'national_id')->get();
        $statementData = null;

        if ($guardianId) {
            $guardian = Guardian::with([
                'students.contracts.academicYear',
                'students.contracts.installments.payments',
                'students.grade'
            ])->findOrFail($guardianId);
            
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

    // ==========================================
    // 🔥 11. تقرير حركة الخزينة وتدفق الأموال 🔥
    // ==========================================
    public function cashFlowReport(Request $request)
    {
        abort_if(!auth()->user()->can('view_reports'), 403);
        
        $accountId = $request->input('financial_account_id');
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $accounts = FinancialAccount::where('is_active', 1)->get();
        $transactions = collect();
        $openingBalance = 0;

        if ($accountId) {
            $account = FinancialAccount::findOrFail($accountId);
            
            // حساب الرصيد الافتتاحي
            $baseOpening = $account->opening_balance ?? 0;
            $prevIn = 0;
            $prevOut = 0;
            $prevPayments = 0;
            
            if (Schema::hasTable('incomes')) {
                $prevIn = DB::table('incomes')
                    ->where('financial_account_id', $accountId)
                    ->where('income_date', '<', $startDate)
                    ->sum('total_amount');
            }
            
            if (Schema::hasTable('expenses')) {
                $prevOut = DB::table('expenses')
                    ->where('financial_account_id', $accountId)
                    ->where('expense_date', '<', $startDate)
                    ->sum('total_amount');
            }
            
            if (Schema::hasTable('payments')) {
                $prevPayments = DB::table('payments')
                    ->where('financial_account_id', $accountId)
                    ->where('payment_date', '<', $startDate)
                    ->sum('amount');
            }
            
            $openingBalance = $baseOpening + $prevIn - $prevOut + $prevPayments;

            // 1. الإيرادات العامة
            if (Schema::hasTable('incomes')) {
                $incomes = DB::table('incomes')
                    ->where('financial_account_id', $accountId)
                    ->whereBetween('income_date', [$startDate, $endDate])
                    ->select(
                        'income_date as date', 
                        DB::raw("'سند إيراد' as type"), 
                        'total_amount as inflow', 
                        DB::raw("0 as outflow"), 
                        'transaction_details as notes'
                    )
                    ->get();
                $transactions = $transactions->concat($incomes);
            }

            // 2. المصروفات العامة
            if (Schema::hasTable('expenses')) {
                $expenses = DB::table('expenses')
                    ->where('financial_account_id', $accountId)
                    ->whereBetween('expense_date', [$startDate, $endDate])
                    ->select(
                        'expense_date as date', 
                        DB::raw("'سند مصروف' as type"), 
                        DB::raw("0 as inflow"), 
                        'total_amount as outflow', 
                        'transaction_details as notes'
                    )
                    ->get();
                $transactions = $transactions->concat($expenses);
            }

            // 3. السندات والمدفوعات الشاملة
            if (Schema::hasTable('payments')) {
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
                $transactions = $transactions->concat($generalPayments);
            }

            $transactions = $transactions->sortBy('date')->values();
        }

        return view('backend.reports.cash_flow', compact(
            'accounts', 'accountId', 'startDate', 'endDate', 'transactions', 'openingBalance'
        ));
    }

    // ==========================================
    // 🔥 12. تقرير خصومات الطلاب 🔥
    // ==========================================
    public function discountsReport(Request $request)
    {
        abort_if(!auth()->user()->can('view_reports'), 403);
        $yearId = $request->input('academic_year_id');
        $years = AcademicYear::all();
        
        $query = StudentContract::with(['student.grade', 'branch', 'academicYear'])
            ->where('discount', '>', 0);
        
        if ($yearId) {
            $query->where('academic_year_id', $yearId);
        }
        
        $discountContracts = $query->orderByDesc('discount')->get();
        $totalDiscounts = $discountContracts->sum('discount');
        
        return view('backend.reports.discounts_report', compact(
            'years', 'yearId', 'discountContracts', 'totalDiscounts'
        ));
    }

    // ==========================================
    // 🔥 13. تقرير الحضور والغياب الإحصائي 🔥
    // ==========================================
    public function attendanceReport(Request $request)
    {
        abort_if(!auth()->user()->can('view_reports'), 403);
        
        $startDate = $request->input('start_date', Carbon::now()->startOfWeek()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfWeek()->format('Y-m-d'));
        $compareYear1 = $request->input('compare_year1', date('Y'));
        $compareYear2 = $request->input('compare_year2', date('Y') - 1);

        $stats = collect();
        if (Schema::hasTable('student_attendances')) {
            $stats = DB::table('student_attendances')
                ->whereBetween('attendance_date', [$startDate, $endDate])
                ->select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->pluck('total', 'status');
        }

        $totalRecords = $stats->sum();
        $present = $stats->get('present', 0);
        $absent = $stats->get('absent', 0);
        $late = $stats->get('late', 0);
        $excused = $stats->get('excused', 0);

        $topAbsentees = collect();
        if (Schema::hasTable('student_attendances')) {
            $topAbsentees = DB::table('student_attendances')
                ->join('students', 'students.id', '=', 'student_attendances.student_id')
                ->leftJoin('branches', 'branches.id', '=', 'students.branch_id')
                ->whereBetween('student_attendances.attendance_date', [$startDate, $endDate])
                ->where('student_attendances.status', 'absent')
                ->select(
                    'students.first_name', 
                    'students.tribe', 
                    'branches.name_ar as branch_name', 
                    DB::raw('count(student_attendances.id) as absent_days')
                )
                ->groupBy('students.id', 'students.first_name', 'students.tribe', 'branches.name_ar')
                ->orderByDesc('absent_days')
                ->limit(10)
                ->get();
        }

        $branches = Branche::all();
        $comparisonData = collect();

        foreach ($branches as $branch) {
            $absentY1 = 0;
            $absentY2 = 0;
            
            if (Schema::hasTable('student_attendances')) {
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
            }

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
    // 🔥 14. تقرير أرباح وإشغال الحافلات 🔥
    // ==========================================
    public function busReport(Request $request)
    {
        abort_if(!auth()->user()->can('view_reports'), 403);
        
        $buses = Bus::withCount('contracts')->get();
        
        $buses->map(function($bus) {
            $goCount = 0;
            $returnCount = 0;
            $bothCount = 0;
            
            if (Schema::hasTable('student_contracts')) {
                $goCount = StudentContract::where('bus_id', $bus->id)
                    ->whereHas('student', fn($q) => $q->where('transport_type', 'bus_go'))
                    ->count();
                    
                $returnCount = StudentContract::where('bus_id', $bus->id)
                    ->whereHas('student', fn($q) => $q->where('transport_type', 'bus_return'))
                    ->count();
                    
                $bothCount = StudentContract::where('bus_id', $bus->id)
                    ->whereHas('student', fn($q) => $q->where('transport_type', 'bus_both'))
                    ->count();
            }

            $bus->total_revenue = ($goCount * ($bus->monthly_fare_single ?? 0)) + 
                                  ($returnCount * ($bus->monthly_fare_single ?? 0)) + 
                                  ($bothCount * ($bus->monthly_fare_both ?? 0));
                                  
            $bus->students_both = $bothCount;
            $bus->students_single = $goCount + $returnCount;
            
            return $bus;
        });

        $totalStudentsInBuses = $buses->sum('contracts_count');
        $expectedBusRevenue = $buses->sum('total_revenue');
        
        return view('backend.reports.bus_report', compact(
            'buses', 'totalStudentsInBuses', 'expectedBusRevenue'
        ));
    }

    // ==========================================
    // 🔥 15. تقرير مقارنة الأداء (Year over Year) 🔥
    // ==========================================
    public function comparisonReport(Request $request)
    {
        abort_if(!auth()->user()->can('view_reports'), 403);
        
        $year1 = $request->input('year1', date('Y') - 1);
        $year2 = $request->input('year2', date('Y'));

        $getYearData = function($year) {
            $contracts = 0;
            if (Schema::hasTable('student_contracts')) {
                $contracts = DB::table('student_contracts')
                    ->whereYear('contract_date', $year)
                    ->sum('grand_total');
            }
            
            $storeSales = 0;
            if (Schema::hasTable('store_invoices')) {
                $storeSales = DB::table('store_invoices')
                    ->whereNull('deleted_at')
                    ->whereYear('invoice_date', $year)
                    ->sum('grand_total');
            }
            
            $otherIncomes = 0;
            if (Schema::hasTable('incomes')) {
                $otherIncomes = DB::table('incomes')
                    ->whereNull('deleted_at')
                    ->whereYear('income_date', $year)
                    ->sum('total_amount');
            }
            
            $payrolls = 0;
            if (Schema::hasTable('hr_payrolls')) {
                $payrolls = DB::table('hr_payrolls')
                    ->where('status', 'paid')
                    ->where('year', $year)
                    ->sum('net_salary');
            }
            
            $purchases = 0;
            if (Schema::hasTable('purchase_invoices')) {
                $purchases = DB::table('purchase_invoices')
                    ->whereNull('deleted_at')
                    ->whereYear('invoice_date', $year)
                    ->sum('grand_total');
            }
            
            $expenses = 0;
            if (Schema::hasTable('expenses')) {
                $expenses = DB::table('expenses')
                    ->whereNull('deleted_at')
                    ->whereYear('expense_date', $year)
                    ->sum('total_amount');
            }
            
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

        return view('backend.reports.comparison', compact(
            'year1', 'year2', 'dataY1', 'dataY2', 'profitGrowth', 'revenueGrowth', 'availableYears'
        ));
    }

    // ==========================================
    // 🔥 16. تقرير مقارنة الفروع 🔥
    // ==========================================
    public function branchComparisonReport(Request $request)
    {
        abort_if(!auth()->user()->can('view_reports'), 403);
        
        $branches = Branche::all();
        $branch1 = $request->input('branch1', $branches->first()->id ?? null);
        $branch2 = $request->input('branch2', $branches->skip(1)->first()->id ?? null);
        $year = $request->input('year', date('Y'));

        $getBranchData = function($bId, $y) {
            if (!$bId) return [
                'contracts'=>0, 'storeSales'=>0, 'otherIncomes'=>0, 'totalRevenue'=>0, 
                'payrolls'=>0, 'purchases'=>0, 'expenses'=>0, 'totalExpenses'=>0, 'netProfit'=>0
            ];
            
            $contracts = 0;
            if (Schema::hasTable('student_contracts')) {
                $contracts = DB::table('student_contracts')
                    ->where('branch_id', $bId)
                    ->whereYear('contract_date', $y)
                    ->sum('grand_total');
            }
            
            $storeSales = 0;
            if (Schema::hasTable('store_invoices')) {
                $storeSales = DB::table('store_invoices')
                    ->where('branch_id', $bId)
                    ->whereNull('deleted_at')
                    ->whereYear('invoice_date', $y)
                    ->sum('grand_total');
            }
            
            $otherIncomes = 0;
            if (Schema::hasTable('incomes')) {
                $otherIncomes = DB::table('incomes')
                    ->where('branch_id', $bId)
                    ->whereNull('deleted_at')
                    ->whereYear('income_date', $y)
                    ->sum('total_amount');
            }
            
            $employees = DB::table('hr_employees')->where('branch_id', $bId)->pluck('id');
            
            $payrolls = 0;
            if (Schema::hasTable('hr_payrolls')) {
                $payrolls = DB::table('hr_payrolls')
                    ->whereIn('employee_id', $employees)
                    ->where('status', 'paid')
                    ->where('year', $y)
                    ->sum('net_salary');
            }
            
            $purchases = 0;
            if (Schema::hasTable('purchase_invoices')) {
                $purchases = DB::table('purchase_invoices')
                    ->where('branch_id', $bId)
                    ->whereNull('deleted_at')
                    ->whereYear('invoice_date', $y)
                    ->sum('grand_total');
            }
            
            $expenses = 0;
            if (Schema::hasTable('expenses')) {
                $expenses = DB::table('expenses')
                    ->where('branch_id', $bId)
                    ->whereNull('deleted_at')
                    ->whereYear('expense_date', $y)
                    ->sum('total_amount');
            }
            
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
        
        $b1Name = Branche::find($branch1)->name_ar ?? 'الفرع الأول';
        $b2Name = Branche::find($branch2)->name_ar ?? 'الفرع الثاني';

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
            'branches', 'branch1', 'branch2', 'year', 'dataB1', 'dataB2', 
            'b1Name', 'b2Name', 'winner', 'profitDiff', 'availableYears'
        ));
    }
}