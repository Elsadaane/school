<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('student_contract_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_contract_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products');            
            $table->string('description')->nullable(); // 🔥 إضافة وصف تفصيلي للبند لو احتاجه المحاسب
            
            $table->decimal('unit_price', 10, 3);
            $table->integer('quantity')->default(1);
            $table->decimal('tax_percent', 5, 2)->default(0); 
            $table->decimal('row_total', 10, 3); // إجمالي السطر (السعر * الكمية + الضريبة)
            
            $table->timestamps();
        });
    }
    
    public function down(): void { Schema::dropIfExists('student_contract_items'); }
};