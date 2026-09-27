<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrShift extends Model
{
    protected $guarded = [];

    public function employees()
    {
        return $this->hasMany(HrEmployee::class, 'hr_shift_id');
    }
}