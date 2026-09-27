<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
       Schema::create('student_installments', function (Blueprint $table) {
            $table->id();
            
            // الروابط
            $table->foreignId('student_contract_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            
            // بيانات القسط
            $table->decimal('amount', 10, 3); // مبلغ القسط 
            $table->decimal('paid_amount', 10, 3)->default(0); // المدفوع منه
            $table->decimal('remaining_amount', 10, 3)->default(0); // المتبقي من القسط
            
            $table->date('due_date'); // تاريخ الاستحقاق
            $table->enum('status', ['paid', 'unpaid', 'partial'])->default('unpaid'); 
            $table->date('paid_date')->nullable();
            $table->text('notes')->nullable(); 
            
            $table->timestamps();
        });
    }
    
    public function down(): void { Schema::dropIfExists('student_installments'); }
};