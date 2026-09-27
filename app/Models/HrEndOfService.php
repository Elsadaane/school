<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrEndOfService extends Model
{
    use HasFactory;

    protected $table = 'hr_end_of_services';

    protected $fillable = [
        'employee_id',
        'termination_date',
        'reason',
        'gratuity_amount',
        'leave_encashment',
        'loan_deductions',
        'net_payout',
        'transaction_id',
        'notes',
        'signature_path',
    ];

    protected $casts = [
        'termination_date' => 'date',
        'gratuity_amount' => 'decimal:3',
        'leave_encashment' => 'decimal:3',
        'loan_deductions' => 'decimal:3',
        'net_payout' => 'decimal:3',
    ];

    // ==========================================
    // 🔥 العلاقات 🔥
    // ==========================================
    
    public function employee()
    {
        return $this->belongsTo(HrEmployee::class, 'employee_id');
    }

    public function expense()
    {
        return $this->belongsTo(Expense::class, 'transaction_id');
    }

    // ==========================================
    // 🔥 Scopes 🔥
    // ==========================================
    
    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeForYear($query, $year)
    {
        return $query->whereYear('termination_date', $year);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('termination_date', now()->month)
                     ->whereYear('termination_date', now()->year);
    }

    public function scopeThisYear($query)
    {
        return $query->whereYear('termination_date', now()->year);
    }

    // ==========================================
    // 🔥 Accessors 🔥
    // ==========================================
    
    // نص سبب الإنهاء بالعربي
    public function getReasonTextAttribute()
    {
        $reasons = [
            'resignation' => 'استقالة',
            'termination' => 'فصل',
            'contract_end' => 'انتهاء عقد',
            'other' => 'أخرى',
        ];
        
        return $reasons[$this->reason] ?? $this->reason;
    }

    // أيقونة سبب الإنهاء
    public function getReasonIconAttribute()
    {
        $icons = [
            'resignation' => 'bx-log-out',
            'termination' => 'bx-user-x',
            'contract_end' => 'bx-file',
            'other' => 'bx-help-circle',
        ];
        
        return $icons[$this->reason] ?? 'bx-help-circle';
    }

    // لون سبب الإنهاء
    public function getReasonColorAttribute()
    {
        $colors = [
            'resignation' => 'warning',
            'termination' => 'danger',
            'contract_end' => 'info',
            'other' => 'secondary',
        ];
        
        return $colors[$this->reason] ?? 'secondary';
    }

    // إجمالي المستحقات
    public function getTotalEntitlementsAttribute()
    {
        return round($this->gratuity_amount + $this->leave_encashment, 3);
    }

    // مدة الخدمة (محسوبة من تاريخ التعيين)
    public function getServiceYearsTextAttribute()
    {
        if (!$this->employee || !$this->employee->join_date) {
            return 'غير محدد';
        }

        $joinDate = \Carbon\Carbon::parse($this->employee->join_date);
        $terminationDate = $this->termination_date;
        
        $years = $joinDate->diffInYears($terminationDate);
        $months = $joinDate->diffInMonths($terminationDate) % 12;
        
        if ($years == 0 && $months == 0) {
            return 'أقل من شهر';
        }
        
        $text = '';
        if ($years > 0) {
            $text .= $years . ' سنة';
        }
        if ($months > 0) {
            $text .= ($text ? ' و ' : '') . $months . ' شهر';
        }
        
        return $text;
    }
}