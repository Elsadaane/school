<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_invoices', function (Blueprint $table) {
            // إضافة حقل نوع الدفع بعد حقل حالة الدفع
            $table->enum('payment_type', ['cash', 'auto_installment', 'manual_installment'])
                  ->default('cash')
                  ->after('payment_status');
        });
    }

    public function down(): void
    {
        Schema::table('purchase_invoices', function (Blueprint $table) {
            $table->dropColumn('payment_type');
        });
    }
};