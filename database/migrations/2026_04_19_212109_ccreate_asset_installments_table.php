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
        Schema::create('asset_installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('assets')->cascadeOnDelete();
            $table->decimal('amount', 15, 3); // مبلغ القسط
            $table->date('due_date'); // تاريخ الاستحقاق
            $table->boolean('is_paid')->default(0); // مدفوع ولا لأ؟
            $table->date('paid_date')->nullable(); // تاريخ الدفع الفعلي
            $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_installments');
    }
};
