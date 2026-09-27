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
    Schema::create('incomes', function (Blueprint $table) {
        $table->id();
        $table->string('income_code')->unique();
        $table->foreignId('branch_id')->constrained('branches')->restrictOnDelete();
        $table->foreignId('income_category_id')->constrained('income_categories');
        $table->foreignId('income_sub_category_id')->nullable()->constrained('income_sub_categories');
        $table->foreignId('financial_account_id')->constrained('financial_accounts'); 
        $table->foreignId('user_id')->constrained('users'); 
        
        $table->string('source_name')->nullable(); 
        $table->string('reference_no')->nullable(); 
        
        $table->decimal('amount', 15, 3);
        $table->decimal('tax_percent', 5, 2)->default(0);
        $table->decimal('tax_amount', 15, 3)->default(0);
        $table->decimal('total_amount', 15, 3);
        
        $table->date('income_date');
        $table->string('check_number')->nullable();
        $table->string('transaction_details');
        $table->text('notes')->nullable();
        $table->string('attachment')->nullable();
        
        $table->softDeletes();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incomes');
    }
};
