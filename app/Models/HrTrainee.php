<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrTrainee extends Model
{
    protected $guarded = [];

    // القسم اللي بيتدرب فيه
    public function department()
    {
        return $this->belongsTo(HrDepartment::class, 'hr_department_id');
    }
}