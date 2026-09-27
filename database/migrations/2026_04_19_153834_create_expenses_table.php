<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('expense_code')->unique(); // كود المصروف (مثال: EXP-0001)
            $table->foreignId('branch_id')->constrained('branches')->restrictOnDelete(); // الربط بالفرع

            // 1. التصنيفات
            $table->foreignId('expense_category_id')->constrained();
            $table->foreignId('expense_sub_category_id')->nullable()->constrained();

            // 2. الربط المحاسبي
            $table->foreignId('financial_account_id')->constrained('financial_accounts'); // الخزنة اللي صرفنا منها
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers'); // المورد (إن وجد)
            $table->foreignId('user_id')->constrained('users'); // الموظف المنفذ

            // 3. المبالغ
            $table->decimal('amount', 15, 3); // المبلغ الأساسي
            $table->decimal('tax_percent', 5, 2)->default(0); // نسبة الضريبة
            $table->decimal('tax_amount', 15, 3)->default(0); // قيمة الضريبة
            $table->decimal('total_amount', 15, 3); // الإجمالي المصروف

            // 4. التفاصيل
            $table->date('expense_date'); // تاريخ الصرف
            $table->string('supplier_invoice_no')->nullable(); // رقم فاتورة المورد
            $table->string('check_number')->nullable(); // رقم الشيك
            $table->string('transaction_details'); // تفاصيل المعاملة
            $table->text('notes')->nullable();
            $table->string('attachment')->nullable(); // المرفقات (صورة الفاتورة)

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};