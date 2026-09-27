<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentEvaluation extends Model
{
    use HasFactory;

    protected $guarded = []; // عشان نسمح بإدخال الداتا بدون مشاكل الـ Mass Assignment

    // العلاقة مع الطالب
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    // العلاقة مع العام الدراسي
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    // العلاقة مع المعلم اللي قام بالتقييم (عشان حقل evaluated_by)
    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'evaluated_by');
    }
}