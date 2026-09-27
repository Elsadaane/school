<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->foreignId('grade_id')->constrained('grades')->cascadeOnDelete();
            
            $table->string('name_ar'); // مثال: شعبة 1، شعبة أ
            $table->string('name_en')->nullable();
            $table->integer('max_capacity')->default(25); // أقصى عدد للطلاب
            
            // يمكن ربط المعلم المربي (Home Room Teacher) لاحقاً هنا
            // $table->foreignId('teacher_id')->nullable()->constrained('hr_employees'); 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sections');
    }
};
