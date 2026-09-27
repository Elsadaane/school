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
Schema::create('virtual_classes', function (Blueprint $table) {
    $table->id();
    $table->foreignId('section_id')->constrained('sections')->cascadeOnDelete();
    $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
    $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
    $table->string('meeting_id')->unique(); // كود الغرفة الفريد
    $table->string('topic'); // عنوان الحصة
    $table->dateTime('start_at'); // وقت البدء
    $table->integer('duration')->default(45); // مدة الحصة بالدقائق
    $table->string('join_url')->nullable(); // رابط الانضمام (لو استخدمت زووم مثلاً)
    $table->boolean('is_active')->default(true);
    $table->enum('status', ['scheduled', 'active', 'completed', 'cancelled'])
                  ->default('scheduled');
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('virtual_classes');
    }
};
