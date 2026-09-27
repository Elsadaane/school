<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrJobGrade extends Model
{
    protected $guarded = [];

    public function employees()
    {
        return $this->hasMany(HrEmployee::class, 'hr_job_grade_id');
    }
}