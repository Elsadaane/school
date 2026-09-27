<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('store_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique(); // رقم الفاتورة INV-1234
            $table->foreignId('branch_id')->constrained('branches')->restrictOnDelete();
            
            // الربط بالعملاء والمستخدمين
            $table->foreignId('student_id')->nullable()->constrained('students'); // Nullable لولي الأمر أو بيع خارجي
            $table->foreignId('user_id')->constrained('users'); // الكاشير أو الموظف اللي عمل الفاتورة
            
            $table->date('invoice_date');
            
            // المبالغ
            $table->decimal('sub_total', 10, 3)->default(0);
            $table->decimal('discount', 10, 3)->default(0);
            $table->decimal('tax_amount', 10, 3)->default(0);
            $table->decimal('grand_total', 10, 3)->default(0);
            
            // الربط المالي
            $table->enum('payment_status', ['paid', 'unpaid', 'partial'])->default('paid'); 
            $table->string('payment_method')->default('cash'); // cash, card, bank_transfer
            $table->foreignId('financial_account_id')->nullable()->constrained('financial_accounts'); // الخزنة اللي دخلت فيها الفلوس
            
            $table->text('notes')->nullable(); // ملاحظات على الفاتورة
            
            $table->softDeletes(); // للحفاظ على التسلسل المحاسبي لو الفاتورة اتلغت
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('store_invoices'); }
};