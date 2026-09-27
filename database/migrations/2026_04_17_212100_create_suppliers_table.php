<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            
            $table->string('company_name'); // اسم الشركة أو التاجر
            $table->string('contact_person')->nullable(); // اسم الشخص المسؤول
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('tax_number')->nullable(); // الرقم الضريبي للمورد
            $table->text('address')->nullable();
            
            // الحسابات
            $table->decimal('opening_balance', 12, 3)->default(0); // الرصيد الافتتاحي
            $table->decimal('current_balance', 12, 3)->default(0); // الرصيد الحالي (مدين/دائن)
            
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};