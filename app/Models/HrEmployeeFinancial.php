<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrEmployeeFinancial extends Model
{
    use HasFactory;

    protected $table = 'hr_employee_financials';

    protected $fillable = [
        'employee_id',
        'financial_type_id',
        'amount',
        'action_date',
        'notes',
        'status',        // 🆕 pending / processed
        'payroll_id',    // 🆕 ربط بمسير الرواتب
    ];

    protected $casts = [
        'action_date' => 'date',
        'amount' => 'decimal:3',
    ];

    // ==========================================
    // 🔥 العلاقات 🔥
    // ==========================================
    
    // علاقة الموظف
    public function employee()
    {
        return $this->belongsTo(HrEmployee::class, 'employee_id');
    }

    // علاقة النوع المالي (علاوة/خصم)
    public function financialType()
    {
        return $this->belongsTo(HrFinancialType::class, 'financial_type_id');
    }

    // 🆕 علاقة مسير الرواتب
    public function payroll()
    {
        return $this->belongsTo(HrPayroll::class, 'payroll_id');
    }

    // ==========================================
    // 🔥 Scopes للفلترة السريعة 🔥
    // ==========================================
    
    // الحركات المعلقة (غير معالجة)
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // الحركات المُعالجة (مرتبطة بمسير رواتب)
    public function scopeProcessed($query)
    {
        return $query->where('status', 'processed');
    }

    // العلاوات فقط
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

    // حركات شهر معين
    public function scopeForMonth($query, $month, $year)
    {
        return $query->whereMonth('action_date', $month)
                     ->whereYear('action_date', $year);
    }

    // حركات موظف معين
    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    // ==========================================
    // 🔥 Accessors (خصائص محسوبة) 🔥
    // ==========================================
    
    // هل الحركة مُعالجة؟
    public function getIsProcessedAttribute()
    {
        return $this->status === 'processed';
    }

    // هل الحركة معلقة؟
    public function getIsPendingAttribute()
    {
        return $this->status === 'pending';
    }

    // اسم النوع بالعربي
    public function getTypeNameAttribute()
    {
        return $this->financialType ? $this->financialType->name_ar : 'غير محدد';
    }

    // نوع الحركة (allowance/deduction)
    public function getTransactionTypeAttribute()
    {
        return $this->financialType ? $this->financialType->type : null;
    }

    // نص الحالة
    public function getStatusTextAttribute()
    {
        return $this->status === 'processed' ? 'مُعالجة' : 'معلقة';
    }

    // لون الحالة (للعرض في الواجهة)
    public function getStatusColorAttribute()
    {
        return $this->status === 'processed' ? 'success' : 'warning';
    }

    // أيقونة الحالة
    public function getStatusIconAttribute()
    {
        return $this->status === 'processed' ? 'check-circle' : 'clock';
    }

    // ==========================================
    // 🔥 دوال مساعدة 🔥
    // ==========================================
    
    // ربط الحركة بمسير رواتب
    public function linkToPayroll($payrollId)
    {
        return $this->update([
            'status' => 'processed',
            'payroll_id' => $payrollId,
        ]);
    }

    // إلغاء الربط بمسير الرواتب
    public function unlinkFromPayroll()
    {
        return $this->update([
            'status' => 'pending',
            'payroll_id' => null,
        ]);
    }

    // التحقق من إمكانية التعديل
    public function canBeModified()
    {
        return $this->status === 'pending';
    }

    // التحقق من إمكانية الحذف
    public function canBeDeleted()
    {
        return $this->status === 'pending';
    }
}