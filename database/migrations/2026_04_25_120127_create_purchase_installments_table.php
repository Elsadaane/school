<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_invoice_id')->constrained('purchase_invoices')->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained('suppliers')->cascadeOnDelete();
            
            $table->decimal('amount', 12, 3); // مبلغ القسط
            $table->decimal('paid_amount', 12, 3)->default(0); // المدفوع منه
            
            $table->date('due_date'); // تاريخ استحقاق القسط
            
            $table->boolean('is_paid')->default(false);
            $table->date('paid_date')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_installments');
    }
};