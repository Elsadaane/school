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
        // 1. جدول المرتجع الأساسي (إشعار دائن للمورد)
        Schema::create('purchase_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_invoice_id')->constrained('purchase_invoices')->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained('suppliers')->restrictOnDelete();
            
            $table->string('return_number')->unique(); // رقم المرتجع PR-123
            $table->date('return_date');
            
            $table->decimal('total_return_amount', 12, 3)->default(0); // إجمالي قيمة المرتجع
            $table->text('notes')->nullable();
            
            $table->timestamps();
        });

        // 2. بنود المرتجع (هنا السر كله 🚀)
        Schema::create('purchase_return_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_return_id')->constrained('purchase_returns')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            
            $table->integer('returned_quantity');
            $table->decimal('unit_cost', 10, 3); 
            $table->decimal('row_total', 12, 3);
            
            // 🔥 هنا بنسأل: هل البضاعة دي بايظة؟ 🔥
            $table->boolean('is_defective')->default(false); 
            $table->string('return_reason')->nullable(); // سبب الإرجاع (مكسور، مقاس غلط، إلخ)
            
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_returns');
    }
};
