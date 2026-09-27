<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassSchedule extends Model
{
    use HasFactory;

    protected $guarded = [];

    // الفرع
    public function branch()
    {
        return $this->belongsTo(Branche::class);
    }

    // العام الدراسي
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    // الصف (المرحلة)
    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    // الشعبة (الفصل)
    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    // المادة الدراسية
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    // المعلم
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
}