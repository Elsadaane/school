<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_transfers', function (Blueprint $table) {
            $table->id();
            // الربط بالفروع بدل المخازن
            $table->foreignId('from_branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('to_branch_id')->constrained('branches')->cascadeOnDelete();
            
            $table->string('transfer_number')->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // المنشئ
            
            $table->date('transfer_date');
            
            $table->enum('status', ['pending', 'shipped', 'completed', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();

            $table->foreignId('shipped_by')->nullable()->constrained('users');
            $table->timestamp('shipped_at')->nullable();
            
            $table->foreignId('received_by')->nullable()->constrained('users');
            $table->timestamp('received_at')->nullable();
            
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('stock_transfer_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_transfer_id')->constrained('stock_transfers')->cascadeOnDelete();
            // المنتج اللي هيتخصم منه في الفرع المُرسل
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete(); 
            
            // الكمية (رقم صحيح بناءً على جدولك الحالي)
            $table->integer('quantity'); 
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_transfer_items');
        Schema::dropIfExists('stock_transfers');
    }
};