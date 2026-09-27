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
    Schema::create('academic_year_branch', function (Blueprint $table) {
        $table->id();
        $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
        $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_year_branch');
    }
};
