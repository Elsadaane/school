<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrLeaveRequest extends Model
{
    use HasFactory;

    protected $guarded = [];

    // 🌟 العلاقة اللي كانت ناقصة وعاملة الإيرور 🌟
    public function employee()
    {
        return $this->belongsTo(HrEmployee::class, 'employee_id');
    }

    // بالمرة نضيف علاقة نوع الإجازة عشان لو احتجناها في الـ Blade
    public function leaveType()
    {
        return $this->belongsTo(HrLeaveType::class, 'leave_type_id');
    }
}