<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guardians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();

            // 🔥 الحقول الأساسية ثنائية اللغة 🔥
            $table->string('name_ar')->comment('اسم ولي الأمر بالعربية');
            $table->string('name_en')->nullable()->comment('اسم ولي الأمر بالإنجليزية');
            $table->string('status')->default('active');

            
            // 🔥 بيانات الدخول والتواصل 🔥
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone');
            $table->string('national_id')->unique();
            $table->date('national_id_expiry')->nullable();
            
            // 🔥 بيانات إضافية ثنائية اللغة 🔥
            $table->string('nationality_ar')->nullable();
            $table->string('nationality_en')->nullable();
            
            $table->string('job_title_ar')->nullable();
            $table->string('job_title_en')->nullable();
            
            $table->string('work_place_ar')->nullable();
            $table->string('work_place_en')->nullable();
            
            $table->string('id_card_image')->nullable(); // مسار صورة البطاقة
            
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guardians');
    }
};