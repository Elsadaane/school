<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('student_contracts', function (Blueprint $table) {
            $table->id();
            $table->string('contract_number')->unique(); // رقم العقد المرجعي
            
            // الروابط الأكاديمية
            $table->foreignId('branch_id')->constrained('branches');
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years');
            $table->foreignId('grade_id')->nullable()->constrained('grades');
            $table->foreignId('section_id')->nullable()->constrained('sections');
            // تفاصيل النقل الدقيقة (بديلة لـ bus_id القديم)
            $table->foreignId('transport_route_id')->nullable()->constrained('transport_routes')->nullOnDelete()->comment('خط السير المختار للطالب');
            $table->foreignId('morning_bus_id')->nullable()->constrained('buses')->nullOnDelete()->comment('باص الذهاب (الصباحي)');
            $table->foreignId('afternoon_bus_id')->nullable()->constrained('buses')->nullOnDelete()->comment('باص العودة (المسائي)');            
            $table->date('contract_date');
            $table->string('attachment_file')->nullable();
            
            // 💰 الهيكلة المالية الدقيقة 💰
            $table->decimal('previous_debt', 10, 3)->default(0); // 🔥 المديونية المُرحلة من العام الماضي
            $table->decimal('sub_total', 10, 3)->default(0); // إجمالي رسوم العام الجديد
            $table->decimal('discount', 10, 3)->default(0); // الخصم
            $table->decimal('total_after_discount', 10, 3)->default(0); // الإجمالي بعد الخصم
            $table->decimal('tax_amount', 10, 3)->default(0); // الضريبة
            
            // الإجمالي الشامل = (إجمالي بعد الخصم + الضريبة + المديونية السابقة)
            $table->decimal('grand_total', 10, 3)->default(0); 
            
            // المدفوعات والمتبقي
            $table->decimal('paid_amount', 10, 3)->default(0); 
            $table->decimal('total_remaining', 10, 3)->default(0); 
            
            // حالات العقد
            $table->enum('financial_status', ['unpaid', 'partially_paid', 'fully_paid'])->default('unpaid');
            $table->enum('contract_status', ['draft', 'active', 'canceled'])->default('draft');
            
            $table->text('notes')->nullable(); // 🔥 حقل الملاحظات اللي كان ناقص
            
            // بيانات التوقيع الإلكتروني
            $table->longText('guardian_signature')->nullable(); 
            $table->timestamp('signed_at')->nullable();
            
            $table->timestamps();
        });
    }
    
    public function down(): void { Schema::dropIfExists('student_contracts'); }
};