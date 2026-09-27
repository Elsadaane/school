<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentPromotion extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function fromAcademicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'from_academic_year_id');
    }

    public function toAcademicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'to_academic_year_id');
    }

    public function fromGrade()
    {
        return $this->belongsTo(Grade::class, 'from_grade_id');
    }

    public function toGrade()
    {
        return $this->belongsTo(Grade::class, 'to_grade_id');
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
    public function fromSection()
    {
        return $this->belongsTo(Section::class, 'from_section_id');
    }
}