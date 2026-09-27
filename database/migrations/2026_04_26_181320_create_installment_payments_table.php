<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('installment_payments', function (Blueprint $table) {
            $table->id();
            
            // الربط بجدول الأقساط الأساسي
            $table->foreignId('student_installment_id')->constrained('student_installments')->cascadeOnDelete();
            
            // المبالغ والبيانات المطلوبة للإيصال
            $table->decimal('amount_paid', 10, 3);
            $table->dateTime('payment_date');
            $table->string('payment_method')->default('cash');
            $table->string('receipt_number')->unique();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('installment_payments');
    }
};