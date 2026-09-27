<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrLoan extends Model
{
    use HasFactory;

    protected $table = 'hr_loans';

    protected $fillable = [
        'employee_id',
        'loan_amount',
        'months_to_pay',
        'monthly_installment',
        'start_deduction_month',
        'paid_amount',
        'status',
        'notes',
        'completed_at',
        'financial_record_id',
    ];

    protected $casts = [
        'start_deduction_month' => 'date',
        'completed_at' => 'date',
        'loan_amount' => 'decimal:3',
        'monthly_installment' => 'decimal:3',
        'paid_amount' => 'decimal:3',
    ];

    // ==========================================
    // 🔥 العلاقات 🔥
    // ==========================================
    
    public function employee()
    {
        return $this->belongsTo(HrEmployee::class, 'employee_id');
    }

    public function financialRecord()
    {
        return $this->belongsTo(HrEmployeeFinancial::class, 'financial_record_id');
    }

    // ==========================================
    // 🔥 Scopes 🔥
    // ==========================================
    
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeStartingFrom($query, $month, $year)
    {
        return $query->whereMonth('start_deduction_month', $month)
                     ->whereYear('start_deduction_month', $year);
    }

    // ==========================================
    // 🔥 Accessors 🔥
    // ==========================================
    
    // المبلغ المتبقي
    public function getRemainingAmountAttribute()
    {
        return round($this->loan_amount - $this->paid_amount, 3);
    }

    // عدد الأقساط المتبقية
    public function getRemainingInstallmentsAttribute()
    {
        if ($this->monthly_installment <= 0) return 0;
        return max(0, ceil($this->remaining_amount / $this->monthly_installment));
    }

    // نسبة السداد
    public function getPaymentPercentageAttribute()
    {
        if ($this->loan_amount <= 0) return 0;
        return round(($this->paid_amount / $this->loan_amount) * 100, 1);
    }

    // نص الحالة
    public function getStatusTextAttribute()
    {
        $statuses = [
            'pending' => 'في الانتظار',
            'active' => 'نشطة',
            'completed' => 'مكتملة السداد',
            'canceled' => 'ملغاة',
        ];
        return $statuses[$this->status] ?? 'غير محدد';
    }

    // لون الحالة
    public function getStatusColorAttribute()
    {
        $colors = [
            'pending' => 'warning',
            'active' => 'primary',
            'completed' => 'success',
            'canceled' => 'secondary',
        ];
        return $colors[$this->status] ?? 'secondary';
    }

    // أيقونة الحالة
    public function getStatusIconAttribute()
    {
        $icons = [
            'pending' => 'bx-time-five',
            'active' => 'bx-loader-circle',
            'completed' => 'bx-check-circle',
            'canceled' => 'bx-x-circle',
        ];
        return $icons[$this->status] ?? 'bx-help-circle';
    }

    // هل السلفة مكتملة؟
    public function getIsCompletedAttribute()
    {
        return $this->status === 'completed' || $this->remaining_amount <= 0;
    }
}