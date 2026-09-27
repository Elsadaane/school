<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    // 1. جدول خطوط السير (التسعير المركزي)
    Schema::create('transport_routes', function (Blueprint $table) {
        $table->id();
        $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
        $table->string('name')->comment('اسم خط السير (مثال: السيب - الخوض)');
        $table->decimal('monthly_fare_both', 10, 3)->default(0)->comment('سعر الذهاب والعودة');
        $table->decimal('monthly_fare_single', 10, 3)->default(0)->comment('سعر الاتجاه الواحد');
        $table->text('notes')->nullable();
        $table->softDeletes();
        $table->timestamps();
    });

    // 2. جدول الحافلات (محدث)
    Schema::create('buses', function (Blueprint $table) {
        $table->id();
        $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
        $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
        
        // ربط خط السير (جديد)
        $table->foreignId('transport_route_id')->nullable()->constrained('transport_routes')->nullOnDelete();
        
        $table->foreignId('driver_id')->nullable()->constrained('hr_employees')->nullOnDelete();
        
        // مشرفة الحافلة (جديد)
        $table->foreignId('supervisor_id')->nullable()->constrained('hr_employees')->nullOnDelete()->comment('مشرفة الحافلة');
        
        $table->string('bus_number');
        $table->enum('ownership_type', ['owned', 'rented'])->default('owned')->comment('مملوكة للمدرسة أم مستأجرة');
        $table->decimal('monthly_rent', 10, 3)->default(0);
        $table->string('bus_type')->nullable();
        $table->string('bus_model')->nullable();
        $table->integer('capacity')->nullable();
        
        // تواريخ هامة للرادار (جديد)
        $table->date('insurance_expiry_date')->nullable()->comment('تاريخ انتهاء التأمين');
        $table->date('registration_expiry_date')->nullable()->comment('تاريخ انتهاء الملكية/الفحص');
        
        $table->string('driver_name')->nullable();
        $table->string('driver_national_id')->nullable();
        $table->string('driver_phone')->nullable();
        $table->text('notes')->nullable();
        $table->longText('contract_terms_ar')->nullable();
        $table->longText('contract_terms_en')->nullable();
        $table->longText('signature_data')->nullable();
        $table->softDeletes();
        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('buses');
    Schema::dropIfExists('transport_routes');
}
};