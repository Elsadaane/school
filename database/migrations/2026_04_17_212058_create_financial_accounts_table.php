<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('account_code')->nullable()->unique(); // كود الشجرة المحاسبية
            $table->string('name_ar');
            $table->string('name_en')->nullable();
            
            // 🔥 التعديل الأول: الرصيد الافتتاحي والرصيد الحالي 🔥
            $table->decimal('opening_balance', 15, 3)->default(0); // رصيد بداية المدة
            $table->decimal('balance', 15, 3)->default(0); // الرصيد الحالي المتغير
            
            $table->boolean('is_active')->default(1);
            
            // الشجرة المحاسبية
            $table->enum('account_type', ['asset', 'liability', 'equity', 'revenue', 'expense'])->default('asset');
            $table->foreignId('parent_id')->nullable()->constrained('financial_accounts')->nullOnDelete();
            $table->enum('normal_balance', ['debit', 'credit'])->default('debit');
            
            // 🔥 التعديل الثاني: حماية الحسابات من الحذف النهائي 🔥
            $table->softDeletes(); 
            $table->timestamps();

            // 🔥 التعديل الثالث: تسريع استدعاء الشجرة المحاسبية 🔥
            $table->index('parent_id');
            $table->index('account_type');
                        $table->text('description')
                  ->nullable()
                  ->comment('وصف الحساب المالي');

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_accounts');
    }
};