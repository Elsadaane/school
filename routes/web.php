<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\AcademicYearController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\ClassroomController;
use App\Http\Controllers\Admin\GuardianController;
use App\Http\Controllers\Admin\SectionController;
use App\Http\Controllers\Admin\StudentContractController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\TransportRouteController;
use App\Http\Controllers\ChatController; 
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => [ 'localeSessionRedirect', 'localizationRedirect', 'localeViewPath' ]
    ], function () {

    Route::get('/', function () { return view('welcome'); });

    // ==============================================================================
    // مسارات تسجيل الدخول المخصصة (Multi-Guard)
    // ==============================================================================
    Route::get('/login/{type}', [LoginController::class, 'showLoginForm'])->name('login.show');
    Route::post('/login', [LoginController::class, 'login'])->name('login.custom');
    Route::post('/logout/{type}', [LoginController::class, 'logout'])->name('logout.custom');

    // ==============================================================================
    // لوحات التحكم (Dashboards) - نظام الإدارة
    // ==============================================================================
    Route::middleware(['auth:web'])->group(function () {
        
        Route::prefix('admin')->name('admin.')->group(function () {
            Route::get('/audit-logs', [App\Http\Controllers\Admin\AuditLogController::class, 'index'])->name('audit-logs.index');
            Route::get('/installments/receipt/{payment_id}', [\App\Http\Controllers\Admin\StudentContractController::class, 'printReceipt'])->name('student-contracts.receipt');
            Route::post('/installments/student/{id}/pay', [\App\Http\Controllers\Admin\InstallmentController::class, 'payStudentInstallment'])->name('installments.pay_student');
            Route::post('/installments/purchase/{id}/pay', [\App\Http\Controllers\Admin\InstallmentController::class, 'payPurchaseInstallment'])->name('installments.pay_purchase');
            Route::post('/installments/asset/{id}/pay', [\App\Http\Controllers\Admin\InstallmentController::class, 'payAssetInstallment'])->name('installments.pay_asset');
            Route::post('students/transfers/{transfer}/pay', [\App\Http\Controllers\Admin\StudentController::class, 'payTransferInstallment'])->name('students.transfers.pay');
            
            // --- الداشبورد والملف الشخصي ---
            Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
            Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
            Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
            Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
            Route::get('/promotions', [\App\Http\Controllers\Admin\PromotionController::class, 'index'])->name('promotions.index');
            Route::get('promotions/history', [\App\Http\Controllers\Admin\PromotionController::class, 'history'])->name('promotions.history');
            Route::delete('promotions/{promotion}/rollback', [\App\Http\Controllers\Admin\PromotionController::class, 'rollback'])->name('promotions.rollback');
            Route::get('/get-filtered-sections', [\App\Http\Controllers\Admin\PromotionController::class, 'getFilteredSections'])->name('get-filtered-sections');
            Route::post('/promotions', [\App\Http\Controllers\Admin\PromotionController::class, 'store'])->name('promotions.store');
            Route::post('/notifications/{id}/mark-as-read', function($id) {
                auth()->user()->notifications()->findOrFail($id)->markAsRead();
                return redirect()->back();
            })->name('notifications.read');

            // --- الإعدادات الأكاديمية والطلاب ---
            Route::resource('branches', BranchController::class)->except(['create', 'show', 'edit']);
            Route::get('/get-branch-academic-years/{branch_id}', [SectionController::class, 'getAcademicYears'])->name('get-branch-academic-years');
            Route::post('/academic-years/{id}/close', [\App\Http\Controllers\Admin\AcademicYearController::class, 'closeYear'])->name('academic-years.close');
            Route::get('/certificates/student/{student_id}/term/{term_id}', [\App\Http\Controllers\Admin\CertificateController::class, 'printCertificate'])->name('certificates.print');
            Route::get('/get-branch-grades/{branch_id}', [SectionController::class, 'getGrades'])->name('get-branch-grades');
            Route::get('/get-branch-buses', [App\Http\Controllers\Admin\StudentContractController::class, 'getBranchBuses'])->name('get-branch-buses');
            Route::post('/quick-add-bus', [App\Http\Controllers\Admin\StudentContractController::class, 'quickAddBus'])->name('quick-add-bus');
            Route::post('/quick-add-fee', [App\Http\Controllers\Admin\StudentContractController::class, 'quickAddFee'])->name('quick-add-fee');
            Route::post('/emergency-online/declare', [\App\Http\Controllers\Admin\ScheduleController::class, 'declareEmergency'])->name('emergency_online.declare');
            Route::get('/get-branch-sections-tree/{branch_id}/{teacher_id?}', [TeacherController::class, 'getBranchSections'])->name('get-branch-sections-tree');
            Route::resource('academic-years', AcademicYearController::class)->except(['create', 'edit']);
            Route::resource('classrooms', ClassroomController::class)->except(['create', 'edit']);
            Route::resource('sections', SectionController::class)->except(['create','edit']);
            
            // مسار تحليل مستوى الطلاب (AI Analysis)
            Route::get('/reports/ai-analysis', [\App\Http\Controllers\Admin\StudentAnalysisController::class, 'index'])->name('reports.ai_analysis');
            // مسار رادار الأقساط والغياب
            Route::get('/reports/radar', [\App\Http\Controllers\Admin\RadarController::class, 'index'])->name('reports.radar');
            
            // ==========================================
            // ⚙️ إعدادات النظام والمستخدمين (Roles & Permissions)
            // ==========================================
            Route::get('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
            Route::post('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');
            Route::post('/users/{id}/toggle-status', [\App\Http\Controllers\Admin\UserController::class, 'toggleStatus'])->name('users.toggleStatus');
            Route::resource('users', UserController::class);
            Route::resource('roles', RoleController::class);

            // ==========================================
            // 🩺 إدارة الأعذار والغياب
            // ==========================================
            Route::get('/leave-requests', [\App\Http\Controllers\Admin\StudentLeaveController::class, 'index'])->name('leave_requests.index');
            Route::post('/leave-requests/{id}/approve', [\App\Http\Controllers\Admin\StudentLeaveController::class, 'approve'])->name('leave_requests.approve');
            Route::post('/leave-requests/{id}/reject', [\App\Http\Controllers\Admin\StudentLeaveController::class, 'reject'])->name('leave_requests.reject');
            
            // ==========================================
            // 👨‍🏫 المعلمين والفصول الافتراضية
            // ==========================================
            Route::post('teachers/{id}/assign-sections-quick', [TeacherController::class, 'assignSectionsQuick'])->name('teachers.assign_sections_quick');
            Route::get('teachers/{id}/print-contract', [TeacherController::class, 'printContract'])->name('teachers.print_contract');
            Route::resource('teachers', TeacherController::class);
            
            // 🔥 الفصول الافتراضية للإدارة 🔥
            Route::resource('virtual-classes', \App\Http\Controllers\Admin\VirtualClassController::class);
            Route::get('virtual-classes/{id}/join', [\App\Http\Controllers\Admin\VirtualClassController::class, 'joinRoom'])->name('virtual-classes.join');
            Route::get('/get-subject-teachers/{subject_id}', [\App\Http\Controllers\Admin\TeacherController::class, 'getTeachersBySubject'])->name('get-subject-teachers');
            
            Route::get('buses/{id}/print-contract', [\App\Http\Controllers\Admin\BusController::class, 'printContract'])->name('buses.print_contract');
            Route::resource('buses', \App\Http\Controllers\Admin\BusController::class);
            
            // 🔥 أولياء الأمور والطلاب (تم استعادتهم) 🔥
            Route::get('guardians/{id}/show', [GuardianController::class, 'show'])->name('guardians.show');
            Route::resource('guardians', GuardianController::class);
            
            Route::post('students/{id}/transfer', [StudentController::class, 'transfer'])->name('students.transfer');
            Route::get('/students-search/api', [\App\Http\Controllers\Admin\StudentController::class, 'search'])->name('api.students.search');
            Route::resource('students', StudentController::class);

            // ==========================================
            // 🏫 نظام تسكين الطلاب في الشعب (الفصول)
            // ==========================================
            Route::get('/placements', [\App\Http\Controllers\Admin\StudentPlacementController::class, 'index'])->name('placements.index');
            Route::post('/placements/fetch', [\App\Http\Controllers\Admin\StudentPlacementController::class, 'fetch'])->name('placements.fetch');
            Route::post('/placements/store', [\App\Http\Controllers\Admin\StudentPlacementController::class, 'store'])->name('placements.store');

            // ==========================================
            // 🔥 إعدادات الدرجات والتقويم (Smart Grading Setup) 🔥
            // ==========================================
            Route::get('/grading-setup', [\App\Http\Controllers\Admin\ExamCategoryController::class, 'index'])->name('grading.index');
            Route::post('/grading-setup', [\App\Http\Controllers\Admin\ExamCategoryController::class, 'store'])->name('grading.store');

            // ==========================================
            // مسارات عقود الطلاب (Student Contracts)
            // ==========================================
            Route::get('student-contracts/pending-renewals', [\App\Http\Controllers\Admin\StudentContractController::class, 'pendingRenewals'])->name('student-contracts.pending_renewals');
            
            Route::resource('student-contracts', App\Http\Controllers\Admin\StudentContractController::class);
            
            Route::get('student-contracts/{id}/print', [App\Http\Controllers\Admin\StudentContractController::class, 'print'])->name('student-contracts.print');
            Route::post('student-contracts/{id}/add-item', [App\Http\Controllers\Admin\StudentContractController::class, 'addItem'])->name('student-contracts.add-item');
            Route::post('student-contracts/{id}/add-installment', [App\Http\Controllers\Admin\StudentContractController::class, 'addInstallment'])->name('student-contracts.add-installment');
            Route::post('student-contracts/{id}/approve', [App\Http\Controllers\Admin\StudentContractController::class, 'approve'])->name('student-contracts.approve');
            
            Route::get('student-contracts/{id}/signature', [App\Http\Controllers\Admin\StudentContractController::class, 'signaturePage'])->name('student-contracts.signature');
            Route::post('student-contracts/{id}/signature', [App\Http\Controllers\Admin\StudentContractController::class, 'saveSignature'])->name('student-contracts.save-signature');
            Route::get('student-contracts/{id}/payments', [App\Http\Controllers\Admin\StudentContractController::class, 'showPayments'])->name('student-contracts.payments');
            Route::post('student-contracts/installments/{id}/pay', [App\Http\Controllers\Admin\StudentContractController::class, 'payInstallment'])->name('student-contracts.pay-installment');
            
            // --- الغياب والحضور ---
            Route::get('/sections/{id}/attendance', [SectionController::class, 'attendance'])->name('sections.attendance');
            Route::post('/sections/{id}/attendance', [SectionController::class, 'storeAttendance'])->name('sections.storeAttendance');
            Route::get('/sections/{id}/students', [SectionController::class, 'studentsRoster'])->name('sections.students');
            Route::get('attendances/student-profile/{id}', [AttendanceController::class, 'getStudentProfile'])->name('attendances.student_profile');
            Route::get('attendances', [AttendanceController::class, 'index'])->name('attendances.index');
            Route::get('attendances/section/{section_id}/dates', [AttendanceController::class, 'showDateAttendance'])->name('attendances.show_date_attendance');            
            Route::get('attendances/create', [AttendanceController::class, 'create'])->name('attendances.create');
            Route::post('attendances', [AttendanceController::class, 'store'])->name('attendances.store');

            Route::resource('transport-routes', TransportRouteController::class)->names([
                'index' => 'transport-routes.index',
                'create' => 'transport-routes.create',
                'store' => 'transport-routes.store',
                'edit' => 'transport-routes.edit',
                'update' => 'transport-routes.update',
                'destroy' => 'transport-routes.destroy',
            ]);
            Route::get('get-branch-routes', [StudentContractController::class, 'getBranchRoutes'])->name('get-branch-routes');
            Route::get('get-route-buses', [StudentContractController::class, 'getRouteBuses'])->name('get-route-buses');

            // ==========================================
            // 💰 النظام المالي والمحاسبي والمستودعات
            // ==========================================
            Route::get('products/low-stock', [\App\Http\Controllers\Admin\ProductController::class, 'lowStock'])->name('products.low-stock');
            Route::post('/products/{id}/duplicate', [App\Http\Controllers\Admin\ProductController::class, 'duplicate'])->name('products.duplicate');
            Route::resource('products', \App\Http\Controllers\Admin\ProductController::class)->except(['show']);
            
            Route::get('financial-accounts/transfer-history', [\App\Http\Controllers\Admin\FinancialAccountController::class, 'history'])->name('account-transfers.history');
            Route::resource('financial-accounts', \App\Http\Controllers\Admin\FinancialAccountController::class)->except(['create', 'edit', 'show']);
            Route::post('financial-accounts/transfer', [\App\Http\Controllers\Admin\FinancialAccountController::class, 'transferMoney'])->name('financial-accounts.transfer');
            Route::get('financial-accounts/{id}/ledger', [\App\Http\Controllers\Admin\AccountingController::class, 'ledger'])->name('financial-accounts.ledger');
            
            Route::resource('fee-items', \App\Http\Controllers\Admin\FeeItemController::class)->except(['create', 'show', 'edit']);
            Route::get('installments', [\App\Http\Controllers\Admin\InstallmentController::class, 'index'])->name('installments.index');
            Route::get('installments/overdue', [\App\Http\Controllers\Admin\InstallmentController::class, 'overdue'])->name('installments.overdue');
            Route::post('installments/{id}/pay', [App\Http\Controllers\Admin\StudentContractController::class, 'payInstallment'])->name('installments.pay');
            
            Route::resource('suppliers', \App\Http\Controllers\Admin\SupplierController::class);
            Route::resource('purchase-invoices', \App\Http\Controllers\Admin\PurchaseInvoiceController::class);
            Route::post('purchase-invoices/{id}/pay', [\App\Http\Controllers\Admin\PurchaseInvoiceController::class, 'payInvoice'])->name('purchase-invoices.pay');
            Route::resource('purchase-returns', \App\Http\Controllers\Admin\PurchaseReturnController::class);
            
            Route::get('/inventory-adjustments/branch-products', [App\Http\Controllers\Admin\InventoryAdjustmentController::class, 'getBranchProducts'])->name('get-inventory-products');
            Route::post('inventory-adjustments/{id}/approve', [\App\Http\Controllers\Admin\InventoryAdjustmentController::class, 'approve'])->name('inventory-adjustments.approve');
            Route::post('inventory-adjustments/{id}/reject', [\App\Http\Controllers\Admin\InventoryAdjustmentController::class, 'reject'])->name('inventory-adjustments.reject');
            Route::resource('inventory-adjustments', \App\Http\Controllers\Admin\InventoryAdjustmentController::class);
            
            Route::get('store-invoices/create', [\App\Http\Controllers\Admin\StoreInvoiceController::class, 'create'])->name('store-invoices.create');
            Route::post('store-invoices', [\App\Http\Controllers\Admin\StoreInvoiceController::class, 'store'])->name('store-invoices.store');
            
            Route::get('reports/student-statement', [\App\Http\Controllers\Admin\AccountStatementController::class, 'index'])->name('reports.student-statement');
            Route::post('reports/student-statement/generate', [\App\Http\Controllers\Admin\AccountStatementController::class, 'show'])->name('reports.student-statement.generate');

            // ==========================================
            // 📊 مسارات التقارير الشاملة (Reports)
            // ==========================================
            Route::prefix('reports')->name('reports.')->group(function () {
                Route::get('/overall-pnl', [\App\Http\Controllers\Admin\ReportController::class, 'overallReports'])->name('overall');
                Route::get('/branch-performance', [\App\Http\Controllers\Admin\ReportController::class, 'branchPerformance'])->name('branch_performance');
                Route::get('/branch-comparison', [\App\Http\Controllers\Admin\ReportController::class, 'branchComparisonReport'])->name('branch_comparison');
                Route::get('/yoy-comparison', [\App\Http\Controllers\Admin\ReportController::class, 'comparisonReport'])->name('comparison');
                Route::get('/students-financial', [\App\Http\Controllers\Admin\ReportController::class, 'studentFinancialReports'])->name('students_financial');
                Route::get('/guardian-statement', [\App\Http\Controllers\Admin\ReportController::class, 'guardianStatement'])->name('guardian_statement');
                Route::get('/tax-report', [\App\Http\Controllers\Admin\ReportController::class, 'taxReport'])->name('tax_report');
                Route::get('/cash-flow', [\App\Http\Controllers\Admin\ReportController::class, 'cashFlowReport'])->name('cash_flow');
                Route::get('/hr-payroll', [\App\Http\Controllers\Admin\ReportController::class, 'hrReports'])->name('hr');
                Route::get('/attendance', [\App\Http\Controllers\Admin\ReportController::class, 'attendanceReport'])->name('attendance');
                Route::get('/discounts', [\App\Http\Controllers\Admin\ReportController::class, 'discountsReport'])->name('discounts');
                Route::get('/buses', [\App\Http\Controllers\Admin\ReportController::class, 'busReport'])->name('buses');
                Route::get('/inventory-shortages', [\App\Http\Controllers\Admin\ReportController::class, 'inventoryShortages'])->name('inventory_shortages');
                Route::get('/smart-alerts', [\App\Http\Controllers\Admin\ReportController::class, 'smartAlerts'])->name('smart_alerts');
                Route::post('/add-expense', [\App\Http\Controllers\Admin\ReportController::class, 'addExpense'])->name('add_expense');
            });

            Route::get('payments', [\App\Http\Controllers\Admin\PaymentController::class, 'index'])->name('payments.index');
            Route::post('payments', [\App\Http\Controllers\Admin\PaymentController::class, 'store'])->name('payments.store');
            Route::delete('payments', [\App\Http\Controllers\Admin\PaymentController::class, 'destroy'])->name('payments.destroy');
            Route::get('payments/{id}/print', [\App\Http\Controllers\Admin\PaymentController::class, 'printSupplierReceipt'])->name('payments.print');            
            // --- الإيرادات والمصروفات والأصول والتصنيفات (Types) ---
            Route::get('/incomes', [\App\Http\Controllers\Admin\IncomeController::class, 'index'])->name('incomes.index');
            Route::post('/incomes', [\App\Http\Controllers\Admin\IncomeController::class, 'store'])->name('incomes.store');
            Route::put('/incomes/{id}', [\App\Http\Controllers\Admin\IncomeController::class, 'update'])->name('incomes.update');
            Route::delete('/incomes/{id}', [\App\Http\Controllers\Admin\IncomeController::class, 'destroy'])->name('incomes.destroy');
            Route::get('/incomes/types', [\App\Http\Controllers\Admin\IncomeController::class, 'types'])->name('incomes.types');
            Route::post('/incomes/categories', [\App\Http\Controllers\Admin\IncomeController::class, 'storeCategory'])->name('incomes.category.store');
            Route::post('/incomes/subcategories', [\App\Http\Controllers\Admin\IncomeController::class, 'storeSubCategory'])->name('incomes.subcategory.store');
            Route::delete('/incomes/categories/{id}', [\App\Http\Controllers\Admin\IncomeController::class, 'destroyCategory'])->name('incomes.category.destroy');
            Route::delete('/incomes/subcategories/{id}', [\App\Http\Controllers\Admin\IncomeController::class, 'destroySubCategory'])->name('incomes.subcategory.destroy');
            Route::get('/incomes/print-receipt/{id}', [\App\Http\Controllers\Admin\IncomeController::class, 'printReceipt'])->name('incomes.print_receipt');
            Route::get('/get-branch-sections/{branch_id}', [\App\Http\Controllers\Admin\SectionController::class, 'getSectionsByBranch']);
            Route::post('/sections/bulk-transfer', [\App\Http\Controllers\Admin\SectionController::class, 'bulkTransfer'])->name('sections.bulk_transfer');
            
            Route::get('/expenses', [\App\Http\Controllers\Admin\ExpenseController::class, 'index'])->name('expenses.index');
            Route::post('/expenses', [\App\Http\Controllers\Admin\ExpenseController::class, 'store'])->name('expenses.store');
            Route::put('/expenses/{id}', [\App\Http\Controllers\Admin\ExpenseController::class, 'update'])->name('expenses.update');
            Route::delete('/expenses/{id}', [\App\Http\Controllers\Admin\ExpenseController::class, 'destroy'])->name('expenses.destroy');
            Route::get('/expenses/types', [\App\Http\Controllers\Admin\ExpenseController::class, 'types'])->name('expenses.types');
            Route::post('/expenses/categories', [\App\Http\Controllers\Admin\ExpenseController::class, 'storeCategory'])->name('expenses.category.store');
            Route::post('/expenses/subcategories', [\App\Http\Controllers\Admin\ExpenseController::class, 'storeSubCategory'])->name('expenses.subcategory.store');
            Route::delete('/expenses/categories/{id}', [\App\Http\Controllers\Admin\ExpenseController::class, 'destroyCategory'])->name('expenses.category.destroy');
            Route::delete('/expenses/subcategories/{id}', [\App\Http\Controllers\Admin\ExpenseController::class, 'destroySubCategory'])->name('expenses.subcategory.destroy');
            Route::get('/expenses/get-subcategories/{id}', [\App\Http\Controllers\Admin\ExpenseController::class, 'getSubCategories'])->name('expenses.get_subcategories');
            Route::get('/expenses/print-receipt/{id}', [\App\Http\Controllers\Admin\ExpenseController::class, 'printReceipt'])->name('expenses.print_receipt');

            // ==========================================
            // 🏢 إدارة الأصول الثابتة والعهد (Asset Management)
            // ==========================================
            Route::prefix('assets')->name('assets.')->group(function () {
                // 1. الأقسام الرئيسية والفرعية
                Route::get('/types', [\App\Http\Controllers\Admin\AssetController::class, 'types'])->name('types');
                Route::post('/categories', [\App\Http\Controllers\Admin\AssetController::class, 'storeCategory'])->name('category.store');
                Route::post('/subcategories', [\App\Http\Controllers\Admin\AssetController::class, 'storeSubCategory'])->name('subcategory.store');
                Route::delete('/categories/{id}', [\App\Http\Controllers\Admin\AssetController::class, 'destroyCategory'])->name('category.destroy');
                Route::delete('/subcategories/{id}', [\App\Http\Controllers\Admin\AssetController::class, 'destroySubCategory'])->name('subcategory.destroy');

                // 2. إدارة العُهد (تسليم، استرجاع، طباعة، تحديث توقيع)
                Route::post('/custody/{id}/assign', [\App\Http\Controllers\Admin\AssetController::class, 'assignCustody'])->name('custody.assign');
                Route::post('/custody/{id}/return', [\App\Http\Controllers\Admin\AssetController::class, 'returnCustody'])->name('custody.return');
                Route::post('/custody/{id}/signature', [\App\Http\Controllers\Admin\AssetController::class, 'updateSignature'])->name('custody.signature.update');
                Route::get('/custody/{id}/print', [\App\Http\Controllers\Admin\AssetController::class, 'printCustodyReceipt'])->name('custody.print');

                // 3. أقساط الأصول
                Route::get('/installments', [\App\Http\Controllers\Admin\AssetController::class, 'installments'])->name('installments');
                Route::post('/installments/{id}/pay', [\App\Http\Controllers\Admin\AssetController::class, 'payInstallment'])->name('installments.pay');

                // 4. العمليات الأساسية للأصول (بديل للـ Resource لتطابق الأسماء المخصصة)
                Route::get('/', [\App\Http\Controllers\Admin\AssetController::class, 'index'])->name('index');
                Route::post('/', [\App\Http\Controllers\Admin\AssetController::class, 'storeAsset'])->name('store');
                Route::put('/{id}', [\App\Http\Controllers\Admin\AssetController::class, 'updateAsset'])->name('update');
                Route::delete('/{id}', [\App\Http\Controllers\Admin\AssetController::class, 'destroy'])->name('destroy');
            });                
                
            Route::get('/accounting/chart-of-accounts', [\App\Http\Controllers\Admin\AccountingController::class, 'chartOfAccounts'])->name('accounting.chart_of_accounts');
            Route::post('/accounting/close-year', [\App\Http\Controllers\Admin\AccountingController::class, 'closeFinancialYear'])->name('accounting.close_year');
            Route::post('/accounting/store-account', [\App\Http\Controllers\Admin\AccountingController::class, 'storeAccount'])->name('accounting.store_account');
            Route::put('/accounting/update-account/{id}', [\App\Http\Controllers\Admin\AccountingController::class, 'updateAccount'])->name('accounting.update_account');
            
            // --- 📦 التحويلات المخزنية وأرصدة الفروع ---
            Route::get('warehouse-stocks', [\App\Http\Controllers\Admin\WarehouseStockController::class, 'index'])->name('warehouse_stocks.index');
            Route::get('warehouse-stocks/{id}', [\App\Http\Controllers\Admin\WarehouseStockController::class, 'show'])->name('warehouse_stocks.show');
            Route::post('warehouse-stocks/transfer', [\App\Http\Controllers\Admin\WarehouseStockController::class, 'transferStock'])->name('warehouse_stocks.transfer');
            
            Route::get('stock-transfers/get-products', [\App\Http\Controllers\Admin\StockTransferController::class, 'getBranchProducts'])->name('stock-transfers.get-products');
            Route::resource('stock-transfers', \App\Http\Controllers\Admin\StockTransferController::class);
            Route::get('stock-transfers/{id}/print', [\App\Http\Controllers\Admin\StockTransferController::class, 'print'])->name('stock-transfers.print');
            Route::post('stock-transfers/{id}/ship', [\App\Http\Controllers\Admin\StockTransferController::class, 'ship'])->name('stock-transfers.ship');
            Route::post('stock-transfers/{id}/receive', [\App\Http\Controllers\Admin\StockTransferController::class, 'receive'])->name('stock-transfers.receive');
            Route::post('stock-transfers/{id}/cancel', [\App\Http\Controllers\Admin\StockTransferController::class, 'cancel'])->name('stock-transfers.cancel');

            // --- نقل الطلاب بين الفروع وجداول الحصص ---
            Route::get('/student-transfers', [\App\Http\Controllers\Admin\StudentTransferController::class, 'index'])->name('student-transfers.index');
            Route::post('/student-transfers', [\App\Http\Controllers\Admin\StudentTransferController::class, 'store'])->name('student-transfers.store');
            Route::post('/student-transfers/{id}/ministry', [\App\Http\Controllers\Admin\StudentTransferController::class, 'approveMinistry'])->name('student-transfers.approve-ministry');
            Route::post('/student-transfers/{id}/finalize', [\App\Http\Controllers\Admin\StudentTransferController::class, 'finalizeTransfer'])->name('student-transfers.finalize');
            Route::get('/student-transfers/{id}/print', [\App\Http\Controllers\Admin\StudentTransferController::class, 'print'])->name('student-transfers.print');
            Route::get('/student-transfers/{id}/show', [\App\Http\Controllers\Admin\StudentTransferController::class, 'show'])->name('student-transfers.show');
            Route::get('/student-transfers/{id}/cancel', [\App\Http\Controllers\Admin\StudentTransferController::class, 'cancel'])->name('student-transfers.cancel');
            Route::delete('/student-transfers/{id}', [\App\Http\Controllers\Admin\StudentTransferController::class, 'destroy'])->name('student-transfers.destroy');
            
            // --- 📅 مسارات جدول الحصص ---
            Route::get('/schedules/get-filters/{branch_id}', [\App\Http\Controllers\Admin\ScheduleController::class, 'getBranchFilters'])->name('schedules.get-filters');
            Route::get('/schedules/get-sections-ajax', [\App\Http\Controllers\Admin\ScheduleController::class, 'getSectionsAjax'])->name('schedules.get-sections-ajax');
            Route::get('/schedules', [\App\Http\Controllers\Admin\ScheduleController::class, 'index'])->name('schedules.index');
            Route::post('/schedules/get-section', [\App\Http\Controllers\Admin\ScheduleController::class, 'getSectionSchedule'])->name('schedules.get-section');
            Route::post('/schedules/store', [\App\Http\Controllers\Admin\ScheduleController::class, 'store'])->name('schedules.store');
            Route::post('/schedules/available-teachers', [\App\Http\Controllers\Admin\ScheduleController::class, 'getAvailableTeachers'])->name('schedules.available-teachers');
            Route::resource('subjects', \App\Http\Controllers\Admin\SubjectController::class)->except(['create', 'show', 'edit']);
        });

        // --------------------------------------------------------
        // 👔 ب. نظام الموارد البشرية (HRMS)
        // --------------------------------------------------------
        Route::prefix('admin/hr')->name('admin.hr.')->group(function () {
            
            // ⚙️ إعدادات الـ HR (الأقسام، الورديات، الدرجات)
            Route::prefix('settings')->name('settings.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Admin\HrSettingController::class, 'index'])->name('index');
                
                // الأقسام
                Route::post('/departments', [\App\Http\Controllers\Admin\HrSettingController::class, 'storeDepartment'])->name('departments.store');
                Route::put('/departments/{id}', [\App\Http\Controllers\Admin\HrSettingController::class, 'updateDepartment'])->name('departments.update');
                Route::delete('/departments/{id}', [\App\Http\Controllers\Admin\HrSettingController::class, 'destroyDepartment'])->name('departments.destroy');
                // الورديات
                Route::post('/shifts', [\App\Http\Controllers\Admin\HrSettingController::class, 'storeShift'])->name('shifts.store');
                Route::put('/shifts/{id}', [\App\Http\Controllers\Admin\HrSettingController::class, 'updateShift'])->name('shifts.update');
                Route::delete('/shifts/{id}', [\App\Http\Controllers\Admin\HrSettingController::class, 'destroyShift'])->name('shifts.destroy');
                
                // الدرجات الوظيفية
                Route::post('/grades', [\App\Http\Controllers\Admin\HrSettingController::class, 'storeGrade'])->name('grades.store');
                Route::put('/grades/{id}', [\App\Http\Controllers\Admin\HrSettingController::class, 'updateGrade'])->name('grades.update');
                Route::delete('/grades/{id}', [\App\Http\Controllers\Admin\HrSettingController::class, 'destroyGrade'])->name('grades.destroy');
            });

            Route::get('/org-chart', [\App\Http\Controllers\Admin\HrOrgChartController::class, 'index'])->name('org_chart.index');
            Route::resource('employees', \App\Http\Controllers\Admin\HrEmployeeController::class);

            Route::post('/documents/store', [\App\Http\Controllers\Admin\HrEmployeeController::class, 'storeDocument'])->name('documents.store');
            Route::delete('/documents/{id}', [\App\Http\Controllers\Admin\HrEmployeeController::class, 'destroyDocument'])->name('documents.destroy');
            Route::put('/documents/{id}', [\App\Http\Controllers\Admin\HrEmployeeController::class, 'updateDocument'])->name('documents.update');

            Route::get('/contracts', [\App\Http\Controllers\Admin\HrContractController::class, 'index'])->name('contracts.index');
            Route::post('/contracts', [\App\Http\Controllers\Admin\HrContractController::class, 'store'])->name('contracts.store');
            
            Route::get('/contracts/{id}/status/{status}', [\App\Http\Controllers\Admin\HrContractController::class, 'updateStatus'])->name('contracts.status');
            Route::get('/contracts/{id}', [\App\Http\Controllers\Admin\HrContractController::class, 'show'])->name('contracts.show');
            Route::get('/alerts', [\App\Http\Controllers\Admin\HrAlertController::class, 'index'])->name('alerts.index');
            
            // ✅ الحضور والغياب (مرتبة ومنظمة)
            Route::get('/attendance', [\App\Http\Controllers\Admin\HrAttendanceController::class, 'index'])->name('attendance.index');
            Route::post('/attendance/import', [\App\Http\Controllers\Admin\HrAttendanceController::class, 'import'])->name('attendance.import');
            Route::put('/attendance/{id}', [\App\Http\Controllers\Admin\HrAttendanceController::class, 'update'])->name('attendance.update');
            Route::post('/attendance/mark-absentees', [\App\Http\Controllers\Admin\HrAttendanceController::class, 'markAbsentees'])->name('attendance.mark_absentees');
            
            Route::get('/shift-rosters', [\App\Http\Controllers\Admin\HrShiftRosterController::class, 'index'])->name('shift_rosters.index');
            Route::post('/shift-rosters', [\App\Http\Controllers\Admin\HrShiftRosterController::class, 'store'])->name('shift_rosters.store');
            Route::delete('/shift-rosters/{id}', [\App\Http\Controllers\Admin\HrShiftRosterController::class, 'destroy'])->name('shift_rosters.destroy');
            Route::put('/shift-rosters/{id}', [\App\Http\Controllers\Admin\HrShiftRosterController::class, 'update'])->name('shift_rosters.update');

            Route::get('/payroll', [\App\Http\Controllers\Admin\HrPayrollController::class, 'index'])->name('payroll.index');
            Route::post('/payroll/generate', [\App\Http\Controllers\Admin\HrPayrollController::class, 'generate'])->name('payroll.generate');
            Route::post('/payroll/{id}/pay', [\App\Http\Controllers\Admin\HrPayrollController::class, 'paySingle'])->name('payroll.pay_single');
            Route::post('/payroll/pay-bulk', [\App\Http\Controllers\Admin\HrPayrollController::class, 'payBulk'])->name('payroll.pay_bulk');
            Route::post('/payroll/post-entries', [\App\Http\Controllers\Admin\HrPayrollController::class, 'postEntries'])->name('payroll.post_entries');
            Route::get('/payroll/{id}/print', [\App\Http\Controllers\Admin\HrPayrollController::class, 'printPayslip'])->name('payroll.print');
            Route::post('/payroll/{id}/reverse', [\App\Http\Controllers\Admin\HrPayrollController::class, 'reversePayment'])->name('payroll.reverse');

            Route::get('/loans', [\App\Http\Controllers\Admin\HrLoanController::class, 'index'])->name('loans.index');
            Route::post('/loans', [\App\Http\Controllers\Admin\HrLoanController::class, 'store'])->name('loans.store');
            Route::put('/loans/{id}', [\App\Http\Controllers\Admin\HrLoanController::class, 'update'])->name('loans.update'); 
            Route::delete('/loans/{id}', [\App\Http\Controllers\Admin\HrLoanController::class, 'destroy'])->name('loans.destroy'); 
            
            // ✅ الحركات المالية (علاوات وخصومات) - مع إضافة update
            Route::get('/financials', [\App\Http\Controllers\Admin\HrFinancialController::class, 'index'])->name('financials.index');
            Route::post('/financials', [\App\Http\Controllers\Admin\HrFinancialController::class, 'store'])->name('financials.store');
            Route::put('/financials/{id}', [\App\Http\Controllers\Admin\HrFinancialController::class, 'update'])->name('financials.update');
            Route::delete('/financials/{id}', [\App\Http\Controllers\Admin\HrFinancialController::class, 'destroy'])->name('financials.destroy');
            Route::post('/loans/{id}/payment', [\App\Http\Controllers\Admin\HrLoanController::class, 'recordPayment'])->name('loans.record_payment');

            Route::get('/custodies', [\App\Http\Controllers\Admin\HrCustodyController::class, 'index'])->name('custodies.index');
            Route::post('/custodies', [\App\Http\Controllers\Admin\HrCustodyController::class, 'store'])->name('custodies.store');
            Route::post('/custodies/{id}/status', [\App\Http\Controllers\Admin\HrCustodyController::class, 'updateStatus'])->name('custodies.status');
            Route::delete('custodies/{id}', [\App\Http\Controllers\Admin\HrCustodyController::class, 'destroy'])->name('custodies.destroy');
            Route::put('custodies/{id}', [\App\Http\Controllers\Admin\HrCustodyController::class, 'update'])->name('custodies.update');
            Route::post('/custodies/return-multiple', [\App\Http\Controllers\Admin\HrCustodyController::class, 'returnMultiple'])->name('custodies.return_multiple');

            Route::get('/appraisals', [\App\Http\Controllers\Admin\HrAppraisalController::class, 'index'])->name('appraisals.index');
            Route::post('/appraisals', [\App\Http\Controllers\Admin\HrAppraisalController::class, 'store'])->name('appraisals.store');
            Route::put('/appraisals/{id}', [\App\Http\Controllers\Admin\HrAppraisalController::class, 'update'])->name('appraisals.update');
            Route::delete('/appraisals/{id}', [\App\Http\Controllers\Admin\HrAppraisalController::class, 'destroy'])->name('appraisals.destroy');

            Route::get('/penalties', [\App\Http\Controllers\Admin\HrPenaltyController::class, 'index'])->name('penalties.index');
            Route::post('/penalties', [\App\Http\Controllers\Admin\HrPenaltyController::class, 'store'])->name('penalties.store');
            Route::put('/penalties/{id}', [\App\Http\Controllers\Admin\HrPenaltyController::class, 'update'])->name('penalties.update');
            Route::delete('/penalties/{id}', [\App\Http\Controllers\Admin\HrPenaltyController::class, 'destroy'])->name('penalties.destroy');

            Route::get('/eos', [\App\Http\Controllers\Admin\HrEndOfServiceController::class, 'index'])->name('eos.index');
            Route::post('/eos', [\App\Http\Controllers\Admin\HrEndOfServiceController::class, 'store'])->name('eos.store');
            Route::get('/eos/calculate/{id}', [\App\Http\Controllers\Admin\HrEndOfServiceController::class, 'calculateDues']);
            Route::get('/eos/{id}/print', [\App\Http\Controllers\Admin\HrEndOfServiceController::class, 'printClearance'])->name('eos.print');
            Route::get('/calculate/{id}', [\App\Http\Controllers\Admin\HrEndOfServiceController::class, 'calculateDues'])->name('eos.calculate');
                Route::post('/{id}/reverse', [\App\Http\Controllers\Admin\HrEndOfServiceController::class, 'reverse'])->name('eos.reverse');

            Route::get('/candidates', [\App\Http\Controllers\Admin\HrJobCandidateController::class, 'index'])->name('candidates.index');
            Route::post('/candidates', [\App\Http\Controllers\Admin\HrJobCandidateController::class, 'store'])->name('candidates.store');
            Route::post('/candidates/{id}/status', [\App\Http\Controllers\Admin\HrJobCandidateController::class, 'updateStatus'])->name('candidates.status');

            Route::get('/trainees', [\App\Http\Controllers\Admin\HrTraineeController::class, 'index'])->name('trainees.index');
            Route::post('/trainees', [\App\Http\Controllers\Admin\HrTraineeController::class, 'store'])->name('trainees.store');
            Route::put('/trainees/{id}', [\App\Http\Controllers\Admin\HrTraineeController::class, 'update'])->name('trainees.update'); 
            Route::delete('/trainees/{id}', [\App\Http\Controllers\Admin\HrTraineeController::class, 'destroy'])->name('trainees.destroy'); 
            Route::post('/trainees/{id}/status', [\App\Http\Controllers\Admin\HrTraineeController::class, 'updateStatus'])->name('trainees.status');
        });
    });

    // ==============================================================================
    // 💬 نظام المحادثات الفورية (Chat System)
    // ==============================================================================
    Route::middleware(['auth:web,teacher,guardian'])->group(function () {
        Route::delete('/chat/message/{id}/delete', [App\Http\Controllers\ChatController::class, 'deleteMessage'])->name('chat.message.delete');
        Route::post('/chat/message/{id}/update', [App\Http\Controllers\ChatController::class, 'updateMessage'])->name('chat.message.update');
        Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
        Route::get('/chat/fetch/{id}', [ChatController::class, 'fetchMessages'])->name('chat.fetch');
        Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');
        Route::get('/chat/search-users', [ChatController::class, 'searchUsers'])->name('chat.search');
        Route::post('/chat/start', [ChatController::class, 'startConversation'])->name('chat.start');
        Route::get('/chat/user-profile', [ChatController::class, 'getUserProfile'])->name('chat.profile');
        Route::post('/chat/update-status', [ChatController::class, 'updateChatStatus'])->name('chat.status');
        Route::post('/chat/group/create', [App\Http\Controllers\ChatController::class, 'createGroup'])->name('chat.group.create');
        Route::get('/chat/group/{id}/members', [App\Http\Controllers\ChatController::class, 'getGroupMembers']);
        Route::post('/chat/group/{id}/add-member', [App\Http\Controllers\ChatController::class, 'addGroupMember']);
        Route::post('/chat/group/{id}/remove-member', [App\Http\Controllers\ChatController::class, 'removeGroupMember']);
        Route::post('/chat/group/{id}/update-name', [App\Http\Controllers\ChatController::class, 'updateGroupName']);
    });

    // ==============================================================================
    // لوحات التحكم الإضافية (Teacher, Student, Guardian)
    // ==============================================================================
    
    // بوابة المعلم
    Route::middleware(['auth:teacher'])->prefix('teacher')->name('teacher.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Teacher\DashboardController::class, 'index'])->name('dashboard');
        
        // 🔥 مسارات رصد الدرجات الذكي (Smart Grading) 🔥
        Route::get('/marks/{section_id}/{subject_id}', [\App\Http\Controllers\Teacher\MarkController::class, 'index'])->name('marks.index');
        Route::post('/marks/store', [\App\Http\Controllers\Teacher\MarkController::class, 'store'])->name('marks.store');

        Route::get('/schedule', [\App\Http\Controllers\Teacher\ScheduleController::class, 'index'])->name('schedule.index');
        Route::get('/attendance/{schedule_id}', [\App\Http\Controllers\Teacher\AttendanceController::class, 'create'])->name('attendance.create');
        Route::post('/attendance/{schedule_id}', [\App\Http\Controllers\Teacher\AttendanceController::class, 'store'])->name('attendance.store');
        Route::get('/evaluations/{schedule_id}', [\App\Http\Controllers\Teacher\EvaluationController::class, 'create'])->name('evaluations.create');
        Route::post('/evaluations/{schedule_id}', [\App\Http\Controllers\Teacher\EvaluationController::class, 'store'])->name('evaluations.store');
        Route::get('/my-schedule', [\App\Http\Controllers\Teacher\VirtualClassController::class, 'mySchedule'])->name('schedule');
        Route::get('/virtual-room/{id}', [\App\Http\Controllers\Teacher\VirtualClassController::class, 'enterRoom'])->name('virtual_room');
    });


    // بوابة الطالب
    Route::middleware(['auth:student'])->prefix('student')->name('student.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Student\StudentDashboardController::class, 'index'])->name('dashboard');
        Route::get('/live-classes/{id}/join', [\App\Http\Controllers\Student\StudentDashboardController::class, 'joinLiveClass'])->name('live_classes.join');
    });
    
    // بوابة ولي الأمر (Guardian)
    Route::middleware(['auth:guardian'])->prefix('parent')->name('parent.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Guardian\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/student/{id}', [\App\Http\Controllers\Guardian\StudentController::class, 'show'])->name('student.profile');
        Route::get('/student/{student_id}/certificate/{term_id}', [\App\Http\Controllers\Admin\CertificateController::class, 'printCertificate'])->name('certificate.print');
        Route::post('/student/{id}/leave-request', [\App\Http\Controllers\Guardian\StudentController::class, 'submitLeaveRequest'])->name('student.leave_request');
        Route::get('student/{id}/profile', [\App\Http\Controllers\Guardian\StudentController::class, 'showProfile'])->name('student.profile');
    });

    require __DIR__.'/auth.php';
});