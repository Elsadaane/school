<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // 1. تنظيف كاش الصلاحيات والجداول القديمة
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('role_has_permissions')->truncate();
        DB::table('model_has_roles')->truncate();
        DB::table('model_has_permissions')->truncate();
        Permission::query()->delete();
        Role::query()->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2. مصفوفة الصلاحيات المدمجة الشاملة (جميع ما سبق + التحديثات المطلوبة)
        $permissionsByGroup = [
            'الرئيسية والإعدادات' => [
                'view_dashboard'     => 'عرض لوحة التحكم الرئيسية',
                'manage_settings'    => 'تعديل الإعدادات العامة للمدرسة',
                'view_audit_logs'    => 'عرض سجل الرقابة والأمان (Audit)',
                'view_roles'         => 'عرض الأدوار والصلاحيات',
                'create_roles'       => 'إنشاء أدوار وصلاحيات جديدة',
                'edit_roles'         => 'تعديل الأدوار والصلاحيات',
                'delete_roles'       => 'حذف الأدوار والصلاحيات',
                'manage_roles'       => 'إضافة وتعديل وحذف الصلاحيات',
                'view_users'         => 'عرض مستخدمين النظام',
                'create_users'       => 'إضافة مستخدم جديد',
                'edit_users'         => 'تعديل بيانات المستخدمين',
                'delete_users'       => 'حذف وإيقاف المستخدمين',
                'manage_users'       => 'إدارة مستخدمين النظام كاملة',
                'view_branches'      => 'عرض فروع المدرسة',
                'manage_branches'    => 'إضافة وتعديل فروع المدرسة',
                'view_notifications' => 'عرض جرس الإشعارات والتنبيهات',
            ],
            'الأكاديمية والجدولة' => [
                'view_academics'         => 'عرض السنوات الدراسية والصفوف',
                'manage_academics'       => 'إدارة السنوات الدراسية والصفوف',
                'manage_academic_years'  => 'إدارة السنوات الدراسية',
                'manage_classrooms'      => 'إدارة الصفوف',
                'view_sections'          => 'عرض الشعب الدراسية',
                'create_sections'        => 'إنشاء شعب دراسية جديدة',
                'edit_sections'          => 'تعديل الشعب الدراسية',
                'delete_sections'        => 'حذف الشعب الدراسية',
                'view_classroom'         => 'عرض الفصول الدراسية',
                'manage_sections'        => 'إدارة الشعب وتسكين الطلاب',
                'view_subjects'          => 'عرض المواد الدراسية',
                'create_subjects'        => 'إضافة مواد دراسية جديدة',
                'edit_subjects'          => 'تعديل المواد الدراسية',
                'delete_subjects'        => 'حذف المواد الدراسية',
                'manage_subjects'        => 'إدارة المواد الدراسية',
                'view_schedules'         => 'عرض جداول الحصص',
                'manage_schedules'       => 'إنشاء وتعديل جداول الحصص',
                'manage_teachers'        => 'إدارة المعلمين وتسكينهم الأكاديمي',
                'view_virtual_classes'   => 'عرض الفصول الافتراضية',
                'manage_virtual_classes' => 'إدارة وإنشاء الفصول الافتراضية',
            ],
            'الإيرادات والمصروفات' => [
                'view_incomes'              => 'عرض الإيرادات العامة',
                'create_incomes'            => 'إضافة إيراد عام',
                'edit_incomes'              => 'تعديل الإيرادات العامة',
                'delete_incomes'            => 'حذف الإيرادات العامة',
                'manage_incomes'            => 'إدارة الإيرادات كاملة',
                'manage_income_categories'  => 'إدارة تصنيفات الإيرادات',
                'manage_revenue_categories' => 'إدارة فئات ومصادر الإيرادات',
                'view_expenses'             => 'عرض المصروفات',
                'manage_expenses'           => 'إضافة وتعديل المصروفات',
                'manage_expense_categories' => 'إدارة تصنيفات المصروفات',
            ],
            'شؤون الطلاب وأولياء الأمور' => [
                'view_guardians'            => 'عرض بيانات أولياء الأمور',
                'create_guardians'          => 'إضافة ولي أمر جديد',
                'show_guardians'            => 'تفاصيل ولي أمر',
                'edit_guardians'            => 'تعديل بيانات ولي الأمر',
                'delete_guardians'          => 'حذف بيانات ولي الأمر',
                'manage_guardians'          => 'إدارة أولياء الأمور',
                'view_students'             => 'عرض بيانات الطلاب',
                'create_students'           => 'تسجيل طالب جديد',
                'edit_students'             => 'تعديل بيانات الطلاب',
                'delete_students'           => 'حذف طالب (نقل للأرشيف)',
                'transfer_students'         => 'نقل الطلاب',
                'view_student_transfers'    => 'عرض تحويلات الطلاب',
                'create_student_transfers'  => 'إنشاء طلب تحويل لطالب',
                'edit_student_transfers'    => 'تعديل تحويلات الطلاب',
                'delete_student_transfers'  => 'حذف تحويلات الطلاب',
                'manage_students'           => 'إدارة الطلاب كاملة',
                'manage_placements'         => 'تسكين الطلاب في الشعب',
                'manage_student_placements' => 'إدارة وتعديل التسكين الأكاديمي للطلاب',
                'manage_student_transfers'  => 'إدارة تحويلات الطلاب',
                'manage_student_leaves'     => 'اعتماد طلبات استئذان الطلاب',
                'manage_leave_requests'     => 'إدارة استئذان الطلاب',
                'manage_leaves'             => 'إدارة الاستئذانات (عام)', 
                'manage_student_promotions' => 'إدارة الترحيل والنجاح والرسوب',
                'manage_promotions'         =>  'صلاحيه ترحيل الطلاب',
                'view_student_attendances'  => 'مراقبة حضور الطلاب',
                'manage_attendance'         => 'تسجيل وتعديل حضور الطلاب',
                'manage_attendances'        => 'سجل الحضور العام للطلاب',
                'manage_installments'        => 'إدارة الأقساط والمدفوعات',
                'manage_purchases'           => 'إدارة مرتجعات المشتريات',
            ],
            'الرسوم والعقود والتحصيل' => [
                'view_fee_items'            => 'عرض بنود الرسوم',
                'manage_fee_items'          => 'إدارة بنود الرسوم وتسعيرها',
                'create_fee_items'          => 'إضافة بند دراسي',
                'edit_fee_items'            => 'تعديل بند دراسي',
                'delete_fee_items'          => 'حذف بند دراسي',
                'view_student_contracts'    => 'عرض عقود الطلاب',
                'create_student_contracts'  => 'إبرام عقد طالب جديد',
                'edit_student_contracts'    => 'تعديل عقود الطلاب',
                'delete_student_contracts'  => 'إلغاء وحذف عقود الطلاب',
                'manage_student_contracts'  => 'إدارة عقود الطلاب كاملة',
                'view_student_financials'   => 'عرض ماليات ومديونيات الطلاب',
                'create_student_financials' => 'إنشاء ماليات للطلاب',
                'edit_student_financials'   => 'تعديل ماليات ورسوم الطلاب',
                'delete_student_financials' => 'حذف ماليات الطلاب',
                'collect_installments'      => 'سداد وتحصيل الأقساط',
                'view_installments'         => 'عرض الأقساط',
                'pay_installments'          => 'دفع الأقساط',
                'pay_student_installments'  => 'تسديد أقساط الطلاب المتأخرة',
            ],
            'الحسابات والمالية العامة' => [
                'view_financial_accounts'     => 'عرض الشجرة المحاسبية',
                'create_financial_accounts'   => 'إضافة حساب مالي',
                'edit_financial_accounts'     => 'تعديل حساب مالي',
                'manage_financial_accounts'   => 'إدارة الشجرة المحاسبية والبنوك',
                'transfer_financial_accounts' => 'تحويل أموال بين الحسابات',
                'manage_accounting'           => 'إدارة الشجرة المحاسبية والتسويات',
                'view_payments'               => 'عرض سندات القبض والصرف',
                'create_payments'             => 'إنشاء سندات قبض وصرف',
                'edit_payments'               => 'تعديل المدفوعات',
                'delete_payments'             => 'حذف سندات الصرف والقبض',
                'print_payments'              => 'طباعة السندات والمدفوعات',
                'manage_payments'             => 'إدارة سندات القبض والدفع',
                'view_journal_entries'        => 'عرض قيود اليومية',
                'create_journal_entries'      => 'تسجيل قيود يومية يدوية',
            ],
            'نقاط البيع والمقصف' => [
                'view_store_invoices'   => 'عرض فواتير المقصف والزي',
                'create_store_invoices' => 'إنشاء فاتورة بيع جديدة (POS)',
                'delete_store_invoices' => 'إلغاء فواتير المبيعات',
                'manage_store_invoices' => 'إدارة فواتير المقصف المدرسي',
                'manage_pos'            => 'إدارة نقاط البيع كاملة',
            ],
            'توزيع الدرجات والتقييمات' => [
                'manage_evaluations'   => 'إدارة التقييمات السلوكية والأكاديمية',
                'manage_marks'         => 'إدارة ورصد درجات الطلاب',
                'manage_grading_setup' => 'إعدادات رصد الدرجات والتقويم',
            ],
            'المخازن والمشتريات' => [
                'view_stock'                   => 'مراقبة النواقص والأرصدة المخزنية',
                'view_products'                => 'عرض المنتجات والأصناف',
                'create_products'              => 'إضافة صنف جديد',
                'edit_products'                => 'تعديل بيانات الأصناف',
                'delete_products'              => 'حذف المنتجات والأصناف',
                'manage_products'              => 'إدارة المنتجات والأصناف',
                'view_suppliers'               => 'عرض قائمة الموردين',
                'create_suppliers'             => 'إضافة مورد جديد',
                'edit_suppliers'               => 'تعديل بيانات الموردين',
                'delete_suppliers'             => 'حذف الموردين',
                'manage_suppliers'             => 'إدارة حسابات الموردين',
                'view_purchase_invoices'       => 'عرض فواتير المشتريات',
                'create_purchase_invoices'     => 'إنشاء فاتورة مشتريات جديدة',
                'edit_purchase_invoices'       => 'تعديل فواتير المشتريات',
                'delete_purchase_invoices'     => 'حذف وإلغاء فواتير المشتريات',
                'manage_purchase_invoices'     => 'إدارة فواتير المشتريات',
                'view_purchase_returns'        => 'عرض مرتجعات المشتريات',
                'create_purchase_returns'      => 'إنشاء مرتجع مشتريات',
                'manage_purchase_returns'      => 'إدارة مرتجعات المشتريات',
                'view_inventory_adjustments'   => 'عرض تسويات الجرد',
                'create_inventory_adjustments' => 'إنشاء تسوية جردية جديدة',
                'approve_inventory_adjustments'=> 'اعتماد تسويات الجرد',
                'manage_inventory_adjustments' => 'عمل تسويات جردية للمخزن',
                'manage_warehouse'             => 'إدارة الجرد والتحويل المباشر',
                'manage_warehouse_stocks'      => 'إدارة أرصدة الفروع',
                'view_stock_transfers'         => 'عرض التحويلات المخزنية',
                'create_stock_transfers'       => 'إنشاء طلب تحويل مخزني',
                'edit_stock_transfers'         => 'تعديل التحويلات المخزنية',
                'delete_stock_transfers'       => 'حذف التحويلات المخزنية',
                'manage_stock_transfers'       => 'نقل بضاعة بين الفروع',
                'transfer_products'            => 'تحويل منتجات الجرد السريع',
            ],
            'الأصول الثابتة' => [
                'view_assets'             => 'عرض الأصول الثابتة',
                'create_assets'           => 'إضافة أصل جديد',
                'edit_assets'             => 'إهلاك وتعديل الأصول',
                'manage_assets'           => 'إدارة الأصول الثابتة',
                'manage_asset_custodies'  => 'تسليم واستلام العُهد',
                'delete_assets'           => 'حذف الأصل',
                'manage_asset_categories' => 'إدارة تصنيفات الأصول',
            ],
            'النقل والحافلات' => [
                'view_buses'   => 'عرض الحافلات المدرسية',
                'create_buses' => 'إضافة حافلة جديدة',
                'manage_buses' => 'إدارة بيانات الحافلات والسائقين',
            ],
            'الموارد البشرية (HRMS)' => [
                'view_hr_settings'          => 'عرض إعدادات الموارد البشرية',
                'manage_hr_settings'        => 'إدارة الإدارات والورديات والدرجات',
                'view_employees'            => 'عرض بيانات الموظفين والمعلمين',
                'create_employees'          => 'تسجيل موظف جديد',
                'edit_employees'            => 'تعديل بيانات الموظفين',
                'manage_employees'          => 'إدارة بيانات الموظفين',
                'view_teachers'             => 'عرض قائمة المعلمين',
                'create_teachers'           => 'إضافة معلم جديد',
                'edit_teachers'             => 'تعديل بيانات المعلمين',
                'delete_teachers'           => 'حذف المعلمين',
                'create_contract'           => 'إنشاء عقد موظف/معلم',
                'edit_contract'             => 'تعديل عقود الموظفين',
                'manage_employee_documents' => 'إدارة مستندات ووثائق الموظفين',
                'create_appraisal'          => 'إنشاء تقييم',
                'delete_appraisal'          => 'حذف تقييم',
                'edit_appraisal'            => 'تعديل تقييم',
                'import_attendance'         => 'رفع ملف الحضور',
                'edit_attendance'           => 'اقفال اليوم ورصد الغياب ملف الحضور',
                'manage_shifts_attendance'  => 'إدارة حضور وانصراف الورديات',
                'create_candidate'          => 'انشاء مرشح',
                'update_candidate_status'   => 'تحديث حالة المرشح',
                'manage_hr_contracts'       => 'إدارة عقود الموظفين',
                'manage_hr_attendances'     => 'إدارة حضور وانصراف الموظفين',
                'manage_hr_attendance'      => 'سجل البصمة والغياب الإداري',
                'update_leave_status'       => 'تحديث حالات إجازات الموظفين',
                'manage_hr_leaves'          => 'اعتماد إجازات الموظفين',
                'manage_hr_loans_penalties' => 'إدارة السلف والجزاءات',
                'manage_payrolls'           => 'إدارة الرواتب',
                'manage_end_of_service'    => 'مخالصات نهاية الخدمة',
                'view_hr_contracts'            => 'عرض تفاصيل العقود',
                'view_payrolls'             => 'عرض مسير الرواتب',
                'generate_payrolls'         => 'إصدار واعتماد مسير الرواتب',
                'generate_payroll'          => 'إنشاء مسير الرواتب',
                'manage_shift_rosters'      => 'إدارة جداول الورديات', 
                'manage_payroll_financials' => 'إدارة استحقاقات الرواتب المالية',
                'pay_payroll'               => 'صرف الرواتب', 
                'post_payroll_entries'      => 'ترحيل قيود الرواتب للمالية', 
                'manage_custody'            => 'إدارة عهدة موظف محدد',
                'manage_custodies'          => 'إدارة العُهد للموظفين', 
                'manage_appraisals'         => 'إدارة تقييمات الأداء',
                'create_penalty'            => 'إنشاء جزاء أو خصم', 
                'edit_penalty'              => 'تعديل الجزاءات', 
                'delete_penalty'            => 'حذف الجزاءات', 
                'manage_eos'                => 'إدارة مكافآت نهاية الخدمة', 
                'manage_trainees'           => 'إدارة المتدربين وفترة التجربة'
            ],
            'التقارير التحليلية والذكية' => [
                'view_financial_reports'         => 'تقارير الأرباح والخسائر والضرائب',
                'view_student_financial_reports' => 'كشوفات حسابات الطلاب والمتأخرات',
                'view_academic_reports'          => 'تقارير الحضور الأكاديمي',
                'view_hr_reports'                => 'تقارير الموارد البشرية والرواتب',
                'view_radar_alerts'              => 'رادار التنبيهات الذكي',
                'view_ai_analysis'               => 'المحلل الذكي (AI Analysis)',
                'view_reports'                   => 'عرض كافة التقارير التحليلية', 
                'view_operational_reports'       => 'عرض التقارير التشغيلية'
            ],
            'بوابة المعلم (Teacher Gate)' => [
                'view_teacher_schedule'     => 'عرض جدول المعلم', 
                'take_attendance'           => 'تسجيل حضور الطلاب', 
                'input_marks'               => 'إدخال درجات التقييم', 
                'manage_virtual_classrooms' => 'إدارة الفصول الافتراضية للمعلم'
            ]
        ];

        DB::transaction(function () use ($permissionsByGroup) {
            // 1. زرع الصلاحيات وحقن الـ group_name والـ name_ar لكل صلاحية
            foreach ($permissionsByGroup as $groupName => $permissions) {
                foreach ($permissions as $slug => $arabicName) {
                    Permission::firstOrCreate([
                        'name'       => $slug,
                        'guard_name' => 'web'
                    ], [
                        'group_name' => $groupName,
                        'name_ar'    => $arabicName
                    ]);
                }
            }

            // 2. إنشاء الأدوار
            $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
            $adminRole      = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
            $studentAffairs = Role::firstOrCreate(['name' => 'Student_Affairs', 'guard_name' => 'web']);
            $cashierRole    = Role::firstOrCreate(['name' => 'Cashier', 'guard_name' => 'web']);
            $financeRole    = Role::firstOrCreate(['name' => 'Finance_Manager', 'guard_name' => 'web']);
            $storekeeper    = Role::firstOrCreate(['name' => 'Storekeeper', 'guard_name' => 'web']);
            $hrRole         = Role::firstOrCreate(['name' => 'HR Manager', 'guard_name' => 'web']);
            $accountantRole = Role::firstOrCreate(['name' => 'Accountant', 'guard_name' => 'web']);

            // 3. توزيع الصلاحيات
            $superAdminRole->syncPermissions(Permission::all());
            $adminRole->syncPermissions(Permission::whereNotIn('name', ['manage_roles', 'view_audit_logs'])->get());
            $studentAffairs->syncPermissions(Permission::whereIn('group_name', ['شؤون الطلاب وأولياء الأمور', 'الأكاديمية والجدولة', 'الفصول'])->get());
            $financeRole->syncPermissions(Permission::whereIn('group_name', ['الإيرادات والمصروفات', 'الرسوم والعقود والتحصيل', 'الحسابات والمالية العامة', 'بنود الرسوم الدراسية', 'الحسابات المالية', 'التقارير التحليلية والذكية'])->get());
            $cashierRole->syncPermissions(Permission::whereIn('name', ['view_dashboard', 'collect_installments', 'manage_payments', 'view_store_invoices', 'create_store_invoices', 'manage_pos', 'view_payments', 'create_payments'])->get());
            $accountantRole->syncPermissions(Permission::whereIn('group_name', ['الخزن والمحاسبة والأصول', 'الرسوم والعقود والتحصيل', 'الإيرادات والمصروفات'])->get());
            $storekeeper->syncPermissions(Permission::whereIn('group_name', ['المخازن والمشتريات', 'نقاط البيع والمقصف'])->get());
            $hrRole->syncPermissions(Permission::whereIn('group_name', ['الموارد البشرية (HRMS)'])->get());

            // 4. تأمين المدير العام
            $adminUser = User::find(1);
            if ($adminUser && !$adminUser->hasRole('Super Admin')) {
                $adminUser->assignRole($superAdminRole);
            }
        });

        $this->command->info('✅ تم زرع الصلاحيات الشاملة (القديمة والحديثة) بنجاح!');
    }
}