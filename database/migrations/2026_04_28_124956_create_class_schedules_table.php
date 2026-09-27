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
        Schema::create('class_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->foreignId('grade_id')->constrained('grades')->cascadeOnDelete();
            $table->foreignId('section_id')->constrained('sections')->cascadeOnDelete(); // الشعبة
            
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete(); // المادة
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete(); // المعلم
            
            // يوم الأسبوع (في عُمان من الأحد للخميس)
            $table->enum('day_of_week', ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday']);
            
            // رقم الحصة (الأولى، الثانية، ... إلخ)
            $table->integer('period_number');
            
            // أوقات الحصة (اختياري بس بيخلي السلايدر عند المعلم شكله احترافي)
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            
            // 🔥 التعديل هنا: إضافة حقل يحدد هل الحصة دي في الفصل ولا أونلاين 🔥
            $table->boolean('is_virtual')->default(false); 
            
            $table->timestamps();
            
            // عشان نمنع تكرار نفس الحصة لنفس الشعبة في نفس اليوم
            $table->unique(['section_id', 'day_of_week', 'period_number'], 'unique_section_period');
            // عشان نمنع المعلم يكون عنده حصتين في نفس الوقت في مكانين مختلفين
            $table->unique(['teacher_id', 'day_of_week', 'period_number'], 'unique_teacher_period');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_schedules');
    }
};