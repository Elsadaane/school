<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
     Schema::create('assets', function (Blueprint $table) {
    $table->id();
    $table->string('asset_code')->unique();
    $table->foreignId('branch_id')->constrained('branches')->restrictOnDelete();
    
    // التصنيفات والمورد
    $table->foreignId('asset_category_id')->constrained('asset_categories')->restrictOnDelete();
    $table->foreignId('asset_sub_category_id')->nullable()->constrained('asset_sub_categories')->restrictOnDelete();
    $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->restrictOnDelete();
    $table->string('supplier_invoice_no')->nullable();
    
    // المبالغ والإهلاك
    $table->decimal('amount', 15, 3)->default(0);
    $table->decimal('tax_percent', 5, 2)->default(0);
    $table->decimal('tax_amount', 15, 3)->default(0);
    $table->decimal('total_amount', 15, 3)->default(0);
    $table->decimal('accumulated_depreciation', 15, 3)->default(0);
    $table->decimal('depreciation_rate', 5, 2)->nullable();
    
    // المدفوعات والربط المالي
    $table->enum('payment_type', ['opening', 'cash', 'installment'])->default('cash');
    $table->decimal('paid_amount', 15, 3)->default(0);
    $table->decimal('remaining_amount', 15, 3)->default(0);
    $table->date('transaction_date');
    $table->foreignId('financial_account_id')->nullable()->constrained('financial_accounts')->restrictOnDelete();
    $table->string('check_number')->nullable();
    
    // التفاصيل والمرفقات
    $table->text('transaction_details')->nullable(); 
    $table->text('notes')->nullable();
    $table->string('attachment')->nullable();
    
    // المستخدمين والعُهد
    $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); 
    
    // ✅ التصحيح الجوهري: ربط الموظف بجدول hr_employees بدلاً من users
    $table->foreignId('current_employee_id')->nullable()->constrained('hr_employees')->nullOnDelete(); 
    
    // الحالة
    $table->enum('status', ['active', 'returned', 'suspended'])->default('active');
    $table->text('return_reason')->nullable();
    
    $table->timestamps();
    $table->softDeletes();
});
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};