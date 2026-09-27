<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrPenalty extends Model
{
    use HasFactory;

    protected $table = 'hr_penalties';

    protected $fillable = [
        'employee_id',
        'violation_type',
        'penalty_action',
        'deduction_days',
        'incident_date',
        'details',
        'notes',
        'applied_to_payroll',
        'financial_record_id',
    ];

    protected $casts = [
        'incident_date' => 'date',
        'deduction_days' => 'decimal:2',
        'applied_to_payroll' => 'boolean',
    ];

    // ==========================================
    // 🔥 العلاقات 🔥
    // ==========================================
    
    // علاقة الموظف
    public function employee()
    {
        return $this->belongsTo(HrEmployee::class, 'employee_id');
    }

    // علاقة الحركة المالية المرتبطة
    public function financialRecord()
    {
        return $this->belongsTo(HrEmployeeFinancial::class, 'financial_record_id');
    }

    // ==========================================
    // 🔥 Scopes للفلترة السريعة 🔥
    // ==========================================
    
    // المخالفات المُطبقة في الرواتب
    public function scopeApplied($query)
    {
        return $query->where('applied_to_payroll', true);
    }

    // المخالفات المعلقة
    public function scopePending($query)
    {
        return $query->where('applied_to_payroll', false);
    }

    // مخالفات موظف معين
    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    // مخالفات شهر معين
    public function scopeForMonth($query, $month, $year)
    {
        return $query->whereMonth('incident_date', $month)
                     ->whereYear('incident_date', $year);
    }

    // خصومات الراتب فقط
    public function scopeDeductions($query)
    {
        return $query->where('penalty_action', 'salary_deduction');
    }

    // إنذارات فقط
    public function scopeWarnings($query)
    {
        return $query->whereIn('penalty_action', ['verbal_warning', 'written_warning']);
    }

    // ==========================================
    // 🔥 Accessors (خصائص محسوبة) 🔥
    // ==========================================
    
    // اسم الإجراء بالعربي
    public function getActionNameArAttribute()
    {
        $actions = [
            'verbal_warning' => 'إنذار شفهي',
            'written_warning' => 'إنذار كتابي',
            'salary_deduction' => 'خصم من الراتب',
            'suspension' => 'إيقاف عن العمل',
        ];
        return $actions[$this->penalty_action] ?? 'غير محدد';
    }

    // لون الإجراء (للعرض في الواجهة)
    public function getActionColorAttribute()
    {
        $colors = [
            'verbal_warning' => 'warning',
            'written_warning' => 'info',
            'salary_deduction' => 'danger',
            'suspension' => 'dark',
        ];
        return $colors[$this->penalty_action] ?? 'secondary';
    }

    // أيقونة الإجراء
    public function getActionIconAttribute()
    {
        $icons = [
            'verbal_warning' => 'bx-message-square-error',
            'written_warning' => 'bx-file',
            'salary_deduction' => 'bx-money',
            'suspension' => 'bx-user-x',
        ];
        return $icons[$this->penalty_action] ?? 'bx-help-circle';
    }

    // قيمة الخصم المحسوب (بالريال)
    public function getDeductionAmountAttribute()
    {
        if ($this->penalty_action !== 'salary_deduction' || !$this->employee) {
            return 0;
        }
        $dailyRate = $this->employee->basic_salary / 30;
        return round($dailyRate * $this->deduction_days, 3);
    }

    // نص الحالة
    public function getAppliedStatusTextAttribute()
    {
        return $this->applied_to_payroll ? 'مُطبق في الرواتب' : 'لم يُطبق بعد';
    }

    // لون الحالة
    public function getAppliedStatusColorAttribute()
    {
        return $this->applied_to_payroll ? 'success' : 'warning';
    }

    // أيقونة الحالة
    public function getAppliedStatusIconAttribute()
    {
        return $this->applied_to_payroll ? 'bx-check-circle' : 'bx-time-five';
    }
}