<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // تنظيف الكاش الخاص بحزم الصلاحيات لضمان عدم حدوث تعارض
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 🌟 تفكيك الصلاحيات وتجميعها داخل المجموعات (group_name) بالعربي فقط 🌟
        $permissionsByGroup = [
            'إدارة النظام والأمان' => [
                'view_audit_logs',
                'manage_settings',
            ],
            'إدارة المستخدمين والصلاحيات' => [
                'view_users',
                'create_users',
                'edit_users',
                'delete_users',
                'manage_users',
                'view_roles',
                'manage_roles',
            ],
            'شؤون الطلاب والقبول' => [
                'view_students',
                'create_students',
                'edit_students',
                'delete_students',
                'transfer_students',
                'manage_placements', // تسكين الفصول والشعب
                'manage_guardians',  // أولياء الأمور
                'manage_leave_requests', // استئذان الطلاب
            ],
            'الأكاديمية والجدولة والدرجات' => [
                'manage_branches',
                'manage_academic_years',
                'manage_classrooms',
                'manage_sections',
                'manage_subjects',
                'manage_schedules',      // جداول الحصص
                'manage_grading_setup',  // إعدادات رصد الدرجات والتقويم
                'manage_attendances',    // سجل الحضور العام للطلاب
                'view_virtual_classes',    // 🔥 الفصول الافتراضية
                'manage_virtual_classes',  // 🔥 إدارة الفصول الافتراضية
            ],
            'العقود والمبيعات والتحصيل' => [
                'manage_student_contracts',
                'view_installments',
                'pay_installments',
                'manage_store_invoices', // نقطة بيع المقصف المدرسي
            ],
            'الخزن والمحاسبة والأصول' => [
                'manage_payments', // سندات القبض والصرف
                'manage_incomes',
                'manage_income_categories', // 🔥 تصنيفات الإيرادات
                'manage_expenses',
                'manage_expense_categories', // 🔥 تصنيفات المصروفات
                'manage_financial_accounts',
                'manage_fee_items',
                'manage_assets',
                'manage_asset_categories', // 🔥 تصنيفات الأصول
                'manage_accounting', // الشجرة المحاسبية
            ],
            'المخازن والمشتريات' => [
                'manage_products',
                'transfer_products', // 🔥 التحويل السريع بين المخازن
                'manage_warehouse_stocks',
                'manage_inventory_adjustments',
                'manage_stock_transfers',
                'manage_purchase_invoices',
                'manage_suppliers',
            ],
            'الموارد البشرية والرواتب (HRMS)' => [
                'manage_hr_settings',
                'manage_employees',
                'manage_teachers',
                'manage_buses', // الحافلات والسائقين والمشرفين
                'manage_shift_rosters',
                'manage_hr_attendance', // سجل البصمة والغياب الإداري
                'manage_payroll_financials', // العلاوات والخصومات والسلف والقروض
                'generate_payroll',
                'pay_payroll',
                'post_payroll_entries', // ترحيل القيود للمالية
                'manage_custodies',     // العهد المادية للموظفين والمعلمين
                'manage_appraisals',    // تقييم الأداء
                'create_penalty',
                'edit_penalty',
                'delete_penalty',
                'manage_eos',           // تصفية نهاية الخدمة
                'manage_trainees',      // المتدربين وفترة التجربة
            ],
            'التقارير والتحليلات الذكية' => [
                'view_financial_reports',
                'view_operational_reports',
                'view_ai_analysis', // رادار الذكاء الاصطناعي لتحليل الطلاب والأقساط
            ],
            'بوابة المعلم (Teacher Gate)' => [
                'view_teacher_schedule',
                'take_attendance',
                'input_marks',
                'manage_virtual_classrooms',
            ]
        ];

        DB::transaction(function () use ($permissionsByGroup) {
            
            // 1. زرع الصلاحيات وحقن الـ group_name لكل صلاحية
            foreach ($permissionsByGroup as $groupName => $permissions) {
                foreach ($permissions as $permissionName) {
                    Permission::firstOrCreate([
                        'name'       => $permissionName,
                        'guard_name' => 'web'
                    ], [
                        'group_name' => $groupName
                    ]);
                }
            }

            // 2. إنشاء الأدوار الإدارية الأساسية للهيكل التعليمي (Roles)
            $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
            $adminRole      = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
            $hrRole         = Role::firstOrCreate(['name' => 'HR Manager', 'guard_name' => 'web']);
            $accountantRole = Role::firstOrCreate(['name' => 'Accountant', 'guard_name' => 'web']);
            $storekeeperRole = Role::firstOrCreate(['name' => 'Storekeeper', 'guard_name' => 'web']); // 🔥 أضفت دور أمين المخزن

            // 3. توزيع الصلاحيات على الأدوار بشكل ذكي وأوتوماتيكي:
            
            // - السوبر أدمن يأخذ كافة الصلاحيات بدون استثناء
            $allPermissionIds = Permission::pluck('id')->toArray();
            $superAdminRole->syncPermissions($allPermissionIds);

            // - الأدمن العام يأخذ كل شيء ما عدا الأمور الحساسة جداً
            $adminPermissions = Permission::whereNotIn('name', ['manage_roles', 'view_audit_logs'])->pluck('id')->toArray();
            $adminRole->syncPermissions($adminPermissions);

            // - مدير الموارد البشرية (HR)
            $hrPermissions = Permission::where('group_name', 'الموارد البشرية والرواتب (HRMS)')->pluck('id')->toArray();
            $hrRole->syncPermissions($hrPermissions);

            // - المحاسب المالي (سيأخذ تلقائياً التصنيفات الجديدة بناءً على اسم الجروب)
            $accountantPermissions = Permission::whereIn('group_name', ['الخزن والمحاسبة والأصول', 'العقود والمبيعات والتحصيل'])
                ->orWhereIn('name', ['generate_payroll', 'pay_payroll', 'post_payroll_entries'])
                ->pluck('id')->toArray();
            $accountantRole->syncPermissions($accountantPermissions);

            // - أمين المخزن والمشتريات
            $storekeeperPermissions = Permission::where('group_name', 'المخازن والمشتريات')->pluck('id')->toArray();
            $storekeeperRole->syncPermissions($storekeeperPermissions);

            // 4. إنشاء يوزر (المدير العام للمدرسة) وتعيينه كـ Super Admin تلقائياً لو مش موجود
            $superUser = User::firstOrCreate(
                ['email' => 'admin@school.com'],
                [
                    'name'     => 'المدير العام',
                    'password' => Hash::make('password123'), // يرجى التغيير فور تسجيل الدخول للإنتاجية
                    'status'   => 'active'
                ]
            );

            // ربط اليوزر بدور السوبر أدمن
            if (!$superUser->hasRole('Super Admin')) {
                $superUser->assignRole($superAdminRole);
            }
        });

        $this->command->info('✅ تم استرجاع مصفوفة الصلاحيات (بالعربية فقط) مع الإضافات الجديدة بنجاح!');
    }
}