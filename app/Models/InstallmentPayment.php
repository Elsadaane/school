<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstallmentPayment extends Model
{
    use HasFactory;

    // 🔥 السماح بإدخال البيانات في كل الحقول (الحل للإيرور بتاعك) 🔥
    protected $guarded = [];

    // ==========================================
    // الروابط (Relationships)
    // ==========================================

    // الدفعة دي تابعة لأي قسط؟
    public function installment()
    {
        return $this->belongsTo(StudentInstallment::class, 'student_installment_id');
    }
}