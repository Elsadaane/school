<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
     Schema::create('student_promotions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
    
    // من (السنة والصف والشعبة القديمة) 👈 تم إضافة الشعبة هنا
    $table->foreignId('from_academic_year_id')->constrained('academic_years');
    $table->foreignId('from_grade_id')->constrained('grades');
    $table->foreignId('from_section_id')->nullable()->constrained('sections')->nullOnDelete(); // 💡 العمود السحري لحفظ الشعبة القديمة
    
    // إلى (السنة والصف الجديد)
    $table->foreignId('to_academic_year_id')->constrained('academic_years');
    $table->foreignId('to_grade_id')->constrained('grades');
    
    // حالة النقل (ناجح وتم ترفيعه / راسب وباقي للإعادة)
    $table->enum('status', ['promoted', 'retained']);
    
    $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete(); // مين الموظف اللي نقلهم
    $table->timestamps();
});}

    public function down(): void
    {
        Schema::dropIfExists('student_promotions');
    }
};