<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('conversation_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->cascadeOnDelete();
            
            // الشخص المشارك (سواء كان User, Teacher, Guardian)
            $table->morphs('messageable'); 
            
            $table->timestamps();
            
            // منع تكرار نفس الشخص في نفس المحادثة
            $table->unique(['conversation_id', 'messageable_id', 'messageable_type'], 'participant_unique');
        });
    }
    public function down(): void { Schema::dropIfExists('conversation_participants'); }
};