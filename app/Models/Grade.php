<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;

    protected $guarded = [];

    // 🔥 دي العلاقة اللي لارافيل بيدور عليها ومش لاقيها 🔥
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }
    
    // لو عندك علاقة بالشعب (Sections) ضيفها بالمرة
    public function sections()
    {
        return $this->hasMany(Section::class, 'grade_id');
    }
    public function branches()
    {
        return $this->belongsToMany(Branche::class, 'branch_grade', 'grade_id', 'branch_id');
    }
    // علاقة الصف بالطلاب (عشان نعدهم في الكارت)
    public function students()
    {
        return $this->hasMany(Student::class, 'grade_id');
    }
    
}