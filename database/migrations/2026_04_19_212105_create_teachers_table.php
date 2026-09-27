<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete(); // ربط بالفرع
            
            $table->string('name_ar');
            $table->string('name_en')->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone')->nullable();
            $table->string('specialization')->nullable(); // التخصص
            $table->string('national_id')->nullable();    // رقم الهوية
            $table->string('nationality')->nullable();
            $table->text('address')->nullable();
            $table->foreignId('hr_employee_id')->nullable()->constrained('hr_employees')->nullOnDelete();
            
            // 🔥 الحقول الجديدة الخاصة بالعقود والتوقيع الإلكتروني 🔥
            $table->longText('contract_terms_ar')->nullable(); // شروط العقد عربي (HTML)
            $table->longText('contract_terms_en')->nullable(); // شروط العقد إنجليزي (HTML)
            $table->longText('signature_data')->nullable(); // التوقيع كصورة Base64
            
            // $table->boolean('is_active')->default(true);
            $table->string('status')->default('active');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};