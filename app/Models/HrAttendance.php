<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrAttendance extends Model
{
    use HasFactory;
    protected $guarded = [];
    // 🔥 ضيف الدالة دي (العلاقة مع الموظف) 🔥
    public function employee()
    {
        return $this->belongsTo(HrEmployee::class, 'employee_id');
    }
}
