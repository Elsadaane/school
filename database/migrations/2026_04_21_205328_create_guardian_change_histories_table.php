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
    Schema::create('guardian_change_histories', function (Blueprint $table) {
        $table->id();
        $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
        $table->foreignId('old_guardian_id')->constrained('guardians')->cascadeOnDelete();
        $table->foreignId('new_guardian_id')->constrained('guardians')->cascadeOnDelete();
        $table->text('change_reason'); // سبب التغيير
        $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete(); // مين الموظف اللي عمل التغيير
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guardian_change_histories');
    }
};
