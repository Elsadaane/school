<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrFinancialType extends Model
{
    use HasFactory;

    protected $table = 'hr_financial_types';

    protected $fillable = [
        'name_ar',
        'name_en',
        'type',        // allowance / deduction
        'value_type',  // fixed / percentage
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ==========================================
    // 🔥 العلاقات 🔥
    // ==========================================
    
    // الحركات المالية المرتبطة بهذا النوع
    public function employeeFinancials()
    {
        return $this->hasMany(HrEmployeeFinancial::class, 'financial_type_id');
    }

    // علاوات العقود المرتبطة بهذا النوع
    public function contractAllowances()
    {
        return $this->hasMany(HrContractAllowance::class, 'financial_type_id');
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

    // النشطة فقط
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ==========================================
    // 🔥 Accessors 🔥
    // ==========================================
    
    // اسم النوع بالعربي
    public function getTypeNameArAttribute()
    {
        return $this->type === 'allowance' ? 'علاوة' : 'خصم';
    }

    // لون النوع (للعرض في الواجهة)
    public function getTypeColorAttribute()
    {
        return $this->type === 'allowance' ? 'success' : 'danger';
    }

    // أيقونة النوع
    public function getTypeIconAttribute()
    {
        return $this->type === 'allowance' ? 'arrow-up-circle' : 'arrow-down-circle';
    }

    // اسم نوع القيمة
    public function getValueTypeNameAttribute()
    {
        return $this->value_type === 'fixed' ? 'مبلغ ثابت' : 'نسبة مئوية';
    }
}