<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            
            // 📸 الهوية البصرية
            $table->string('logo')->nullable();
            $table->string('seal')->nullable(); // الختم
            $table->string('header_image')->nullable();
            $table->string('contract_header')->nullable();
            $table->string('contract_footer')->nullable();
            
            // 🏢 بيانات الشركة
            $table->string('company_name_ar')->nullable();
            $table->string('company_name_en')->nullable();
            $table->string('cr_number')->nullable(); // السجل التجاري
            $table->string('tax_number')->nullable(); // الرقم الضريبي
            $table->decimal('tax_value', 5, 2)->default(0); // قيمة الضريبة
            
            // 📍 العناوين والتواصل
            $table->string('address_ar')->nullable();
            $table->string('address_en')->nullable();
            $table->string('governorate_ar')->nullable(); // المحافظة
            $table->string('governorate_en')->nullable();
            $table->string('wilayat_ar')->nullable(); // الولاية
            $table->string('wilayat_en')->nullable();
            $table->string('building_number')->nullable();
            $table->string('po_box')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('email')->nullable();
            $table->string('phone_code')->default('+968');
            $table->string('phone')->nullable();
            
            // 📄 الشروط والأحكام (بصيغة HTML للـ CKEditor)
            $table->longText('student_contract_terms_ar')->nullable();
            $table->longText('student_contract_terms_en')->nullable();
            
            $table->longText('general_terms_ar')->nullable();
            $table->longText('general_terms_en')->nullable();
            
            $table->longText('teacher_contract_terms_ar')->nullable();
            $table->longText('teacher_contract_terms_en')->nullable();
            
            $table->longText('driver_contract_terms_ar')->nullable();
            $table->longText('driver_contract_terms_en')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};