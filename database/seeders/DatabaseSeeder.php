<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Faker\Factory as Faker;
use App\Models\User;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('ar_SA');
        $now = Carbon::now();
        $pastMonth = Carbon::now()->subMonth();
        $pastWeek = Carbon::now()->subWeek();
        $futureMonth = Carbon::now()->addMonth();

        // ========================================================
        // 🧹 1. تنظيف قاعدة البيانات بالكامل لتجنب التكرار
        // ========================================================
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        $tables = [
            'users', 'model_has_roles', 'settings', 'branches', 'academic_years', 'academic_terms', 'academic_year_branch', 'financial_years',
            'grades', 'branch_grade', 'sections', 'subjects', 'teachers', 'subject_teacher',
            'financial_accounts', 'journal_entries', 'journal_items', 'suppliers', 'products', 'guardians', 'students', 
            'student_contracts', 'student_contract_items', 'student_installments', 'payments', 'installment_payments',
            'purchase_invoices', 'purchase_invoice_items', 'purchase_installments', 'purchase_returns', 
            'asset_categories', 'assets', 'asset_installments', 'transport_routes', 'buses', // 🔥 إضافة خطوط السير
            'hr_departments', 'hr_shifts', 'hr_job_grades', 'hr_employees', 'hr_contracts'
        ];

        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // ========================================================
        // 👑 2. الإدارة والإعدادات وتأمين الصلاحيات
        // ========================================================
        $adminId = DB::table('users')->insertGetId([
            'name' => 'عبدالرحمن السيد السعدني',
            'email' => 'admin@smartstudents.edu.om',
            'password' => Hash::make('12345678'),
            'status' => 'active',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $adminUser = User::find($adminId);
        $superAdminRole = Role::where('name', 'Super Admin')->first() ?? Role::where('name', 'Admin')->first();
        if ($adminUser && $superAdminRole) {
            $adminUser->assignRole($superAdminRole);
        }

        DB::table('settings')->insert([
            'company_name_ar' => 'اركان السد العالي للاستثمار ش م م',
            'company_name_en' => 'Arkan Al Sadd Al Aali Investment LLC',
            'cr_number' => '1642397',
            'email' => 'info@smartstudents.edu.om',
            'phone' => '+96899999999',
            'address_ar' => 'سلطنة عمان',
            'created_at' => $now,
        ]);

        // ========================================================
        // 🏢 3. الفروع والحسابات المالية (الشجرة المحاسبية)
        // ========================================================
        $branchId = DB::table('branches')->insertGetId([
            'name_ar' => 'مدارس الطالب الذكي - فرع السيب',
            'phone' => '96824123456',
            'address' => 'السيب، مسقط، سلطنة عُمان',
            'is_active' => 1,
            'created_at' => $now,
        ]);

        $assetAccountId = DB::table('financial_accounts')->insertGetId([
            'account_code' => '1000', 'name_ar' => 'الأصول المتداولة', 'account_type' => 'asset', 'normal_balance' => 'debit', 'created_at' => $now
        ]);
        
        $cashAccountId = DB::table('financial_accounts')->insertGetId([
            'account_code' => '1101', 'name_ar' => 'الخزينة الرئيسية', 'account_type' => 'asset', 'parent_id' => $assetAccountId, 'opening_balance' => 0, 'balance' => 25000, 'created_at' => $now
        ]);

        $revenueAccountId = DB::table('financial_accounts')->insertGetId([
            'account_code' => '4000', 'name_ar' => 'إيرادات الرسوم الدراسية', 'account_type' => 'revenue', 'normal_balance' => 'credit', 'balance' => 0, 'created_at' => $now
        ]);

        $expenseAccountId = DB::table('financial_accounts')->insertGetId([
            'account_code' => '5000', 'name_ar' => 'المصروفات التشغيلية', 'account_type' => 'expense', 'normal_balance' => 'debit', 'balance' => 0, 'created_at' => $now
        ]);

        // ========================================================
        // 📅 4. السنوات والصفوف الدراسية
        // ========================================================
        $academicYearId = DB::table('academic_years')->insertGetId([
            'name' => '2025-2026', 'start_date' => '2025-08-01', 'end_date' => '2026-06-30', 'is_current' => 1, 'created_at' => $now,
        ]);

        DB::table('academic_terms')->insert([
            ['academic_year_id' => $academicYearId, 'name_ar' => 'الفصل الدراسي الأول', 'start_date' => '2025-08-15', 'end_date' => '2026-01-15', 'is_active' => 1, 'created_at' => $now]
        ]);

        DB::table('academic_year_branch')->insert(['branch_id' => $branchId, 'academic_year_id' => $academicYearId, 'created_at' => $now]);

        $grade1 = DB::table('grades')->insertGetId(['name_ar' => 'الصف الأول', 'level_order' => 1, 'created_at' => $now]);
        $grade2 = DB::table('grades')->insertGetId(['name_ar' => 'الصف الثاني', 'level_order' => 2, 'created_at' => $now]);

        DB::table('branch_grade')->insert([
            ['branch_id' => $branchId, 'grade_id' => $grade1, 'created_at' => $now],
            ['branch_id' => $branchId, 'grade_id' => $grade2, 'created_at' => $now],
        ]);

        $sectionId = DB::table('sections')->insertGetId([
            'branch_id' => $branchId, 'academic_year_id' => $academicYearId, 'grade_id' => $grade1, 'name_ar' => 'شعبة (أ)', 'max_capacity' => 25, 'created_at' => $now
        ]);

        // ========================================================
        // 🚌 5. الموارد البشرية، خطوط السير، والباصات (الهيكلة الجديدة) 🔥
        // ========================================================
        
        // 1. إنشاء السائق والمشرفة في الـ HR
        $driverId = DB::table('hr_employees')->insertGetId([
            'employee_code' => 'EMP-2001', 'branch_id' => $branchId, 'full_name_ar' => 'عبدالله السعدي',
            'phone' => '96899123456', 'national_id' => '1234567890', 'job_title' => 'سائق حافلة', 'status' => 'active',
            'join_date' => $pastMonth, 
            'basic_salary' => 350, 
            'driving_license_number' => 'OM-998877', 'driving_license_type' => 'ثقيل', 'driving_license_expiry' => clone $now->addYear(),
            'created_at' => $now, 'updated_at' => $now
        ]);

        $supervisorId = DB::table('hr_employees')->insertGetId([
            'employee_code' => 'EMP-2002', 'branch_id' => $branchId, 'full_name_ar' => 'فاطمة البلوشية',
            'phone' => '96899123457', 'national_id' => '1234567891', 'job_title' => 'مشرفة حافلة', 'status' => 'active',
            'join_date' => $pastMonth, 
            'basic_salary' => 300, 
            'created_at' => $now, 'updated_at' => $now
        ]);

        // 2. إنشاء خط السير المركزي (التسعير)
        $routeId = DB::table('transport_routes')->insertGetId([
            'branch_id' => $branchId, 'name' => 'السيب - المعبيلة الجنوبية',
            'monthly_fare_both' => 45.000, 'monthly_fare_single' => 25.000,
            'created_at' => $now, 'updated_at' => $now
        ]);

        // 3. إنشاء الحافلة وربطها بالخط والموظفين
        $busId = DB::table('buses')->insertGetId([
            'branch_id' => $branchId, 'academic_year_id' => $academicYearId, 'transport_route_id' => $routeId,
            'driver_id' => $driverId, 'supervisor_id' => $supervisorId, 'bus_number' => '1542 A', 
            'ownership_type' => 'rented', 'monthly_rent' => 350.000, 'capacity' => 30, 
            'insurance_expiry_date' => clone $now->addMonths(5), 'registration_expiry_date' => clone $now->addMonths(6),
            'created_at' => $now, 'updated_at' => $now
        ]);

        // ========================================================
        // 👨‍👩‍👦 6. أولياء الأمور والطلاب
        // ========================================================
        $guardianId = DB::table('guardians')->insertGetId([
            'branch_id' => $branchId, 'name_ar' => 'يوسف خلفان بن سعيد السيابي', 'email' => 'yousef@example.com',
            'password' => Hash::make('12345678'), 'phone' => '96899123456', 'national_id' => '1234567890', 'nationality_ar' => 'عماني', 'created_at' => $now,
        ]);

        $studentId = DB::table('students')->insertGetId([
            'branch_id' => $branchId, 'parent_id' => $guardianId, 'academic_year_id' => $academicYearId, 'grade_id' => $grade1, 'section_id' => $sectionId,
            'first_name' => 'ناصر', 'second_name' => 'يوسف', 'tribe' => 'السيابي', 'dob' => '2018-05-15', 'gender' => 'male', 'status' => 'active', 'transport_type' => 'bus_both', 'created_at' => $now,
        ]);

        // ========================================================
        // 💰 7. الرسوم والعقود المالية للطلاب 
        // ========================================================
        $feeProduct = DB::table('products')->insertGetId([
            'branch_id' => $branchId, 'name_ar' => 'رسوم دراسية كاملة', 'type' => 'service', 'selling_price' => 1200, 'is_active' => 1, 'created_at' => $now
        ]);

        $contractId = DB::table('student_contracts')->insertGetId([
            'contract_number' => 'CON-2026-001', 'branch_id' => $branchId, 'student_id' => $studentId, 'academic_year_id' => $academicYearId, 
            'transport_route_id' => $routeId, 'morning_bus_id' => $busId, 'afternoon_bus_id' => $busId,
            'contract_date' => $pastMonth, 'sub_total' => 1200, 'grand_total' => 1200, 
            'paid_amount' => 300, 'total_remaining' => 900, 'financial_status' => 'partially_paid', 'contract_status' => 'active', 'created_at' => $now,
        ]);

        DB::table('student_contract_items')->insert([
            'student_contract_id' => $contractId, 'product_id' => $feeProduct, 'unit_price' => 1200, 'quantity' => 1, 'row_total' => 1200, 'created_at' => $now
        ]);

        DB::table('student_installments')->insert([
            'student_contract_id' => $contractId, 'student_id' => $studentId, 'amount' => 300, 'paid_amount' => 300, 'remaining_amount' => 0, 
            'due_date' => $pastMonth, 'status' => 'paid', 'paid_date' => $pastMonth, 'created_at' => $now,
        ]);

        // ========================================================
        // 📦 8. الموردين والمشتريات 
        // ========================================================
        $supplierId = DB::table('suppliers')->insertGetId([
            'branch_id' => $branchId, 'company_name' => 'شركة الأنوار للتوريدات', 'phone' => '96822223333', 'is_active' => 1, 'created_at' => $now
        ]);

        $bookProduct = DB::table('products')->insertGetId([
            'branch_id' => $branchId, 'name_ar' => 'كتب الصف الأول', 'sku' => 'BOK-01', 'type' => 'product', 'cost_price' => 10, 'selling_price' => 15, 'quantity' => 100, 'created_at' => $now
        ]);

        $purchaseInvoiceId = DB::table('purchase_invoices')->insertGetId([
            'branch_id' => $branchId, 'supplier_id' => $supplierId, 'invoice_number' => 'PINV-001', 'invoice_date' => $pastMonth,
            'grand_total' => 1000, 'paid_amount' => 200, 'due_amount' => 800, 'payment_type' => 'manual_installment', 'payment_status' => 'partial', 'created_at' => $now
        ]);

        DB::table('purchase_invoice_items')->insert([
            'purchase_invoice_id' => $purchaseInvoiceId, 'product_id' => $bookProduct, 'quantity' => 100, 'unit_cost' => 10, 'row_total' => 1000, 'created_at' => $now
        ]);

        // ========================================================
        // ⚖️ 9. السندات المحاسبية ونظام القيد المزدوج 
        // ========================================================
        $paymentId = DB::table('payments')->insertGetId([
            'financial_account_id' => $cashAccountId, 'receipt_number' => 'REC-001', 'user_id' => $adminId,
            'payment_type' => 'advance', 'payable_type' => 'App\Models\Student', 'payable_id' => $studentId,
            'amount' => 300, 'payment_date' => $pastMonth, 'transaction_details' => 'سداد القسط الأول (دفعة مقدمة) للطالب ناصر السيابي', 'created_at' => $now
        ]);

        $journalId1 = DB::table('journal_entries')->insertGetId([
            'branch_id' => $branchId, 'entry_number' => 'JE-2026-0001', 'entry_date' => $pastMonth,
            'description' => 'إثبات تحصيل إيراد دفعة مقدمة', 'reference_type' => 'App\Models\Payment', 'reference_id' => $paymentId,
            'created_by' => $adminId, 'created_at' => $now
        ]);

        DB::table('journal_items')->insert([
            ['journal_entry_id' => $journalId1, 'financial_account_id' => $cashAccountId, 'debit' => 300, 'credit' => 0, 'line_description' => 'من ح/ الخزينة', 'created_at' => $now],
            ['journal_entry_id' => $journalId1, 'financial_account_id' => $revenueAccountId, 'debit' => 0, 'credit' => 300, 'line_description' => 'إلى ح/ الإيرادات', 'created_at' => $now],
        ]);

        $expensePaymentId = DB::table('payments')->insertGetId([
            'financial_account_id' => $cashAccountId, 'receipt_number' => 'VOU-001', 'user_id' => $adminId,
            'payment_type' => 'installment', 'payable_type' => 'App\Models\Supplier', 'payable_id' => $supplierId,
            'amount' => 200, 'payment_date' => $pastMonth, 'transaction_details' => 'سداد دفعة نقدية لمورد (شركة الأنوار للتوريدات)', 'created_at' => $now
        ]);

        $journalId2 = DB::table('journal_entries')->insertGetId([
            'branch_id' => $branchId, 'entry_number' => 'JE-2026-0002', 'entry_date' => $pastMonth,
            'description' => 'إثبات سداد دفعة للموردين', 'reference_type' => 'App\Models\Payment', 'reference_id' => $expensePaymentId,
            'created_by' => $adminId, 'created_at' => $now
        ]);

        DB::table('journal_items')->insert([
            ['journal_entry_id' => $journalId2, 'financial_account_id' => $expenseAccountId, 'debit' => 200, 'credit' => 0, 'line_description' => 'من ح/ المشتريات/المصروفات', 'created_at' => $now],
            ['journal_entry_id' => $journalId2, 'financial_account_id' => $cashAccountId, 'debit' => 0, 'credit' => 200, 'line_description' => 'إلى ح/ الخزينة', 'created_at' => $now],
        ]);

        DB::table('financial_accounts')->where('id', $cashAccountId)->update(['balance' => 25100, 'updated_at' => $now]);
        DB::table('financial_accounts')->where('id', $revenueAccountId)->update(['balance' => 300, 'updated_at' => $now]);
        DB::table('financial_accounts')->where('id', $expenseAccountId)->update(['balance' => 200, 'updated_at' => $now]);

        $this->command->info('✅ تم زرع البيانات الشاملة بنجاح والتوافق التام مع النظام الجديد!');
    }
}