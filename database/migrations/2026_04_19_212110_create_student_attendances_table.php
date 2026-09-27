<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('student_attendances', function (Blueprint $table) {
            $table->id();
            
            // المفاتيح الأجنبية (الروابط)
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->foreignId('grade_id')->constrained('grades')->cascadeOnDelete();
            $table->foreignId('section_id')->constrained('sections')->cascadeOnDelete();
            
            $table->date('attendance_date');
            
            // 1. حالة الحضور الأساسية
            $table->enum('status', ['present', 'absent', 'late', 'excused'])->default('present');
            
            // 🔥 2. الإضافة الجديدة: طريقة الحضور (حضوري ولا أونلاين) 🔥
            $table->enum('attendance_mode', ['in_person', 'online'])->default('in_person');
            
            $table->text('notes')->nullable();
            
            // سبب الغياب أو التأخير
            $table->string('absence_reason')->nullable();
            
            // مين اللي سجل الحضور والغياب ده؟
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            // $table->enum('attendance_mode', ['in_person', 'online'])->default('in_person');
            
            $table->timestamps();
            
            // منع تسجيل نفس الطالب مرتين في نفس اليوم
            $table->unique(['student_id', 'attendance_date']);
        });
    }

    public function down(): void { Schema::dropIfExists('student_attendances'); }
};