<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // =========================================================
        // 🌟 المرحلة الأولى: الإعدادات والجداول المرجعية (Lookups) 🌟
        // =========================================================

        Schema::create('hr_departments', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar');
            $table->string('name_en')->nullable();
            $table->unsignedBigInteger('cost_center_id')->nullable();
            $table->timestamps();
        });

        Schema::create('hr_shifts', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar'); 
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('grace_period_minutes')->default(15);
            $table->boolean('is_active')->default(true); // 🔥 تم إضافتها لحل مشكلة الكنترولر 🔥
            $table->timestamps();
        });

        Schema::create('hr_job_grades', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar'); 
            $table->decimal('min_basic_salary', 10, 3)->default(0);
            $table->decimal('max_basic_salary', 10, 3)->default(0);
            $table->timestamps();
        });

        Schema::create('hr_financial_types', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar'); 
            $table->enum('type', ['allowance', 'deduction']); 
            $table->enum('value_type', ['fixed', 'percentage'])->default('fixed'); 
            $table->timestamps();
        });

        Schema::create('hr_payroll_gl_mappings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hr_financial_type_id')->constrained('hr_financial_types')->cascadeOnDelete();
            $table->foreignId('debit_account_id')->constrained('financial_accounts');
            $table->foreignId('credit_account_id')->constrained('financial_accounts');
            $table->timestamps();
        });

        Schema::create('hr_holidays', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar'); 
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_paid')->default(true); 
            $table->timestamps();
        });

        Schema::create('hr_leave_types', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar'); 
            $table->integer('days_per_year')->default(30); 
            $table->boolean('is_paid')->default(true);
            $table->timestamps();
        });

        // =========================================================
        // 🌟 المرحلة الثانية: بيانات الموظف الأساسية (The Core) 🌟
        // =========================================================

        Schema::create('hr_employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('hr_department_id')->nullable()->constrained('hr_departments')->nullOnDelete();
            $table->foreignId('hr_shift_id')->nullable()->constrained('hr_shifts')->nullOnDelete();
            $table->foreignId('hr_job_grade_id')->nullable()->constrained('hr_job_grades')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); 
            $table->string('driving_license_number')->nullable()->comment('رقم رخصة القيادة');
            $table->string('driving_license_type')->nullable()->comment('نوع الرخصة: خفيف / ثقيل');
            $table->date('driving_license_expiry')->nullable()->comment('تاريخ انتهاء الرخصة');
            
            $table->foreignId('manager_id')->nullable()->constrained('hr_employees')->nullOnDelete();

            $table->string('employee_code')->unique(); 
            $table->string('full_name_ar');
            $table->string('full_name_en')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('phone')->unique();
            $table->string('national_id')->unique()->nullable(); 
            
            $table->string('nationality')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->date('birth_date')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('emergency_contact')->nullable();
            $table->string('pasi_number')->nullable(); 
            $table->boolean('is_insured')->default(false);

            $table->string('job_title'); 
            $table->date('join_date'); 
            $table->decimal('basic_salary', 10, 3)->default(0); 
            
            $table->string('bank_name')->nullable();
            $table->string('iban_number')->nullable();
            
            $table->enum('status', ['active', 'on_leave', 'suspended', 'terminated'])->default('active');
            $table->string('profile_image')->nullable();
            
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('hr_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('hr_employees')->cascadeOnDelete();
            $table->string('contract_number')->unique();
            $table->date('start_date');
            $table->date('end_date')->nullable(); 
            $table->decimal('total_salary', 10, 3); 
            $table->string('attachment')->nullable(); 
            $table->enum('status', ['valid', 'expired', 'terminated'])->default('valid');
            $table->timestamps();
        });

        Schema::create('hr_contract_allowances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hr_contract_id')->constrained('hr_contracts')->cascadeOnDelete();
            $table->foreignId('financial_type_id')->constrained('hr_financial_types')->cascadeOnDelete(); 
            $table->decimal('amount', 10, 3); 
            $table->timestamps();
        });

        Schema::create('hr_employee_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('hr_employees')->cascadeOnDelete();
            $table->string('document_type'); 
            $table->string('document_number');
            $table->date('expiry_date'); 
            $table->string('attachment')->nullable();
            $table->timestamps();
        });

        // =========================================================
        // 🌟 المرحلة الثالثة: الحضور والورديات 🌟
        // =========================================================

        Schema::create('hr_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('hr_employees')->cascadeOnDelete();
            $table->date('date');
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->integer('delay_minutes')->default(0); 
            $table->integer('overtime_minutes')->default(0); 
            $table->enum('status', ['present', 'absent', 'late', 'on_leave'])->default('present');
            $table->timestamps();
        });

        Schema::create('hr_biometric_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('hr_employees')->cascadeOnDelete();
            $table->dateTime('punch_time'); 
            $table->enum('punch_type', ['in', 'out'])->nullable(); 
            $table->string('device_ip')->nullable(); 
            $table->boolean('is_processed')->default(false); 
            $table->timestamps();
        });

        Schema::create('hr_shift_rosters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('hr_employees')->cascadeOnDelete();
            $table->foreignId('shift_id')->constrained('hr_shifts')->cascadeOnDelete();
            $table->date('start_date'); 
            $table->date('end_date');
            $table->timestamps();
        });

        // =========================================================
        // 🌟 المرحلة الرابعة: الرواتب والماليات (Payroll) 🌟
        // =========================================================
        // ⚠️ تم ترتيبهم منطقياً لضمان سلامة الـ Foreign Keys

        Schema::create('hr_payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('hr_employees')->cascadeOnDelete();
            $table->integer('month');
            $table->integer('year');
            
            $table->decimal('working_days', 5, 2)->default(30); 
            $table->decimal('absent_days', 5, 2)->default(0); 
            $table->decimal('pasi_deduction', 10, 3)->default(0); 

            $table->decimal('basic_salary', 10, 3);
            $table->decimal('total_allowances', 10, 3)->default(0); 
            $table->decimal('total_deductions', 10, 3)->default(0); 
            $table->decimal('loan_deduction', 10, 3)->default(0); 
            
            $table->decimal('net_salary', 10, 3); 
            $table->foreignId('paid_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->unsignedBigInteger('transaction_id')->nullable(); 
            $table->foreignId('financial_account_id')->nullable()->constrained('financial_accounts')->nullOnDelete();
            $table->string('signature_path')->nullable();
            
            $table->enum('status', ['draft', 'approved', 'paid'])->default('draft');
            $table->date('payment_date')->nullable();
            $table->timestamps();
        });

        Schema::create('hr_payroll_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hr_payroll_id')->constrained('hr_payrolls')->cascadeOnDelete();
            $table->foreignId('financial_type_id')->nullable()->constrained('hr_financial_types')->nullOnDelete(); 
            $table->string('item_name'); 
            $table->enum('type', ['allowance', 'deduction']); 
            $table->decimal('amount', 10, 3);
            $table->timestamps();
            
            $table->index(['hr_payroll_id', 'type']);

        });

        // 🔥 تم دمج التعديلات الجديدة هنا مباشرة 🔥
        Schema::create('hr_employee_financials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('hr_employees')->cascadeOnDelete();
            $table->foreignId('financial_type_id')->constrained('hr_financial_types')->cascadeOnDelete();
            $table->decimal('amount', 10, 3);
            $table->date('action_date'); 
            $table->text('notes')->nullable();
            
            $table->enum('status', ['pending', 'processed'])->default('pending')->comment('حالة الحركة: معلقة أو مُعالجة');
            $table->foreignId('payroll_id')->nullable()->constrained('hr_payrolls')->nullOnDelete();
            
            $table->timestamps();

            // الفهارس
            $table->index(['status', 'action_date']);
            $table->index('payroll_id');
        });

        // 🔥 تم دمج التعديلات الجديدة هنا مباشرة 🔥
        Schema::create('hr_loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('hr_employees')->cascadeOnDelete();
            $table->decimal('loan_amount', 10, 3);
            $table->decimal('monthly_installment', 10, 3);
            $table->date('start_deduction_month'); 
            $table->decimal('paid_amount', 10, 3)->default(0);
            $table->integer('months_to_pay')->default(1);
            $table->text('notes')->nullable()->comment('ملاحظات على السلفة');
            $table->date('completed_at')->nullable()->comment('تاريخ سداد السلفة بالكامل');
            
            $table->foreignId('financial_record_id')->nullable()->constrained('hr_employee_financials')->nullOnDelete();
            
            $table->enum('status', ['pending', 'active', 'completed', 'canceled'])->default('active');
            $table->timestamps();

            // الفهارس
            $table->index(['employee_id', 'status']);
            $table->index('start_deduction_month');
        });

        // 🔥 تم دمج التعديلات الجديدة هنا مباشرة 🔥
        Schema::create('hr_penalties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('hr_employees')->cascadeOnDelete();
            $table->string('violation_type'); 
            $table->enum('penalty_action', ['verbal_warning', 'written_warning', 'salary_deduction', 'suspension']);
            $table->decimal('deduction_days', 5, 2)->default(0); 
            $table->date('incident_date'); 
            $table->text('details')->nullable();
            $table->boolean('applied_to_payroll')->default(false); 
            
            $table->text('notes')->nullable()->comment('ملاحظات إضافية على المخالفة');
            $table->foreignId('financial_record_id')->nullable()->constrained('hr_employee_financials')->nullOnDelete();
            
            $table->timestamps();

            // الفهارس
            $table->index(['employee_id', 'incident_date']);
            $table->index('applied_to_payroll');
        });

        // =========================================================
        // 🌟 المرحلة الخامسة: الإجازات، التقييم، التوظيف والعهد 🌟
        // =========================================================

        Schema::create('hr_leave_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('hr_employees')->cascadeOnDelete();
            $table->foreignId('leave_type_id')->constrained('hr_leave_types')->cascadeOnDelete();
            $table->integer('year');
            $table->decimal('total_days', 5, 2); 
            $table->decimal('used_days', 5, 2)->default(0); 
            $table->decimal('remaining_days', 5, 2); 
            $table->timestamps();
        });

        Schema::create('hr_leave_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('hr_employees')->cascadeOnDelete();
            $table->foreignId('leave_type_id')->constrained('hr_leave_types')->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('total_days', 5, 2);
            $table->text('reason')->nullable();
            $table->string('attachment')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });

        Schema::create('hr_compensatory_leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('hr_employees')->cascadeOnDelete();
            $table->foreignId('holiday_id')->nullable()->constrained('hr_holidays')->nullOnDelete(); 
            $table->date('worked_date'); 
            $table->decimal('earned_days', 5, 2)->default(1); 
            $table->date('valid_until'); 
            $table->boolean('is_used')->default(false);
            $table->timestamps();
        });

        Schema::create('hr_appraisals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('hr_employees')->cascadeOnDelete();
            $table->foreignId('evaluator_id')->constrained('users'); 
            $table->string('evaluation_period'); 
            $table->date('evaluation_date');
            $table->integer('score_out_of_100'); 
            $table->text('feedback')->nullable(); 
            $table->timestamps();
        });

                // 🔥 جدول نهاية الخدمة المحسّن 🔥
        Schema::create('hr_end_of_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('hr_employees')->cascadeOnDelete();
            $table->date('termination_date'); 
            
            // 🆕 سبب الإنهاء كنص حر (أكثر مرونة من enum)
            $table->string('reason')->comment('سبب إنهاء الخدمة');
            
            // 🆕 حقول حسابية إضافية
            $table->decimal('service_years', 5, 2)->default(0)->comment('سنوات الخدمة');
            $table->decimal('daily_rate', 10, 3)->default(0)->comment('أجرة اليوم');
            $table->decimal('remaining_leaves', 5, 2)->default(0)->comment('رصيد الإجازات المتبقي');
            
            // المبالغ المالية
            $table->decimal('gratuity_amount', 10, 3)->default(0); 
            $table->decimal('leave_encashment', 10, 3)->default(0); 
            $table->decimal('loan_deductions', 10, 3)->default(0); 
            $table->decimal('net_payout', 10, 3)->default(0); 

            // الربط بالحسابات
            $table->unsignedBigInteger('transaction_id')->nullable(); 
            $table->foreignId('financial_account_id')->nullable()->constrained('financial_accounts')->nullOnDelete();
            
            // التوقيع والملاحظات
            $table->text('notes')->nullable();
            $table->string('signature_path')->nullable();
            
            $table->timestamps();
            
            $table->index(['employee_id', 'termination_date']);
        });


        Schema::create('hr_job_candidates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->nullable()->constrained('hr_departments')->nullOnDelete();
            $table->string('full_name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('applied_for_position'); 
            $table->string('cv_attachment')->nullable(); 
            $table->enum('status', ['new', 'interviewed', 'shortlisted', 'rejected', 'hired'])->default('new');
            $table->text('interviewer_notes')->nullable();
            $table->integer('interview_rating')->nullable(); 
            $table->timestamps();
        });

        Schema::create('hr_trainees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hr_department_id')->nullable()->constrained('hr_departments')->nullOnDelete();
            $table->string('full_name');
            $table->string('phone');
            $table->string('university_or_school')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');
            $table->timestamps();
        });

        Schema::create('hr_custodies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('hr_employees')->cascadeOnDelete();
            $table->string('item_name'); 
            $table->string('item_code')->nullable(); 
            $table->date('receive_date'); 
            $table->date('return_date')->nullable(); 
            $table->enum('status', ['in_use', 'returned', 'lost', 'damaged'])->default('in_use');
            $table->text('notes')->nullable();
            $table->string('signature_path')->nullable();       // توقيع الموظف المستلم
            $table->string('admin_signature_path')->nullable(); // توقيع المسؤول (المُسلم)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        // تم تحديث ترتيب الإسقاط ليتناسب مع ترتيب الإنشاء لتجنب أخطاء الـ Foreign Keys
        Schema::dropIfExists('hr_custodies');
        Schema::dropIfExists('hr_trainees');
        Schema::dropIfExists('hr_job_candidates');
        Schema::dropIfExists('hr_end_of_services');
        Schema::dropIfExists('hr_appraisals');
        Schema::dropIfExists('hr_compensatory_leaves');
        Schema::dropIfExists('hr_leave_requests');
        Schema::dropIfExists('hr_leave_balances');
        
        Schema::dropIfExists('hr_penalties');
        Schema::dropIfExists('hr_loans');
        Schema::dropIfExists('hr_employee_financials');
        Schema::dropIfExists('hr_payroll_details');
        Schema::dropIfExists('hr_payrolls');
        
        Schema::dropIfExists('hr_shift_rosters');
        Schema::dropIfExists('hr_biometric_logs');
        Schema::dropIfExists('hr_attendances');
        Schema::dropIfExists('hr_employee_documents');
        Schema::dropIfExists('hr_contract_allowances');
        Schema::dropIfExists('hr_contracts');
        Schema::dropIfExists('hr_employees');
        
        Schema::dropIfExists('hr_leave_types');
        Schema::dropIfExists('hr_holidays');
        Schema::dropIfExists('hr_payroll_gl_mappings');
        Schema::dropIfExists('hr_financial_types');
        Schema::dropIfExists('hr_job_grades');
        Schema::dropIfExists('hr_shifts');
        Schema::dropIfExists('hr_departments');
    }
};