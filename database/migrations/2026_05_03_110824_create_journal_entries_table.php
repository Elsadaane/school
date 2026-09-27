<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. جدول رأس القيد (Journal Entries)
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->restrictOnDelete();
            
            $table->string('entry_number')->unique(); // رقم القيد (مثال: JE-2026-0001)
            $table->date('entry_date');
            $table->string('description'); // بيان القيد العام
            
            // الربط الديناميكي (Morph) عشان نعرف القيد ده جاي منين (فاتورة، سند قبض، راتب...)
            $table->nullableMorphs('reference'); 
            
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. جدول أطراف القيد (Journal Items)
        Schema::create('journal_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_entry_id')->constrained('journal_entries')->cascadeOnDelete();
            
            // الربط بشجرة الحسابات اللي إنت عملتها قبل كده
            $table->foreignId('financial_account_id')->constrained('financial_accounts')->restrictOnDelete();
            
            // المبالغ (دقة 3 أرقام عشرية)
            $table->decimal('debit', 15, 3)->default(0);  // مدين
            $table->decimal('credit', 15, 3)->default(0); // دائن
            
            $table->string('line_description')->nullable(); // بيان تفصيلي للسطر
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_items');
        Schema::dropIfExists('journal_entries');
    }
};