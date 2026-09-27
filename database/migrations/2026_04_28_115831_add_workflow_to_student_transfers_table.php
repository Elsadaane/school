<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_transfers', function (Blueprint $table) {
            // إضافة حالة التحويل (الورك فلو)
            $table->enum('status', [
                'pending_parent',    // في انتظار توقيع ولي الأمر
                'pending_ministry',  // في انتظار موافقة الوزارة/المديرية
                'pending_payment',   // في انتظار دفع الرسوم
                'completed',         // تم النقل بنجاح
                'rejected'           // مرفوض
            ])->default('pending_parent')->after('transfer_date');

            // بيانات موافقة الوزارة
            $table->string('ministry_approval_number')->nullable()->after('status');
            $table->date('ministry_approval_date')->nullable()->after('ministry_approval_number');
            $table->string('ministry_attachment')->nullable()->after('ministry_approval_date'); // صورة ورقة الموافقة

            // حالة الدفع لرسوم التحويل
            $table->boolean('is_fee_paid')->default(false)->after('transfer_fee');
            
            // مين الموظف اللي عمل الطلب ومين اللي اعتمده
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('student_transfers', function (Blueprint $table) {
            $table->dropColumn([
                'status', 
                'ministry_approval_number', 
                'ministry_approval_date', 
                'ministry_attachment',
                'is_fee_paid',
                'created_by',
                'approved_by'
            ]);
        });
    }
};