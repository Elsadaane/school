<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentInstallment extends Model
{
    use HasFactory;

    protected $guarded = [];

    // 1. الربط بالعقد
    public function contract() 
    { 
        return $this->belongsTo(StudentContract::class, 'student_contract_id'); 
    }

    // ✅ 2. Alias لحل مشكلة ReportController
    public function studentContract()
    {
        return $this->contract();
    }

    // 🔥 3. جلب بيانات الطالب (مباشرة وبأعلى سرعة) 🔥
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    // 4. الدفعات
    public function payments()
    {
        return $this->hasMany(InstallmentPayment::class, 'student_installment_id');
    }
}