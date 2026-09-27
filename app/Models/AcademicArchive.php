<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicArchive extends Model
{
    protected $guarded = [];

    // 🔥 تحويل نصوص الـ JSON لـ Arrays أوتوماتيكياً 🔥
    protected $casts = [
        'student_snapshot'   => 'array',
        'financial_snapshot' => 'array',
        'academic_snapshot'  => 'array',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class)->withTrashed(); // withTrashed عشان لو الطالب اتنقل أو اتحذف مستقبلاً
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }
}