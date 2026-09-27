<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Faker\Factory as Faker;
use App\Models\User;
use Spatie\Permission\Models\Role;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('ar_SA');
        $now = Carbon::now();
        $pastMonth = Carbon::now()->subMonth();
        $pastWeek = Carbon::now()->subWeek();
        $futureMonth = Carbon::now()->addMonth();

        // ========================================================
        // 🧹 1. تنظيف قاعدة البيانات بالكامل
        // ========================================================
        $this->command->info('🧹 جاري تنظيف قاعدة البيانات...');
        
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        $tables = [
            'users', 'model_has_roles', 'settings', 'branches', 'academic_years', 'academic_terms', 'academic_year_branch',
            'grades', 'branch_grade', 'sections', 'subjects', 'teachers', 'subject_teacher',
            'financial_accounts', 'journal_entries', 'journal_items', 'suppliers', 'products', 'guardians', 'students', 
            'student_contracts', 'student_contract_items', 'student_installments', 'payments', 'installment_payments',
            'purchase_invoices', 'purchase_invoice_items', 'purchase_installments', 'purchase_returns', 
            'asset_categories', 'assets', 'asset_installments', 'transport_routes', 'buses',
            'hr_departments', 'hr_shifts', 'hr_job_grades', 'hr_financial_types', 'hr_employees', 'hr_contracts',
            'hr_contract_allowances', 'hr_attendances', 'hr_loans', 'hr_penalties', 'hr_payrolls', 'hr_payroll_details',
            'hr_leave_requests', 'hr_leave_types', 'hr_shift_rosters', 'hr_employee_financials'
        ];

        foreach ($tables as $table) {
            try {
                if (Schema::hasTable($table)) {
                    DB::table($table)->truncate();
                }
            } catch (\Exception $e) {
                // تجاهل الجداول غير الموجودة
            }
        }
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // ========================================================
        // 👑 2. الإدارة والإعدادات
        // ========================================================
        $this->command->info('👑 جاري إعداد المدير والإعدادات...');
        
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

        if (Schema::hasTable('settings')) {
            DB::table('settings')->insert([
                'company_name_ar' => 'اركان السد العالي للاستثمار ش م م',
                'company_name_en' => 'Arkan Al Sadd Al Aali Investment LLC',
                'cr_number' => '1642397',
                'email' => 'info@smartstudents.edu.om',
                'phone' => '+96899999999',
                'address_ar' => 'سلطنة عمان - مسقط',
                'created_at' => $now,
            ]);
        }

        // ========================================================
        // 🏢 3. الفروع (3 فروع)
        // ========================================================
        $this->command->info('🏢 جاري إنشاء الفروع...');
        
        $branchIds = [];
        $branchNames = [
            ['name' => 'مدارس الطالب الذكي - فرع السيب', 'phone' => '96824123456', 'address' => 'السيب، مسقط'],
            ['name' => 'مدارس الطالب الذكي - فرع الخوير', 'phone' => '96824123457', 'address' => 'الخوير، مسقط'],
            ['name' => 'مدارس الطالب الذكي - فرع بوشر', 'phone' => '96824123458', 'address' => 'بوشر، مسقط'],
        ];

        $branchColumns = Schema::getColumnListing('branches');
        
        foreach ($branchNames as $branch) {
            $branchData = [
                'phone' => $branch['phone'],
                'address' => $branch['address'],
                'is_active' => 1,
                'created_at' => $now,
            ];
            
            if (in_array('name_ar', $branchColumns)) {
                $branchData['name_ar'] = $branch['name'];
            } elseif (in_array('name', $branchColumns)) {
                $branchData['name'] = $branch['name'];
            }
            
            $branchIds[] = DB::table('branches')->insertGetId($branchData);
        }
        $branchId = $branchIds[0]; // الفرع الرئيسي

        // ========================================================
        // 💰 4. الشجرة المحاسبية
        // ========================================================
        $this->command->info('💰 جاري بناء الشجرة المحاسبية...');
        
        $financialColumns = Schema::getColumnListing('financial_accounts');
        
        $insertFinancialAccount = function($data) use ($financialColumns, $now) {
            $data['created_at'] = $now;
            if (!in_array('normal_balance', $financialColumns)) unset($data['normal_balance']);
            if (!in_array('parent_id', $financialColumns)) unset($data['parent_id']);
            if (!in_array('opening_balance', $financialColumns)) unset($data['opening_balance']);
            if (!in_array('balance', $financialColumns)) unset($data['balance']);
            return DB::table('financial_accounts')->insertGetId($data);
        };
        
        $assetAccountId = $insertFinancialAccount([
            'account_code' => '1000', 'name_ar' => 'الأصول المتداولة', 'account_type' => 'asset', 'normal_balance' => 'debit'
        ]);
        
        $cashAccountId = $insertFinancialAccount([
            'account_code' => '1101', 'name_ar' => 'الخزينة الرئيسية', 'account_type' => 'asset', 'parent_id' => $assetAccountId, 'opening_balance' => 50000, 'balance' => 50000
        ]);

        $bankAccountId = $insertFinancialAccount([
            'account_code' => '1102', 'name_ar' => 'البنك الوطني - الحساب الجاري', 'account_type' => 'asset', 'parent_id' => $assetAccountId, 'opening_balance' => 150000, 'balance' => 150000
        ]);

        $revenueAccountId = $insertFinancialAccount([
            'account_code' => '4000', 'name_ar' => 'إيرادات الرسوم الدراسية', 'account_type' => 'revenue', 'normal_balance' => 'credit', 'balance' => 0
        ]);

        $expenseAccountId = $insertFinancialAccount([
            'account_code' => '5000', 'name_ar' => 'المصروفات التشغيلية', 'account_type' => 'expense', 'normal_balance' => 'debit', 'balance' => 0
        ]);

        $salaryExpenseId = $insertFinancialAccount([
            'account_code' => '5100', 'name_ar' => 'رواتب الموظفين', 'account_type' => 'expense', 'parent_id' => $expenseAccountId, 'normal_balance' => 'debit', 'balance' => 0
        ]);

        // ========================================================
        // 📅 5. السنوات الدراسية والصفوف
        // ========================================================
        $this->command->info('📅 جاري إعداد السنوات الدراسية...');
        
        $academicYearId = DB::table('academic_years')->insertGetId([
            'name' => '2025-2026', 'start_date' => '2025-08-01', 'end_date' => '2026-06-30', 'is_current' => 1, 'created_at' => $now,
        ]);

        if (Schema::hasTable('academic_terms')) {
            DB::table('academic_terms')->insert([
                ['academic_year_id' => $academicYearId, 'name_ar' => 'الفصل الدراسي الأول', 'start_date' => '2025-08-15', 'end_date' => '2026-01-15', 'is_active' => 1, 'created_at' => $now],
                ['academic_year_id' => $academicYearId, 'name_ar' => 'الفصل الدراسي الثاني', 'start_date' => '2026-01-20', 'end_date' => '2026-06-15', 'is_active' => 0, 'created_at' => $now],
            ]);
        }

        if (Schema::hasTable('academic_year_branch')) {
            foreach ($branchIds as $bId) {
                DB::table('academic_year_branch')->insert(['branch_id' => $bId, 'academic_year_id' => $academicYearId, 'created_at' => $now]);
            }
        }

        // الصفوف الدراسية
        $gradeIds = [];
        $gradeNames = ['الصف الأول', 'الصف الثاني', 'الصف الثالث', 'الصف الرابع', 'الصف الخامس', 'الصف السادس'];
        foreach ($gradeNames as $index => $name) {
            $gradeIds[] = DB::table('grades')->insertGetId(['name_ar' => $name, 'level_order' => $index + 1, 'created_at' => $now]);
        }

        if (Schema::hasTable('branch_grade')) {
            foreach ($branchIds as $bId) {
                foreach ($gradeIds as $gId) {
                    DB::table('branch_grade')->insert(['branch_id' => $bId, 'grade_id' => $gId, 'created_at' => $now]);
                }
            }
        }

        // الشعب الدراسية
        $sectionIds = [];
        $sectionLetters = ['أ', 'ب', 'ج'];
        foreach ($branchIds as $bId) {
            foreach ($gradeIds as $gId) {
                foreach ($sectionLetters as $letter) {
                    $sectionIds[] = DB::table('sections')->insertGetId([
                        'branch_id' => $bId, 
                        'academic_year_id' => $academicYearId, 
                        'grade_id' => $gId, 
                        'name_ar' => "شعبة ($letter)", 
                        'max_capacity' => 25, 
                        'created_at' => $now
                    ]);
                }
            }
        }

        // المواد الدراسية
        $subjectNames = ['اللغة العربية', 'اللغة الإنجليزية', 'الرياضيات', 'العلوم', 'الدراسات الاجتماعية', 'التربية الإسلامية', 'الحاسب الآلي', 'التربية البدنية'];
        $subjectIds = [];
        foreach ($subjectNames as $name) {
            $subjectIds[] = DB::table('subjects')->insertGetId(['name_ar' => $name, 'created_at' => $now]);
        }

        // ========================================================
        // 👨‍🏫 6. المعلمين (20 معلم) - نسخة مرنة
        // ========================================================
        $this->command->info('👨‍🏫 جاري تعيين المعلمين...');

        $teacherIds = [];
        $teacherNames = [
            'محمد بن سعيد الهنائي', 'فاطمة بنت خلفان البلوشية', 'أحمد بن سالم الريامي', 'مريم بنت يوسف الغافرية',
            'خالد بن عبدالله الكندي', 'نورة بنت حمد الشكيلية', 'سالم بن علي الحبسي', 'زهراء بنت سعيد الهوتية',
            'يوسف بن حمد البوسعيدي', 'هند بنت ناصر اللواتية', 'عمر بن سعيد المعمري', 'لطيفة بنت أحمد الرشيدية',
            'حسن بن خميس الظاهري', 'سعاد بنت محمد الإماراتي', 'طارق بن زيد الشعيبي', 'عائشة بنت سالم الخروصي',
            'ماجد بن عبدالله الحارثي', 'وفاء بنت حمد الغنبوصية', 'راشد بن سعيد الأميري', 'شمسة بنت علي المعمرية'
        ];

        $teacherColumns = Schema::getColumnListing('teachers');

        foreach ($teacherNames as $index => $name) {
            $teacherData = [
                'branch_id' => $branchIds[$index % 3],
                'phone' => '968' . $faker->numerify('9#######'),
                'email' => 'teacher' . ($index + 1) . '@smartstudents.edu.om',
                'password' => Hash::make('12345678'),
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (in_array('name_ar', $teacherColumns)) {
                $teacherData['name_ar'] = $name;
            } elseif (in_array('full_name_ar', $teacherColumns)) {
                $teacherData['full_name_ar'] = $name;
            } elseif (in_array('name', $teacherColumns)) {
                $teacherData['name'] = $name;
            }

            if (in_array('national_id', $teacherColumns)) {
                $teacherData['national_id'] = $faker->numerify('############');
            }
            if (in_array('join_date', $teacherColumns)) {
                $teacherData['join_date'] = $faker->dateTimeBetween('-3 years', '-1 month')->format('Y-m-d');
            }
            if (in_array('basic_salary', $teacherColumns)) {
                $teacherData['basic_salary'] = $faker->numberBetween(400, 800);
            }
            if (in_array('qualification', $teacherColumns)) {
                $teacherData['qualification'] = $faker->randomElement(['بكالوريوس', 'ماجستير', 'دبلوم']);
            }
            if (in_array('specialization', $teacherColumns)) {
                $teacherData['specialization'] = $faker->randomElement($subjectNames);
            }

            $teacherIds[] = DB::table('teachers')->insertGetId($teacherData);
        }

        // ربط المعلمين بالمواد
        if (Schema::hasTable('subject_teacher')) {
            foreach ($teacherIds as $tId) {
                $randomSubjects = $faker->randomElements($subjectIds, $faker->numberBetween(1, 3));
                foreach ($randomSubjects as $sId) {
                    DB::table('subject_teacher')->insert([
                        'subject_id' => $sId,
                        'teacher_id' => $tId,
                        'created_at' => $now,
                    ]);
                }
            }
        }

        // ========================================================
        // 🏢 7. الموارد البشرية (HR)
        // ========================================================
        $this->command->info('🏢 جاري إعداد الموارد البشرية...');
        
        // الأقسام
        $deptIds = [];
        $deptNames = ['الإدارة', 'المحاسبة', 'شؤون الطلاب', 'النقل المدرسي', 'الصيانة', 'المشتريات'];
        foreach ($deptNames as $name) {
            $deptIds[] = DB::table('hr_departments')->insertGetId(['name_ar' => $name, 'created_at' => $now]);
        }

        // الورديات
        $shiftIds = [];
        $shifts = [
            ['name' => 'الوردية الصباحية', 'start' => '07:00', 'end' => '14:00'],
            ['name' => 'الوردية المسائية', 'start' => '14:00', 'end' => '21:00'],
            ['name' => 'وردية إدارية', 'start' => '08:00', 'end' => '16:00'],
        ];
        foreach ($shifts as $shift) {
            $shiftIds[] = DB::table('hr_shifts')->insertGetId([
                'name_ar' => $shift['name'],
                'start_time' => $shift['start'],
                'end_time' => $shift['end'],
                'grace_period_minutes' => 15,
                'created_at' => $now,
            ]);
        }

        // الدرجات الوظيفية
        $gradeJobIds = [];
        $jobGrades = [
            ['name' => 'موظف', 'min' => 250, 'max' => 400],
            ['name' => 'مشرف', 'min' => 400, 'max' => 600],
            ['name' => 'مدير قسم', 'min' => 600, 'max' => 900],
            ['name' => 'مدير', 'min' => 900, 'max' => 1500],
        ];
        foreach ($jobGrades as $grade) {
            $gradeJobIds[] = DB::table('hr_job_grades')->insertGetId([
                'name_ar' => $grade['name'],
                'min_basic_salary' => $grade['min'],
                'max_basic_salary' => $grade['max'],
                'created_at' => $now,
            ]);
        }

        // الأنواع المالية
        $financialTypeIds = [];
        $financialTypes = [
            ['name' => 'بدل مواصلات', 'type' => 'allowance'],
            ['name' => 'بدل سكن', 'type' => 'allowance'],
            ['name' => 'بدل طبيعة عمل', 'type' => 'allowance'],
            ['name' => 'مكافأة نهاية سنة', 'type' => 'allowance'],
            ['name' => 'جزاء إداري', 'type' => 'deduction'],
            ['name' => 'خصم تأخير', 'type' => 'deduction'],
            ['name' => 'خصم غياب', 'type' => 'deduction'],
            ['name' => 'سداد سلفة موظف', 'type' => 'deduction'],
        ];
        foreach ($financialTypes as $type) {
            $financialTypeIds[] = DB::table('hr_financial_types')->insertGetId([
                'name_ar' => $type['name'],
                'type' => $type['type'],
                'value_type' => 'fixed',
                'created_at' => $now,
            ]);
        }

        // ========================================================
        // 🚌 8. السواقين والمشرفين والموظفين (20 موظف)
        // ========================================================
        $this->command->info('🚌 جاري تعيين السواقين والمشرفين...');
        
        $employeeIds = [];
        $employeeData = [
            ['name' => 'عبدالله بن سعيد السعدي', 'job' => 'سائق حافلة', 'salary' => 350, 'dept' => $deptIds[3], 'shift' => $shiftIds[0]],
            ['name' => 'فاطمة بنت خميس البلوشية', 'job' => 'مشرفة حافلة', 'salary' => 300, 'dept' => $deptIds[3], 'shift' => $shiftIds[0]],
            ['name' => 'سالم بن ناصر الهنائي', 'job' => 'سائق حافلة', 'salary' => 350, 'dept' => $deptIds[3], 'shift' => $shiftIds[0]],
            ['name' => 'مريم بنت أحمد الريامية', 'job' => 'مشرفة حافلة', 'salary' => 300, 'dept' => $deptIds[3], 'shift' => $shiftIds[0]],
            ['name' => 'خالد بن يوسف الكندي', 'job' => 'سائق حافلة', 'salary' => 380, 'dept' => $deptIds[3], 'shift' => $shiftIds[1]],
            ['name' => 'نورة بنت سالم الغافرية', 'job' => 'مشرفة حافلة', 'salary' => 320, 'dept' => $deptIds[3], 'shift' => $shiftIds[1]],
            ['name' => 'يوسف بن حمد الحبسي', 'job' => 'محاسب', 'salary' => 550, 'dept' => $deptIds[1], 'shift' => $shiftIds[2]],
            ['name' => 'هند بنت سعيد الشكيلية', 'job' => 'موظف استقبال', 'salary' => 350, 'dept' => $deptIds[2], 'shift' => $shiftIds[2]],
            ['name' => 'عمر بن علي المعمري', 'job' => 'مدير شؤون الطلاب', 'salary' => 800, 'dept' => $deptIds[2], 'shift' => $shiftIds[2]],
            ['name' => 'لطيفة بنت محمد الرشيدية', 'job' => 'موظف موارد بشرية', 'salary' => 500, 'dept' => $deptIds[0], 'shift' => $shiftIds[2]],
            ['name' => 'حسن بن خميس الظاهري', 'job' => 'سائق حافلة', 'salary' => 360, 'dept' => $deptIds[3], 'shift' => $shiftIds[0]],
            ['name' => 'سعاد بنت أحمد الإماراتي', 'job' => 'مشرفة حافلة', 'salary' => 310, 'dept' => $deptIds[3], 'shift' => $shiftIds[1]],
            ['name' => 'طارق بن زيد الشعيبي', 'job' => 'فني صيانة', 'salary' => 400, 'dept' => $deptIds[4], 'shift' => $shiftIds[2]],
            ['name' => 'عائشة بنت سالم الخروصي', 'job' => 'موظف مشتريات', 'salary' => 450, 'dept' => $deptIds[5], 'shift' => $shiftIds[2]],
            ['name' => 'ماجد بن عبدالله الحارثي', 'job' => 'سائق حافلة', 'salary' => 370, 'dept' => $deptIds[3], 'shift' => $shiftIds[1]],
            ['name' => 'وفاء بنت حمد الغنبوصية', 'job' => 'مشرفة حافلة', 'salary' => 315, 'dept' => $deptIds[3], 'shift' => $shiftIds[0]],
            ['name' => 'راشد بن سعيد الأميري', 'job' => 'سائق حافلة', 'salary' => 355, 'dept' => $deptIds[3], 'shift' => $shiftIds[0]],
            ['name' => 'شمسة بنت علي المعمرية', 'job' => 'مشرفة حافلة', 'salary' => 305, 'dept' => $deptIds[3], 'shift' => $shiftIds[1]],
            ['name' => 'ناصر بن سالم الهوت', 'job' => 'مدير النقل', 'salary' => 900, 'dept' => $deptIds[3], 'shift' => $shiftIds[2]],
            ['name' => 'بلقيس بنت يوسف البوسعيدية', 'job' => 'موظف شؤون طلاب', 'salary' => 380, 'dept' => $deptIds[2], 'shift' => $shiftIds[2]],
        ];

        $hrEmpColumns = Schema::getColumnListing('hr_employees');

        foreach ($employeeData as $index => $emp) {
            $isDriver = strpos($emp['job'], 'سائق') !== false;
            
            $empRecord = [
                'employee_code' => 'EMP-' . str_pad(2001 + $index, 4, '0', STR_PAD_LEFT),
                'branch_id' => $branchId,
                'phone' => '968' . $faker->numerify('9#######'),
                'job_title' => $emp['job'],
                'status' => 'active',
                'join_date' => $faker->dateTimeBetween('-2 years', '-1 month')->format('Y-m-d'),
                'basic_salary' => $emp['salary'],
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (in_array('hr_department_id', $hrEmpColumns)) $empRecord['hr_department_id'] = $emp['dept'];
            if (in_array('hr_shift_id', $hrEmpColumns)) $empRecord['hr_shift_id'] = $emp['shift'];
            if (in_array('full_name_ar', $hrEmpColumns)) $empRecord['full_name_ar'] = $emp['name'];
            elseif (in_array('name_ar', $hrEmpColumns)) $empRecord['name_ar'] = $emp['name'];
            if (in_array('national_id', $hrEmpColumns)) $empRecord['national_id'] = $faker->numerify('############');
            if (in_array('nationality', $hrEmpColumns)) $empRecord['nationality'] = 'عماني';
            if (in_array('is_insured', $hrEmpColumns)) $empRecord['is_insured'] = 1;
            if (in_array('driving_license_number', $hrEmpColumns) && $isDriver) $empRecord['driving_license_number'] = 'OM-' . $faker->numerify('######');
            if (in_array('driving_license_type', $hrEmpColumns) && $isDriver) $empRecord['driving_license_type'] = 'ثقيل';
            if (in_array('driving_license_expiry', $hrEmpColumns) && $isDriver) $empRecord['driving_license_expiry'] = now()->addYears(2)->format('Y-m-d');

            $employeeIds[] = DB::table('hr_employees')->insertGetId($empRecord);
        }

        // ========================================================
        // 🚌 9. خطوط السير والباصات (8 خطوط، 12 باص)
        // ========================================================
        $this->command->info('🚌 جاري إنشاء خطوط السير والباصات...');
        
        $routeIds = [];
        $routes = [
            ['name' => 'السيب - المعبيلة الجنوبية', 'fare_both' => 45, 'fare_single' => 25],
            ['name' => 'السيب - المعبيلة الشمالية', 'fare_both' => 45, 'fare_single' => 25],
            ['name' => 'السيب - الوادي الكبير', 'fare_both' => 50, 'fare_single' => 28],
            ['name' => 'الخوير - الغبرة', 'fare_both' => 48, 'fare_single' => 27],
            ['name' => 'الخوير - الخوض', 'fare_both' => 52, 'fare_single' => 30],
            ['name' => 'بوشر - القرم', 'fare_both' => 40, 'fare_single' => 22],
            ['name' => 'بوشر - العذيبة', 'fare_both' => 45, 'fare_single' => 25],
            ['name' => 'بوشر - الرميس', 'fare_both' => 50, 'fare_single' => 28],
        ];

        foreach ($routes as $route) {
            $routeIds[] = DB::table('transport_routes')->insertGetId([
                'branch_id' => $branchId,
                'name' => $route['name'],
                'monthly_fare_both' => $route['fare_both'],
                'monthly_fare_single' => $route['fare_single'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // الباصات
        $busIds = [];
        $driverIds = array_slice($employeeIds, 0, 8);
        $supervisorIds = array_slice($employeeIds, 8, 8);
        
        $busColumns = Schema::getColumnListing('buses');
        
        for ($i = 0; $i < 12; $i++) {
            $busData = [
                'branch_id' => $branchId,
                'academic_year_id' => $academicYearId,
                'transport_route_id' => $routeIds[$i % count($routeIds)],
                'bus_number' => 'BUS-' . str_pad(1001 + $i, 4, '0', STR_PAD_LEFT),
                'ownership_type' => $faker->randomElement(['owned', 'rented']),
                'monthly_rent' => $faker->randomElement([0, 300, 350, 400]),
                'capacity' => $faker->randomElement([25, 30, 35, 40]),
                'insurance_expiry_date' => now()->addMonths($faker->numberBetween(3, 12))->format('Y-m-d'),
                'registration_expiry_date' => now()->addMonths($faker->numberBetween(3, 12))->format('Y-m-d'),
                'created_at' => $now,
                'updated_at' => $now,
            ];
            
            if (in_array('driver_id', $busColumns)) $busData['driver_id'] = $driverIds[$i % count($driverIds)];
            if (in_array('supervisor_id', $busColumns)) $busData['supervisor_id'] = $supervisorIds[$i % count($supervisorIds)];
            
            $busIds[] = DB::table('buses')->insertGetId($busData);
        }

        // ========================================================
        // 👨‍👩‍👦 10. أولياء الأمور والطلاب (60 طالب)
        // ========================================================
        $this->command->info('👨‍👩‍👦 جاري تسجيل أولياء الأمور والطلاب...');

        $guardianIds = [];
        $studentIds = [];

        $arabTribes = ['السيابي', 'البلوشي', 'الهنائي', 'الريامي', 'الكندي', 'الغافري', 'الحبسي', 'الهوتي', 'البوسعيدي', 'اللواتي', 'المعمري', 'الرشيدي', 'الظاهري', 'الشعيبي', 'الخروصي', 'الأميري', 'الغامدي', 'المعولي'];
        $firstNamesMale = ['ناصر', 'سالم', 'خالد', 'يوسف', 'أحمد', 'عبدالله', 'محمد', 'حسن', 'عمر', 'سعيد', 'راشد', 'ماجد', 'طارق', 'فهد', 'بدر'];
        $firstNamesFemale = ['فاطمة', 'مريم', 'نورة', 'هند', 'لطيفة', 'سعاد', 'عائشة', 'شمسة', 'بلقيس', 'وفاء', 'زهراء', 'سارة', 'ليلى', 'ريم', 'دانة'];
        $guardianNames = ['يوسف', 'سالم', 'خالد', 'أحمد', 'عبدالله', 'محمد', 'حسن', 'عمر', 'سعيد', 'راشد', 'ناصر', 'ماجد'];

        // 🔥 فحص القيم الفعلية لـ transport_type من information_schema
        $allowedTransportTypes = ['bus_both', 'bus_morning', 'bus_evening', 'private'];

        try {
            $enumValues = DB::select("
                SELECT COLUMN_TYPE 
                FROM INFORMATION_SCHEMA.COLUMNS 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'students' 
                AND COLUMN_NAME = 'transport_type'
            ");
            
            if (!empty($enumValues)) {
                $columnType = $enumValues[0]->COLUMN_TYPE;
                preg_match_all("/'([^']+)'/", $columnType, $matches);
                if (!empty($matches[1])) {
                    $allowedTransportTypes = $matches[1];
                }
            }
        } catch (\Exception $e) {
            // استخدام القيم الافتراضية لو فشل الفحص
        }

        $this->command->info("   📋 القيم الفعلية في الـ enum: " . json_encode($allowedTransportTypes));
        $guardianColumns = Schema::getColumnListing('guardians');
        $studentColumns = Schema::getColumnListing('students');

        for ($i = 0; $i < 60; $i++) {
            $tribe = $faker->randomElement($arabTribes);
            $guardianFirstName = $faker->randomElement($guardianNames);
            $guardianFullName = $guardianFirstName . ' بن ' . $faker->randomElement($guardianNames) . ' ' . $tribe;
            
            $guardianData = [
                'branch_id' => $branchIds[$i % 3],
                'email' => 'guardian' . ($i + 1) . '@example.com',
                'password' => Hash::make('12345678'),
                'phone' => '968' . $faker->numerify('9#######'),
                'created_at' => $now,
                'updated_at' => $now,
            ];
            
            if (in_array('name_ar', $guardianColumns)) $guardianData['name_ar'] = $guardianFullName;
            elseif (in_array('name', $guardianColumns)) $guardianData['name'] = $guardianFullName;
            if (in_array('national_id', $guardianColumns)) $guardianData['national_id'] = $faker->numerify('############');
            if (in_array('nationality_ar', $guardianColumns)) $guardianData['nationality_ar'] = 'عماني';
            
            $guardianIds[] = DB::table('guardians')->insertGetId($guardianData);

            $gender = $faker->randomElement(['male', 'female']);
            $firstName = $gender === 'male' ? $faker->randomElement($firstNamesMale) : $faker->randomElement($firstNamesFemale);
            $secondName = $guardianFirstName;
            
            $studentData = [
                'branch_id' => $branchIds[$i % 3],
                'parent_id' => end($guardianIds),
                'academic_year_id' => $academicYearId,
                'grade_id' => $faker->randomElement($gradeIds),
                'section_id' => $faker->randomElement($sectionIds),
                'first_name' => $firstName,
                'dob' => $faker->dateTimeBetween('-12 years', '-6 years')->format('Y-m-d'),
                'gender' => $gender,
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ];
            
            if (in_array('second_name', $studentColumns)) $studentData['second_name'] = $secondName;
            if (in_array('tribe', $studentColumns)) $studentData['tribe'] = $tribe;
            
            if (in_array('transport_type', $studentColumns)) {
                $studentData['transport_type'] = $faker->randomElement($allowedTransportTypes);
            }
            
            $studentIds[] = DB::table('students')->insertGetId($studentData);
        }

// ========================================================
// 💰 11. الرسوم والعقود المالية للطلاب
// ========================================================
$this->command->info('💰 جاري إنشاء العقود المالية للطلاب...');

$feeProducts = [];
$feeTypes = [
    ['name' => 'رسوم دراسية كاملة', 'price' => 1200],
    ['name' => 'رسوم الصف الأول', 'price' => 1000],
    ['name' => 'رسوم الصف الثاني', 'price' => 1100],
    ['name' => 'رسوم الصف الثالث', 'price' => 1150],
    ['name' => 'رسوم الصف الرابع', 'price' => 1200],
    ['name' => 'رسوم الصف الخامس', 'price' => 1250],
    ['name' => 'رسوم الصف السادس', 'price' => 1300],
];

foreach ($feeTypes as $fee) {
    $feeProducts[] = DB::table('products')->insertGetId([
        'branch_id' => $branchId,
        'name_ar' => $fee['name'],
        'type' => 'service',
        'selling_price' => $fee['price'],
        'is_active' => 1,
        'created_at' => $now,
    ]);
}

// 🔥 فحص القيم الفعلية لـ financial_status من قاعدة البيانات
$allowedFinancialStatuses = ['paid', 'partially_paid', 'unpaid'];
try {
    $enumValues = DB::select("
        SELECT COLUMN_TYPE 
        FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = DATABASE() 
        AND TABLE_NAME = 'student_contracts' 
        AND COLUMN_NAME = 'financial_status'
    ");
    
    if (!empty($enumValues)) {
        preg_match_all("/'([^']+)'/", $enumValues[0]->COLUMN_TYPE, $matches);
        if (!empty($matches[1])) {
            $allowedFinancialStatuses = $matches[1];
        }
    }
} catch (\Exception $e) {
    // استخدام القيم الافتراضية
}

// 🔥 فحص القيم الفعلية لـ contract_status
$allowedContractStatuses = ['active', 'expired', 'terminated', 'pending'];
try {
    $enumValues = DB::select("
        SELECT COLUMN_TYPE 
        FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = DATABASE() 
        AND TABLE_NAME = 'student_contracts' 
        AND COLUMN_NAME = 'contract_status'
    ");
    
    if (!empty($enumValues)) {
        preg_match_all("/'([^']+)'/", $enumValues[0]->COLUMN_TYPE, $matches);
        if (!empty($matches[1])) {
            $allowedContractStatuses = $matches[1];
        }
    }
} catch (\Exception $e) {
    // استخدام القيم الافتراضية
}

// 🔥 فحص القيم الفعلية لـ status في student_installments
$allowedInstallmentStatuses = ['pending', 'paid', 'partial', 'overdue'];
try {
    $enumValues = DB::select("
        SELECT COLUMN_TYPE 
        FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = DATABASE() 
        AND TABLE_NAME = 'student_installments' 
        AND COLUMN_NAME = 'status'
    ");
    
    if (!empty($enumValues)) {
        preg_match_all("/'([^']+)'/", $enumValues[0]->COLUMN_TYPE, $matches);
        if (!empty($matches[1])) {
            $allowedInstallmentStatuses = $matches[1];
        }
    }
} catch (\Exception $e) {
    // استخدام القيم الافتراضية
}

$this->command->info("   📋 financial_status: " . json_encode($allowedFinancialStatuses));
$this->command->info("   📋 contract_status: " . json_encode($allowedContractStatuses));
$this->command->info("   📋 installment_status: " . json_encode($allowedInstallmentStatuses));

// 🔥 فحص أعمدة جدول installment_payments مسبقاً
$installmentPaymentColumns = [];
if (Schema::hasTable('installment_payments')) {
    $installmentPaymentColumns = Schema::getColumnListing('installment_payments');
}

// 🔥 دالة مساعدة لتعيين حالة صحيحة
$getValidStatus = function($desired, $allowedList) {
    if (in_array($desired, $allowedList)) {
        return $desired;
    }
    // محاولة إيجاد بديل قريب
    $alternatives = [
        'paid' => ['paid', 'completed', 'done'],
        'partially_paid' => ['partial', 'partially_paid', 'pending'],
        'unpaid' => ['unpaid', 'pending', 'due'],
        'active' => ['active', 'valid', 'current'],
        'pending' => ['pending', 'unpaid', 'due'],
        'partial' => ['partial', 'partially_paid', 'pending'],
    ];
    
    if (isset($alternatives[$desired])) {
        foreach ($alternatives[$desired] as $alt) {
            if (in_array($alt, $allowedList)) {
                return $alt;
            }
        }
    }
    
    // لو مفيش بديل، استخدم أول قيمة في القائمة
    return $allowedList[0] ?? $desired;
};

// عقود الطلاب
$contractIds = [];
foreach ($studentIds as $index => $studentId) {
    $student = DB::table('students')->find($studentId);
    $gradeIndex = array_search($student->grade_id, $gradeIds);
    $feeProduct = $feeProducts[min($gradeIndex + 1, count($feeProducts) - 1)];
    $feeAmount = DB::table('products')->find($feeProduct)->selling_price;
    
    $transportFee = 0;
    $routeId = null;
    $morningBus = null;
    $afternoonBus = null;
    
    if (isset($student->transport_type) && !in_array($student->transport_type, ['none', 'private'])) {
        $routeId = $faker->randomElement($routeIds);
        $route = DB::table('transport_routes')->find($routeId);
        $transportFee = $student->transport_type === 'bus_both' ? $route->monthly_fare_both * 10 : $route->monthly_fare_single * 10;
        $morningBus = $faker->randomElement($busIds);
        $afternoonBus = $student->transport_type === 'bus_both' ? $faker->randomElement($busIds) : null;
    }
    
    $subTotal = $feeAmount + $transportFee;
    $discount = $faker->boolean(30) ? $faker->numberBetween(5, 15) / 100 * $subTotal : 0;
    $grandTotal = $subTotal - $discount;
    
    // 🔥 اختيار حالة الدفع مع التحقق من القيم المسموحة
    $paymentStatusChoice = $faker->randomElement(['paid', 'partially_paid', 'partially_paid', 'unpaid']);
    $paymentStatus = $getValidStatus($paymentStatusChoice, $allowedFinancialStatuses);
    
    $paidAmount = 0;
    if ($paymentStatus === $getValidStatus('paid', $allowedFinancialStatuses)) {
        $paidAmount = $grandTotal;
    } elseif ($paymentStatus === $getValidStatus('partially_paid', $allowedFinancialStatuses)) {
        $paidAmount = $faker->numberBetween(20, 70) / 100 * $grandTotal;
    }
    
    $contractData = [
        'contract_number' => 'CON-2026-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
        'branch_id' => $student->branch_id,
        'student_id' => $studentId,
        'academic_year_id' => $academicYearId,
        'contract_date' => $pastMonth->format('Y-m-d'),
        'sub_total' => $subTotal,
        'grand_total' => $grandTotal,
        'paid_amount' => $paidAmount,
        'total_remaining' => $grandTotal - $paidAmount,
        'financial_status' => $paymentStatus,
        'contract_status' => $getValidStatus('active', $allowedContractStatuses),
        'created_at' => $now,
    ];
    
    if ($routeId) $contractData['transport_route_id'] = $routeId;
    if ($morningBus) $contractData['morning_bus_id'] = $morningBus;
    if ($afternoonBus) $contractData['afternoon_bus_id'] = $afternoonBus;
    if ($discount > 0) $contractData['discount'] = $discount;
    
    $contractIds[] = DB::table('student_contracts')->insertGetId($contractData);

    DB::table('student_contract_items')->insert([
        'student_contract_id' => end($contractIds),
        'product_id' => $feeProduct,
        'unit_price' => $feeAmount,
        'quantity' => 1,
        'row_total' => $feeAmount,
        'created_at' => $now,
    ]);

    if ($transportFee > 0) {
        $transportProduct = DB::table('products')->insertGetId([
            'branch_id' => $branchId,
            'name_ar' => 'رسوم النقل - ' . DB::table('transport_routes')->find($routeId)->name,
            'type' => 'service',
            'selling_price' => $transportFee,
            'is_active' => 1,
            'created_at' => $now,
        ]);
        
        DB::table('student_contract_items')->insert([
            'student_contract_id' => end($contractIds),
            'product_id' => $transportProduct,
            'unit_price' => $transportFee,
            'quantity' => 1,
            'row_total' => $transportFee,
            'created_at' => $now,
        ]);
    }

    // الأقساط (5 أقساط)
    $installmentAmount = $grandTotal / 5;
    for ($j = 0; $j < 5; $j++) {
        $dueDate = Carbon::parse($pastMonth->format('Y-m-d'))->addMonths($j);
        $installmentPaid = 0;
        $installmentStatusChoice = 'pending';
        
        if ($paymentStatus === $getValidStatus('paid', $allowedFinancialStatuses)) {
            $installmentPaid = $installmentAmount;
            $installmentStatusChoice = 'paid';
        } elseif ($paymentStatus === $getValidStatus('partially_paid', $allowedFinancialStatuses)) {
            $remainingPaid = $paidAmount - ($j * $installmentAmount);
            if ($remainingPaid >= $installmentAmount) {
                $installmentPaid = $installmentAmount;
                $installmentStatusChoice = 'paid';
            } elseif ($remainingPaid > 0) {
                $installmentPaid = $remainingPaid;
                $installmentStatusChoice = 'partial';
            }
        }
        
        // 🔥 استخدام الحالة الصحيحة
        $installmentStatus = $getValidStatus($installmentStatusChoice, $allowedInstallmentStatuses);
        
        $installmentId = DB::table('student_installments')->insertGetId([
            'student_contract_id' => end($contractIds),
            'student_id' => $studentId,
            'amount' => $installmentAmount,
            'paid_amount' => $installmentPaid,
            'remaining_amount' => $installmentAmount - $installmentPaid,
            'due_date' => $dueDate->format('Y-m-d'),
            'status' => $installmentStatus,
            'paid_date' => $installmentStatus === $getValidStatus('paid', $allowedInstallmentStatuses) ? $dueDate->format('Y-m-d') : null,
            'created_at' => $now,
        ]);

        if ($installmentPaid > 0) {
    $paymentId = DB::table('payments')->insertGetId([
        'financial_account_id' => $faker->randomElement([$cashAccountId, $bankAccountId]),
        'receipt_number' => 'REC-' . str_pad($index * 5 + $j + 1, 5, '0', STR_PAD_LEFT),
        'user_id' => $adminId,
        'payment_type' => 'installment',
        'payable_type' => 'App\\Models\\StudentInstallment',
        'payable_id' => $installmentId,
        'amount' => $installmentPaid,
        'payment_date' => $dueDate->format('Y-m-d'),
        'transaction_details' => 'سداد قسط #' . ($j + 1) . ' - عقد ' . end($contractIds),
        'created_at' => $now,
    ]);

    // 🔥 تسجيل الحركة في installment_payments بشكل ذكي جداً
    if (!empty($installmentPaymentColumns)) {
        // 🔍 البحث عن الأعمدة المحتملة للربط
        $linkData = ['created_at' => $now];
        
        // عمود المبلغ
        if (in_array('amount', $installmentPaymentColumns)) {
            $linkData['amount'] = $installmentPaid;
        } elseif (in_array('paid_amount', $installmentPaymentColumns)) {
            $linkData['paid_amount'] = $installmentPaid;
        }
        
        // عمود الربط بالقسط - نبحث عن كل الأسماء الممكنة
        $installmentColumn = null;
        $possibleInstallmentColumns = [
            'installment_id', 'student_installment_id', 'related_id', 
            'installment_payment_id', 'item_id'
        ];
        foreach ($possibleInstallmentColumns as $col) {
            if (in_array($col, $installmentPaymentColumns)) {
                $installmentColumn = $col;
                break;
            }
        }
        
        // عمود الربط بالدفع - نبحث عن كل الأسماء الممكنة
        $paymentColumn = null;
        $possiblePaymentColumns = [
            'payment_id', 'payment_method_id', 'transaction_id',
            'receipt_id', 'pay_id'
        ];
        foreach ($possiblePaymentColumns as $col) {
            if (in_array($col, $installmentPaymentColumns)) {
                $paymentColumn = $col;
                break;
            }
        }
        
        // لو لقينا الأعمدة المناسبة
        if ($installmentColumn && $paymentColumn) {
            $linkData[$installmentColumn] = $installmentId;
            $linkData[$paymentColumn] = $paymentId;
            
            try {
                DB::table('installment_payments')->insert($linkData);
            } catch (\Exception $e) {
                // لو فشل الإدراج، نتجاهل الخطأ ونكمل
                $this->command->warn("   ⚠️  تحذير: تعذر تسجيل الحركة في installment_payments - " . $e->getMessage());
            }
        } else {
            $this->command->warn("   ⚠️  تحذير: جدول installment_payments لا يحتوي على أعمدة الربط المناسبة");
            $this->command->warn("   📋 الأعمدة الموجودة: " . implode(', ', $installmentPaymentColumns));
        }
    }
}
    }
}
// ========================================================
// 🏛️ 13. الأصول الثابتة
// ========================================================
$this->command->info('🏛️ جاري تسجيل الأصول الثابتة...');

if (Schema::hasTable('asset_categories') && Schema::hasTable('assets')) {
    // 🔥 فحص أعمدة جدول assets
    $assetColumns = Schema::getColumnListing('assets');
    $this->command->info("   📋 أعمدة assets: " . implode(', ', $assetColumns));
    
    $assetCategories = ['أثاث', 'معدات', 'سيارات', 'أجهزة كمبيوتر', 'مباني'];
    $categoryIds = [];
    foreach ($assetCategories as $cat) {
        $categoryIds[] = DB::table('asset_categories')->insertGetId([
            'name_ar' => $cat,
            'created_at' => $now,
        ]);
    }

    $assetNames = [
        ['name' => 'مكيف سبليت', 'cat' => 1, 'cost' => 250, 'life' => 5],
        ['name' => 'جهاز كمبيوتر مكتبي', 'cat' => 3, 'cost' => 350, 'life' => 4],
        ['name' => 'طابعة ليزر', 'cat' => 3, 'cost' => 180, 'life' => 3],
        ['name' => 'جهاز عرض (بروجيكتور)', 'cat' => 2, 'cost' => 400, 'life' => 5],
        ['name' => 'سيارة نقل صغيرة', 'cat' => 2, 'cost' => 5000, 'life' => 7],
        ['name' => 'مجموعة كراسي وطاولات', 'cat' => 0, 'cost' => 500, 'life' => 10],
        ['name' => 'خزانة ملفات', 'cat' => 0, 'cost' => 120, 'life' => 8],
        ['name' => 'آلة تصوير', 'cat' => 3, 'cost' => 800, 'life' => 5],
    ];

    foreach ($assetNames as $asset) {
        $assetData = [
            'branch_id' => $branchId,
            'asset_category_id' => $categoryIds[$asset['cat']],
            'purchase_date' => $faker->dateTimeBetween('-3 years', '-1 month')->format('Y-m-d'),
            'purchase_cost' => $asset['cost'],
            'useful_life_years' => $asset['life'],
            'status' => $faker->randomElement(['active', 'active', 'active', 'maintenance']),
            'created_at' => $now,
            'updated_at' => $now,
        ];
        
        // 🔥 استخدام اسم العمود الصحيح للاسم
        if (in_array('name_ar', $assetColumns)) {
            $assetData['name_ar'] = $asset['name'];
        } elseif (in_array('name', $assetColumns)) {
            $assetData['name'] = $asset['name'];
        } elseif (in_array('asset_name', $assetColumns)) {
            $assetData['asset_name'] = $asset['name'];
        } elseif (in_array('title', $assetColumns)) {
            $assetData['title'] = $asset['name'];
        }
        
        //  استخدام اسم العمود الصحيح للكود
        if (in_array('asset_code', $assetColumns)) {
            $assetData['asset_code'] = 'AST-' . $faker->numerify('####');
        } elseif (in_array('code', $assetColumns)) {
            $assetData['code'] = 'AST-' . $faker->numerify('####');
        } elseif (in_array('serial_number', $assetColumns)) {
            $assetData['serial_number'] = 'AST-' . $faker->numerify('####');
        }
        
        //  الحقول الاختيارية
        if (in_array('depreciation_method', $assetColumns)) {
            $assetData['depreciation_method'] = 'straight_line';
        }
        
        try {
            DB::table('assets')->insert($assetData);
        } catch (\Exception $e) {
            $this->command->warn("   ⚠️ تم تخطي أصل: " . $asset['name'] . " - " . $e->getMessage());
        }
    }
}
        // ========================================================
        // 📊 14. الحضور والغياب للموظفين
        // ========================================================
        $this->command->info('📊 جاري تسجيل سجلات الحضور...');
        
        foreach ($employeeIds as $empId) {
            for ($day = 1; $day <= 20; $day++) {
                $date = Carbon::now()->startOfMonth()->addDays($day - 1);
                if ($date->isWeekend()) continue;
                
                $status = $faker->randomElement(['present', 'present', 'present', 'present', 'late', 'absent']);
                $checkIn = null;
                $checkOut = null;
                $delay = 0;
                
                if ($status !== 'absent') {
                    $checkIn = $faker->time('H:i');
                    $checkOut = $faker->time('H:i');
                    if ($status === 'late') {
                        $delay = $faker->numberBetween(5, 60);
                    }
                }
                
                DB::table('hr_attendances')->insert([
                    'employee_id' => $empId,
                    'date' => $date->format('Y-m-d'),
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                    'delay_minutes' => $delay,
                    'overtime_minutes' => $faker->boolean(20) ? $faker->numberBetween(30, 120) : 0,
                    'status' => $status,
                    'created_at' => $now,
                ]);
            }
        }

        // ========================================================
        // 💸 15. السلف والجزاءات
        // ========================================================
        $this->command->info('💸 جاري إنشاء السلف والجزاءات...');
        
        for ($i = 0; $i < 8; $i++) {
            $empId = $faker->randomElement($employeeIds);
            $amount = $faker->numberBetween(100, 500);
            $months = $faker->numberBetween(2, 6);
            
            DB::table('hr_loans')->insert([
                'employee_id' => $empId,
                'loan_amount' => $amount,
                'monthly_installment' => $amount / $months,
                'start_deduction_month' => $pastMonth->format('Y-m-d'),
                'paid_amount' => $faker->numberBetween(0, $amount / 2),
                'months_to_pay' => $months,
                'status' => $faker->randomElement(['active', 'active', 'completed']),
                'notes' => 'سلفة شخصية',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        for ($i = 0; $i < 10; $i++) {
            $empId = $faker->randomElement($employeeIds);
            $action = $faker->randomElement(['verbal_warning', 'written_warning', 'salary_deduction', 'suspension']);
            
            DB::table('hr_penalties')->insert([
                'employee_id' => $empId,
                'violation_type' => $faker->randomElement(['تأخير متكرر', 'غياب بدون إذن', 'إهمال في العمل', 'سلوك غير لائق']),
                'penalty_action' => $action,
                'deduction_days' => $action === 'salary_deduction' ? $faker->numberBetween(1, 5) : 0,
                'incident_date' => $faker->dateTimeBetween('-3 months', 'now')->format('Y-m-d'),
                'details' => $faker->sentence(10),
                'applied_to_payroll' => $faker->boolean(50),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // ========================================================
        // 📝 16. عقود الموظفين
        // ========================================================
        $this->command->info('📝 جاري إنشاء عقود الموظفين...');
        
        foreach ($employeeIds as $index => $empId) {
            $emp = DB::table('hr_employees')->find($empId);
            
            DB::table('hr_contracts')->insert([
                'employee_id' => $empId,
                'contract_number' => 'HR-CON-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                'start_date' => $emp->join_date,
                'end_date' => Carbon::parse($emp->join_date)->addYear()->format('Y-m-d'),
                'total_salary' => $emp->basic_salary,
                'status' => 'valid',
                'created_at' => $now,
            ]);
        }

        // ========================================================
        // 💰 17. مسير الرواتب
        // ========================================================
        $this->command->info('💰 جاري إنشاء مسيرات الرواتب...');
        
        foreach ([$pastMonth, Carbon::now()->startOfMonth()] as $payrollMonth) {
            foreach ($employeeIds as $empId) {
                $emp = DB::table('hr_employees')->find($empId);
                $basicSalary = $emp->basic_salary;
                $allowances = $faker->numberBetween(0, 100);
                $deductions = $faker->numberBetween(0, 50);
                $pasi = $emp->is_insured ? $basicSalary * 0.07 : 0;
                $loanDeduction = $faker->numberBetween(0, 50);
                $netSalary = $basicSalary + $allowances - $deductions - $pasi - $loanDeduction;
                
                $payrollId = DB::table('hr_payrolls')->insertGetId([
                    'employee_id' => $empId,
                    'month' => $payrollMonth->month,
                    'year' => $payrollMonth->year,
                    'working_days' => 30,
                    'absent_days' => $faker->numberBetween(0, 2),
                    'pasi_deduction' => $pasi,
                    'basic_salary' => $basicSalary,
                    'total_allowances' => $allowances,
                    'total_deductions' => $deductions,
                    'loan_deduction' => $loanDeduction,
                    'net_salary' => $netSalary,
                    'status' => $faker->randomElement(['paid', 'paid', 'paid', 'draft']),
                    'payment_date' => $payrollMonth->endOfMonth()->format('Y-m-d'),
                    'financial_account_id' => $faker->randomElement([$cashAccountId, $bankAccountId]),
                    'paid_by' => $adminId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                DB::table('hr_payroll_details')->insert([
                    ['hr_payroll_id' => $payrollId, 'item_name' => 'الراتب الأساسي', 'type' => 'allowance', 'amount' => $basicSalary, 'created_at' => $now],
                    ['hr_payroll_id' => $payrollId, 'item_name' => 'علاوات', 'type' => 'allowance', 'amount' => $allowances, 'created_at' => $now],
                    ['hr_payroll_id' => $payrollId, 'item_name' => 'خصومات', 'type' => 'deduction', 'amount' => $deductions, 'created_at' => $now],
                    ['hr_payroll_id' => $payrollId, 'item_name' => 'تأمينات اجتماعية', 'type' => 'deduction', 'amount' => $pasi, 'created_at' => $now],
                    ['hr_payroll_id' => $payrollId, 'item_name' => 'أقساط سلف', 'type' => 'deduction', 'amount' => $loanDeduction, 'created_at' => $now],
                ]);
            }
        }

        // ========================================================
        // 🏖️ 18. الإجازات
        // ========================================================
        $this->command->info('🏖️ جاري تسجيل الإجازات...');
        
        $leaveTypes = [];
        $leaveTypeNames = ['إجازة سنوية', 'إجازة مرضية', 'إجازة طارئة', 'إجازة بدون راتب'];
        foreach ($leaveTypeNames as $name) {
            $leaveTypes[] = DB::table('hr_leave_types')->insertGetId([
                'name_ar' => $name,
                'days_per_year' => $faker->randomElement([15, 20, 30]),
                'is_paid' => $name !== 'إجازة بدون راتب',
                'created_at' => $now,
            ]);
        }

        for ($i = 0; $i < 20; $i++) {
            $empId = $faker->randomElement($employeeIds);
            $startDate = $faker->dateTimeBetween('-3 months', '+1 month')->format('Y-m-d');
            $endDate = Carbon::parse($startDate)->addDays($faker->numberBetween(1, 10))->format('Y-m-d');
            
            DB::table('hr_leave_requests')->insert([
                'employee_id' => $empId,
                'leave_type_id' => $faker->randomElement($leaveTypes),
                'start_date' => $startDate,
                'end_date' => $endDate,
                'total_days' => Carbon::parse($startDate)->diffInDays($endDate) + 1,
                'reason' => $faker->sentence(5),
                'status' => $faker->randomElement(['pending', 'approved', 'approved', 'rejected']),
                'created_at' => $now,
            ]);
        }

        // ========================================================
        // 📊 19. قيود محاسبية متنوعة
        // ========================================================
        $this->command->info('📊 جاري إنشاء قيود محاسبية...');
        
        for ($i = 0; $i < 20; $i++) {
            $journalId = DB::table('journal_entries')->insertGetId([
                'branch_id' => $branchId,
                'entry_number' => 'JE-' . date('Y') . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'entry_date' => $faker->dateTimeBetween('-3 months', 'now')->format('Y-m-d'),
                'description' => $faker->sentence(5),
                'created_by' => $adminId,
                'created_at' => $now,
            ]);

            $amount = $faker->numberBetween(50, 1000);
            $accounts = $faker->randomElements([$cashAccountId, $bankAccountId, $revenueAccountId, $expenseAccountId, $salaryExpenseId], 2);
            
            DB::table('journal_items')->insert([
                ['journal_entry_id' => $journalId, 'financial_account_id' => $accounts[0], 'debit' => $amount, 'credit' => 0, 'line_description' => 'قيد مدين', 'created_at' => $now],
                ['journal_entry_id' => $journalId, 'financial_account_id' => $accounts[1], 'debit' => 0, 'credit' => $amount, 'line_description' => 'قيد دائن', 'created_at' => $now],
            ]);
        }

        // ========================================================
        // ✅ النهاية
        // ========================================================
        $this->command->info('');
        $this->command->info('✅ ========================================');
        $this->command->info('✅ تم زرع البيانات التجريبية بنجاح!');
        $this->command->info('✅ ========================================');
        $this->command->info('📊 ملخص البيانات:');
        $this->command->info('   👥 60 طالب مع أولياء أمورهم');
        $this->command->info('   👨‍🏫 20 معلم');
        $this->command->info('   👨‍💼 20 موظف (سواقين، مشرفين، إداريين)');
        $this->command->info('   🚌 12 باص على 8 خطوط سير');
        $this->command->info('   📝 60 عقد طالب');
        $this->command->info('   📦 15 فاتورة شراء');
        $this->command->info('   💰 مسيرات رواتب لشهرين');
        $this->command->info('   💸 سلف وجزاءات متنوعة');
        $this->command->info('   🏛️ أصول ثابتة');
        $this->command->info('   📊 20 قيد محاسبي');
        $this->command->info('✅ ========================================');
    }
}