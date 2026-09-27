<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrDepartment extends Model
{
    protected $guarded = [];

    public function employees()
    {
        return $this->hasMany(HrEmployee::class, 'hr_department_id');
    }
}