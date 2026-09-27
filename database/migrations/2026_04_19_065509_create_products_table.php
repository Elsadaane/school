<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            
            $table->string('name_ar');
            $table->string('name_en')->nullable();
            
            // 🔥 1. شلنا كلمة unique() من هنا خالص 🔥
            $table->string('sku')->nullable(); 
            
            $table->enum('type', ['service', 'product'])->default('product');
            $table->decimal('cost_price', 10, 3)->default(0); 
            $table->decimal('selling_price', 10, 3)->default(0); 
            $table->decimal('tax_percent', 5, 2)->default(0); 
            
            $table->integer('quantity')->default(0); 
            $table->integer('alert_quantity')->default(5); 
            $table->integer('sold_quantity')->default(0); 
            
            $table->text('description_ar')->nullable();
            $table->text('description_en')->nullable();
            $table->integer('defective_quantity')->default(0);
            
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // 🔥 2. ضفنا القاعدة دي: الكود ميتكررش جوه نفس الفرع فقط 🔥
            $table->unique(['branch_id', 'sku'], 'unique_sku_per_branch');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};