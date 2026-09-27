<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('student_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years');
            
            // التقييم العام (ممتاز، جيد جدا، ضعيف، الخ)
            $table->string('overall_level')->nullable(); 
            
            // نقاط الضعف والمواد اللي محتاج فيها تقوية
            $table->text('weak_subjects')->nullable(); 
            $table->text('teacher_notes')->nullable(); // ملاحظات المعلم
            
            // درجات أو نسب مئوية سابقة
            $table->decimal('previous_year_score', 5, 2)->nullable(); 
            
            // مين المعلم اللي عمل التقييم ده
            $table->foreignId('evaluated_by')->nullable()->constrained('teachers')->nullOnDelete();
            
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('student_evaluations'); }
};