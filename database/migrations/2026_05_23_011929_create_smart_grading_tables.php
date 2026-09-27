<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. جدول الفصول الدراسية
        Schema::create('academic_terms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->string('name_ar'); // الفصل الدراسي الأول
            $table->string('name_en')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        // 2. جدول إعدادات أدوات التقويم (توزيع الدرجات)
        Schema::create('exam_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_term_id')->constrained('academic_terms')->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('grade_id')->constrained('grades')->cascadeOnDelete(); // الصف
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete(); // المادة
            
            $table->string('name'); // مثال: "الاختبار القصير"، "الأنشطة"، "الاختبار النهائي"
            $table->decimal('max_mark', 5, 2); // الدرجة القصوى (مثلاً من 15)
            $table->integer('sort_order')->default(1); // لترتيب الأعمدة في شاشة المعلم
            
            $table->timestamps();
        });

        // 3. جدول رصد درجات الطلاب الفعلي
        Schema::create('student_marks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('exam_category_id')->constrained('exam_categories')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('teachers'); // المعلم اللي رصد الدرجة
            
            $table->decimal('mark_obtained', 5, 2)->nullable(); // الدرجة الحاصل عليها
            $table->boolean('is_absent')->default(false); // لو غاب عن التقييم
            $table->text('teacher_notes')->nullable(); 
            
            $table->timestamps();
            
            // 🔥 منع تكرار رصد نفس التقييم لنفس الطالب مرتين 🔥
            $table->unique(['student_id', 'exam_category_id']);
        });

        // 4. جدول التقديرات الذكية
        Schema::create('grade_scales', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar'); // ممتاز، جيد جداً
            $table->string('letter'); // A, B, C
            $table->decimal('min_percent', 5, 2); // من 90%
            $table->decimal('max_percent', 5, 2); // إلى 100%
            $table->string('color_code')->default('#2ecc71'); // لون التقدير
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grade_scales');
        Schema::dropIfExists('student_marks');
        Schema::dropIfExists('exam_categories');
        Schema::dropIfExists('academic_terms');
    }
};