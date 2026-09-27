<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('store_invoice_items', function (Blueprint $table) {
            $table->id();
            
            // 🔥 التعديل هنا: كتابة اسم الجدول صراحة عشان نتجنب أي إيرور 🔥
            $table->foreignId('store_invoice_id')->constrained('store_invoices')->cascadeOnDelete();
            
            $table->foreignId('product_id')->constrained('products');
            
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 3);
            $table->decimal('row_total', 10, 3);
            
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('store_invoice_items'); }
};