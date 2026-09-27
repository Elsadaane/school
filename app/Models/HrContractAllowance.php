<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrContractAllowance extends Model
{
    use HasFactory;

    protected $table = 'hr_contract_allowances';

    protected $fillable = [
        'hr_contract_id',
        'financial_type_id',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:3',
    ];

    // ==========================================
    // 🔥 العلاقات 🔥
    // ==========================================
    
    // العقد المرتبط
    public function contract()
    {
        return $this->belongsTo(HrContract::class, 'hr_contract_id');
    }

    // النوع المالي (بدل/علاوة)
    public function financialType()
    {
        return $this->belongsTo(HrFinancialType::class, 'financial_type_id');
    }

    // ==========================================
    // 🔥 Scopes 🔥
    // ==========================================
    
    // البدلات فقط
    public function scopeAllowances($query)
    {
        return $query->whereHas('financialType', function($q) {
            $q->where('type', 'allowance');
        });
    }

    // الخصومات فقط
    public function scopeDeductions($query)
    {
        return $query->whereHas('financialType', function($q) {
            $q->where('type', 'deduction');
        });
    }

    // لعقد معين
    public function scopeForContract($query, $contractId)
    {
        return $query->where('hr_contract_id', $contractId);
    }

    // ==========================================
    // 🔥 Accessors 🔥
    // ==========================================
    
    // نوع البدل (علاوة أو خصم)
    public function getTypeTextAttribute()
    {
        return $this->financialType ? $this->financialType->name_ar : 'غير محدد';
    }

    // هل هو علاوة؟
    public function getIsAllowanceAttribute()
    {
        return $this->financialType && $this->financialType->type === 'allowance';
    }

    // هل هو خصم؟
    public function getIsDeductionAttribute()
    {
        return $this->financialType && $this->financialType->type === 'deduction';
    }

    // المبلغ مع العلامة
    public function getSignedAmountAttribute()
    {
        if ($this->isAllowance) {
            return '+' . number_format($this->amount, 3);
        } elseif ($this->isDeduction) {
            return '-' . number_format($this->amount, 3);
        }
        return number_format($this->amount, 3);
    }
}