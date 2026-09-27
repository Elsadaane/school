<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrPayrollDetail extends Model
{
    use HasFactory;

    protected $table = 'hr_payroll_details';

    protected $fillable = [
        'hr_payroll_id',
        'financial_type_id',
        'item_name',
        'type',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:3',
    ];

    // ==========================================
    // 🔥 العلاقات 🔥
    // ==========================================
    
    // علاقة مسير الرواتب
    public function payroll()
    {
        return $this->belongsTo(HrPayroll::class, 'hr_payroll_id');
    }

    // علاقة النوع المالي
    public function financialType()
    {
        return $this->belongsTo(HrFinancialType::class, 'financial_type_id');
    }

    // ==========================================
    // 🔥 Scopes 🔥
    // ==========================================
    
    // العلاوات فقط
    public function scopeAllowances($query)
    {
        return $query->where('type', 'allowance');
    }

    // الخصومات فقط
    public function scopeDeductions($query)
    {
        return $query->where('type', 'deduction');
    }
}