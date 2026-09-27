<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_transfers', function (Blueprint $table) {
            // ✅ إضافة حقول التتبع المالي الدقيق
            $table->decimal('paid_amount', 10, 3)->default(0)->after('transfer_fee');
            $table->decimal('remaining_amount', 10, 3)->default(0)->after('paid_amount');
            
            // ✅ طريقة الدفع (كاش/تقسيط)
            $table->enum('payment_type', ['full', 'installment'])->default('full')->after('remaining_amount');
            
            // ✅ الخزنة المستخدمة
            $table->foreignId('financial_account_id')->nullable()->after('payment_type')
                ->constrained('financial_accounts')->nullOnDelete();
            
            // ✅ وسيلة الدفع (نقدي/بطاقة/تحويل)
            $table->string('payment_method')->nullable()->after('financial_account_id');
            
            // ✅ تاريخ آخر دفعة
            $table->date('last_payment_date')->nullable()->after('payment_method');
        });
    }

    public function down(): void
    {
        Schema::table('student_transfers', function (Blueprint $table) {
            $table->dropColumn([
                'paid_amount',
                'remaining_amount',
                'payment_type',
                'financial_account_id',
                'payment_method',
                'last_payment_date'
            ]);
        });
    }
};