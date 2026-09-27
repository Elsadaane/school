<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function branches()
    {
        return $this->belongsToMany(Branche::class, 'academic_year_branch', 'academic_year_id', 'branch_id');
    }

    // 🔥 دي كانت ناقصة في الموديل عشان الفحص اللي في الكنترولر يشتغل 🔥
    // public function grades()
    // {
    //     return $this->hasMany(Grade::class, 'academic_year_id');
    // }

    public function sections()
    {
        return $this->hasMany(Section::class, 'academic_year_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'academic_year_id');
    }
}