<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_archives', function (Blueprint $table) {
            $table->id();
            
            // الروابط الأساسية
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained('branches');
            $table->foreignId('grade_id')->constrained('grades');
            
            // 📸 لقطات الأرشيف (Snapshots) بتقنية JSON 📸
            $table->json('student_snapshot');     // بيانات الطالب وقتها (اسمه، عنوانه، ولي أمره)
            $table->json('financial_snapshot');   // العقود، الخصومات، المبالغ المدفوعة، الديون
            $table->json('academic_snapshot');    // الحضور والغياب، التقييمات، النتيجة النهائية
            
            $table->foreignId('archived_by')->constrained('users'); // الموظف اللي قفل السنة
            $table->timestamps();
            
            // منع أرشفة نفس الطالب مرتين في نفس السنة
            $table->unique(['student_id', 'academic_year_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_archives');
    }
};