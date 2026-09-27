<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrJobCandidate extends Model
{
    protected $guarded = [];

    // ربط المرشح بالقسم اللي متقدم عليه
    public function department()
    {
        return $this->belongsTo(HrDepartment::class, 'department_id');
    }
}