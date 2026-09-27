<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. جدول سند التسوية الأساسي
        Schema::create('inventory_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users'); // الموظف اللي عمل الجرد
            $table->foreignId('approved_by')->nullable()->constrained('users'); // المدير اللي اعتمد
            
            $table->string('adjustment_number')->unique(); // رقم السند (ADJ-001)
            $table->date('adjustment_date');
            
            // حالة السند: مسودة (لسه مسمعش في المخزن) - معتمد (خصم/زود المخزن) - مرفوض
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 2. جدول تفاصيل الجرد للمنتجات
        Schema::create('inventory_adjustment_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_adjustment_id')->constrained('inventory_adjustments')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            
            $table->integer('system_quantity'); // الكمية اللي كانت في السيستم وقت الجرد
            $table->integer('actual_quantity'); // الكمية الحقيقية اللي لقوها على الرف
            $table->integer('difference'); // الفرق (لو بالموجب يبقى زيادة، لو بالسالب يبقى عجز)
            
            $table->string('reason')->nullable(); // سبب العجز أو الزيادة
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_adjustment_items');
        Schema::dropIfExists('inventory_adjustments');
    }
};