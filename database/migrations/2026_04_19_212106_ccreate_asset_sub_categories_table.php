<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('asset_sub_categories', function (Blueprint $table) {
            $table->id();
            // الربط بالقسم الرئيسي (لو اتمسح القسم الرئيسي، الفرعي يتمسح معاه)
            $table->foreignId('asset_category_id')->constrained('asset_categories')->cascadeOnDelete();
            
            $table->string('name_ar'); // اسم القسم الفرعي عربي
            $table->string('name_en')->nullable(); // اسم القسم الفرعي إنجليزي
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_sub_categories');
    }
};
