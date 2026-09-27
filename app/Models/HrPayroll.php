<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrPayroll extends Model
{
    use HasFactory;

    protected $table = 'hr_payrolls';

    protected $fillable = [
        'employee_id',
        'month',
        'year',
        'working_days',
        'absent_days',
        'pasi_deduction',
        'basic_salary',
        'total_allowances',
        'total_deductions',
        'loan_deduction',
        'net_salary',
        'paid_by',
        'transaction_id',
        'financial_account_id',
        'signature_path',
        'status',
        'payment_date',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'basic_salary' => 'decimal:3',
        'total_allowances' => 'decimal:3',
        'total_deductions' => 'decimal:3',
        'loan_deduction' => 'decimal:3',
        'net_salary' => 'decimal:3',
        'pasi_deduction' => 'decimal:3',
        'working_days' => 'decimal:2',
        'absent_days' => 'decimal:2',
    ];

    // ==========================================
    // 🔥 العلاقات 🔥
    // ==========================================
    
    // علاقة الموظف
    public function employee()
    {
        return $this->belongsTo(HrEmployee::class, 'employee_id');
    }

    // علاقة الحساب المالي
    public function financialAccount()
    {
        return $this->belongsTo(FinancialAccount::class, 'financial_account_id');
    }

    // علاقة المستخدم اللي صرف الراتب
    public function paidBy()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    // علاقة تفاصيل الراتب
    public function details()
    {
        return $this->hasMany(HrPayrollDetail::class, 'hr_payroll_id');
    }

    // العلاوات فقط
    public function allowances()
    {
        return $this->hasMany(HrPayrollDetail::class, 'hr_payroll_id')
            ->where('type', 'allowance');
    }

    // الخصومات فقط
    public function deductions()
    {
        return $this->hasMany(HrPayrollDetail::class, 'hr_payroll_id')
            ->where('type', 'deduction');
    }

    // علاقة القيد المحاسبي
    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class, 'transaction_id');
    }

    // ==========================================
    // 🔥 Scopes 🔥
    // ==========================================
    
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeForMonth($query, $month, $year)
    {
        return $query->where('month', $month)->where('year', $year);
    }

    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeUnposted($query)
    {
        return $query->where('status', 'paid')->whereNull('transaction_id');
    }

    // ==========================================
    // 🔥 Accessors 🔥
    // ==========================================
    
    // نص الحالة
    public function getStatusTextAttribute()
    {
        $statuses = [
            'draft' => 'مسودة',
            'approved' => 'معتمد',
            'paid' => 'تم الصرف',
        ];
        return $statuses[$this->status] ?? 'غير محدد';
    }

    // لون الحالة
    public function getStatusColorAttribute()
    {
        $colors = [
            'draft' => 'warning',
            'approved' => 'info',
            'paid' => 'success',
        ];
        return $colors[$this->status] ?? 'secondary';
    }

    // أيقونة الحالة
    public function getStatusIconAttribute()
    {
        $icons = [
            'draft' => 'bx-edit',
            'approved' => 'bx-check-shield',
            'paid' => 'bx-check-circle',
        ];
        return $icons[$this->status] ?? 'bx-help-circle';
    }

    // هل تم الصرف؟
    public function getIsPaidAttribute()
    {
        return $this->status === 'paid';
    }

    // هل تم الترحيل المحاسبي؟
    public function getIsPostedAttribute()
    {
        return $this->transaction_id !== null;
    }

    // إجمالي الاستحقاقات (الأساسي + العلاوات)
    public function getTotalEarningsAttribute()
    {
        return $this->basic_salary + $this->total_allowances;
    }

    // إجمالي الخصومات (الإدارية + السلف + التأمينات)
    public function getTotalDeductionsSumAttribute()
    {
        return $this->total_deductions + $this->loan_deduction + $this->pasi_deduction;
    }
}