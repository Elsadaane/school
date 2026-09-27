<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_transfer_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_transfer_id')->constrained('student_transfers')->cascadeOnDelete();
            $table->foreignId('financial_account_id')->constrained('financial_accounts');
            $table->decimal('amount', 10, 3);
            $table->date('payment_date');
            $table->string('payment_method')->default('cash'); // cash, card, bank_transfer
            $table->string('receipt_number')->unique();
            $table->text('notes')->nullable();
            $table->foreignId('received_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_transfer_payments');
    }
};