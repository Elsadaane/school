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
    Schema::create('payments', function (Blueprint $table) {
        $table->id();
        $table->foreignId('financial_account_id')->constrained('financial_accounts')->cascadeOnDelete();
        
        $table->string('receipt_number')->unique();
        $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
        
        $table->string('payment_type')->nullable(); // advance, installment, refund
        $table->morphs('payable'); // عشان نربط السند بمورد أو طالب أو ولي أمر
        
        $table->decimal('amount', 15, 3); 
        
        $table->date('payment_date');
        $table->string('check_number')->nullable();
        $table->text('transaction_details');
        $table->text('notes')->nullable();
        
        $table->softDeletes();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
