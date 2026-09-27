<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('financial_years', function (Blueprint $table) {
            $table->id();
            $table->integer('year')->unique(); // السنة (مثال: 2026)
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_closed')->default(false); // هل مقفولة؟
            $table->foreignId('closed_by')->nullable()->constrained('users'); // مين اللي قفلها
            $table->timestamp('closed_at')->nullable(); // اتقفلت إمتى بالظبط
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('financial_years');
    }
};