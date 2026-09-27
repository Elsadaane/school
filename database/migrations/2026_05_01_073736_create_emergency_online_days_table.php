<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emergency_online_days', function (Blueprint $table) {
            $table->id();
            $table->date('target_date'); // اليوم المستهدف للتحويل أونلاين
            
            // لو null يبقى كل الفروع، لو متحدد يبقى فرع معين
            $table->foreignId('branch_id')->nullable()->constrained('branches')->cascadeOnDelete();
            
            $table->string('reason')->nullable(); // سبب الطوارئ (أمطار، صيانة، الخ)
            
            // الموظف أو الإداري اللي أخد قرار التحويل
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete(); 
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emergency_online_days');
    }
};