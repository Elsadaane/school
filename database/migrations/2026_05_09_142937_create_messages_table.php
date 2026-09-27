<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->cascadeOnDelete();
            
            // مرسل الرسالة (عشان نعرف الرسالة دي جاية من إدارة ولا معلم ولا ولي أمر)
            $table->morphs('sender'); 
            
            $table->text('body')->nullable();
            $table->string('attachment')->nullable(); // لو هيبعت صورة أو ملف
            $table->timestamp('read_at')->nullable(); // عشان علامة الصح (Seen)
            
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('messages'); }
};