<?php

namespace App\Models;

use App\Traits\Messageable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Teacher extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable,Messageable;

    protected $guard = 'teacher';

    protected $fillable = [
        'name_ar', 'name_en', 'email', 'password', 'phone', 'specialization', 'national_id', 'address', 'nationality',
        'branch_id',
        'contract_terms_ar',
        'contract_terms_en',
        'signature_data',
        'status'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];
    public function sections()
    {
        // بافتراض إنك عامل جدول وسيط اسمه section_teacher
        return $this->belongsToMany(Section::class, 'section_teacher', 'teacher_id', 'section_id');
    }
    // علاقة المعلم بالمواد الدراسية (Many-to-Many)
    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'subject_teacher', 'teacher_id', 'subject_id');
    }
    // علاقة المعلم بالفرع المدرسـي
    public function branch()
    {
        return $this->belongsTo(Branche::class, 'branch_id');
    }
    public function hrEmployee()
    {
        return $this->belongsTo(HrEmployee::class, 'hr_employee_id');
    }
}