<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrShiftRoster extends Model
{
    protected $guarded = [];

    // ربط بالموظف
    public function employee()
    {
        return $this->belongsTo(HrEmployee::class, 'employee_id');
    }

    // ربط بنوع الوردية (الأساسية)
    public function shift()
    {
        return $this->belongsTo(HrShift::class, 'shift_id');
    }
}