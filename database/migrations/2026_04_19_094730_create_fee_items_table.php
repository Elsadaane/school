<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('fee_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->default(1)->constrained('branches')->cascadeOnDelete();
            
            $table->string('name_ar');
            $table->string('name_en')->nullable();
            
            $table->decimal('price', 10, 3)->default(0); // السعر الأساسي
            $table->integer('quantity')->default(1); // العدد أو الكمية الافتراضية (غالباً 1)
            $table->decimal('tax_percent', 5, 2)->default(0); // نسبة الضريبة (مثلاً 5%)
            
            $table->decimal('total', 10, 3)->default(0); // الإجمالي بعد الضريبة
            
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('fee_items'); }
};