<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrAppraisal extends Model
{
    protected $guarded = [];

    // الموظف اللي بيتقيم
    public function employee()
    {
        return $this->belongsTo(HrEmployee::class, 'employee_id');
    }

    // المدير اللي عمل التقييم (مربوط بجدول users)
    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }
}