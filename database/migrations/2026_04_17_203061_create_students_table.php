<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            
            // 🔥 التعديل الأول: ربط الطالب بجدول أولياء الأمور الجديد (guardians) 🔥
            $table->foreignId('parent_id')->constrained('guardians')->cascadeOnDelete();
            
            $table->foreignId('academic_year_id')->constrained('academic_years')->restrictOnDelete();
            $table->foreignId('grade_id')->constrained('grades')->restrictOnDelete();
            $table->foreignId('section_id')->nullable()->constrained('sections')->nullOnDelete(); // الشعبة ممكن تتحدد بعدين
            $table->enum('status', ['active', 'graduated', 'suspended', 'transferred', 'withdrawn'])->default('active');


            // بيانات الطالب
            $table->string('first_name');
            $table->string('second_name')->nullable();
            $table->string('third_name')->nullable();
            $table->string('fourth_name')->nullable();
            $table->string('tribe'); // القبيلة
            $table->date('dob'); // تاريخ الميلاد
            $table->enum('gender', ['male', 'female']);
            $table->string('nationality')->default('عماني');
            
            // 🔥 التعديل التاني: إضافة حقول الدخول الخاصة ببوابة الطالب 🔥
            $table->string('email')->unique()->nullable();
            $table->string('password')->nullable();

            // بيانات الأم
            $table->string('mother_name')->nullable();
            $table->string('mother_phone')->nullable();
            $table->string('mother_nationality')->nullable();
            $table->string('mother_workplace')->nullable();
            $table->string('mother_work_location')->nullable();
            $table->string('mother_national_id')->nullable();
            $table->date('mother_id_expiry')->nullable();

            // الطوارئ
            $table->string('emergency_name')->nullable();
            $table->string('emergency_relation')->nullable();
            $table->string('emergency_phone')->nullable();

            // العنوان
            $table->string('region')->nullable();
            $table->string('house_number')->nullable();
            $table->string('street_number')->nullable();
            $table->string('pickup_point')->nullable();

            // الحالة الصحية
            $table->boolean('has_allergies')->default(false);
            $table->boolean('has_hearing_issues')->default(false);
            $table->boolean('has_vision_issues')->default(false);
            $table->text('health_notes')->nullable();
            
            // النقل
            $table->enum('transport_type', ['private', 'bus_go', 'bus_return', 'bus_both'])->default('private');

            // المرفقات
            $table->string('student_photo')->nullable();
            $table->string('passport_copy')->nullable();
            $table->string('birth_certificate_copy')->nullable();
            $table->string('health_reports')->nullable();
            $table->string('vaccination_card')->nullable();

            // الموافقة على الشروط
            $table->boolean('agreed_to_terms')->default(false);
            $table->timestamp('agreed_at')->nullable(); // وقت الموافقة
            $table->text('agreed_terms_ar')->nullable();
            $table->text('agreed_terms_en')->nullable();
            $table->text('signature_data')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};