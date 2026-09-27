<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. جدول الفاتورة الأساسي
        Schema::create('purchase_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained('suppliers')->restrictOnDelete();
            
            $table->string('invoice_number')->unique(); // رقم الفاتورة الداخلي
            $table->string('supplier_invoice_number')->nullable(); // رقم فاتورة المورد المطبوعة
            $table->date('invoice_date');
            
            // الحسابات (بـ 3 أرقام عشرية للنظام العماني)
            $table->decimal('sub_total', 12, 3)->default(0);
            $table->decimal('discount', 12, 3)->default(0);
            $table->decimal('tax_amount', 12, 3)->default(0);
            $table->decimal('grand_total', 12, 3)->default(0);
            $table->decimal('paid_amount', 12, 3)->default(0);
            $table->decimal('due_amount', 12, 3)->default(0); // الباقي للمورد
            
            $table->enum('payment_status', ['paid', 'partial', 'unpaid'])->default('unpaid');
            $table->text('notes')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. جدول تفاصيل بنود الفاتورة
        Schema::create('purchase_invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_invoice_id')->constrained('purchase_invoices')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            
            $table->decimal('unit_cost', 10, 3); // سعر الشراء للبند وقت الفاتورة
            $table->integer('quantity'); // الكمية المشتراة (هتزود رصيد المخزن)
            $table->decimal('row_total', 12, 3); // الإجمالي = السعر * الكمية
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_invoice_items');
        Schema::dropIfExists('purchase_invoices');
    }
};