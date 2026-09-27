<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class HrContract extends Model
{
    use HasFactory;

    protected $table = 'hr_contracts';

    protected $fillable = [
        'employee_id',
        'contract_number',
        'start_date',
        'end_date',
        'total_salary',
        'attachment',
        'status',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'total_salary' => 'decimal:3',
    ];

    // ==========================================
    // 🔥 العلاقات 🔥
    // ==========================================
    
    // الموظف صاحب العقد
    public function employee()
    {
        return $this->belongsTo(HrEmployee::class, 'employee_id');
    }

    // البدلات والعلاوات المرتبطة بالعقد
    public function allowances()
    {
        return $this->hasMany(HrContractAllowance::class, 'hr_contract_id');
    }

    // إجمالي البدلات
    public function totalAllowances()
    {
        return $this->allowances()->sum('amount');
    }

    // ==========================================
    // 🔥 Scopes 🔥
    // ==========================================
    
    // العقود السارية حالياً
    public function scopeActive($query)
    {
        return $query->where('status', 'valid')
                     ->whereDate('end_date', '>=', now());
    }

    // العقود المنتهية
    public function scopeExpired($query)
    {
        return $query->where(function($q) {
            $q->whereIn('status', ['expired', 'terminated'])
              ->orWhere(function($sub) {
                  $sub->where('status', 'valid')
                      ->whereDate('end_date', '<', now());
              });
        });
    }

    // العقود اللي هتنتهي قريباً (خلال 30 يوم)
    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->where('status', 'valid')
                     ->whereDate('end_date', '>=', now())
                     ->whereDate('end_date', '<=', now()->addDays($days));
    }

    // عقود موظف معين
    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    // العقود السارية فقط
    public function scopeValid($query)
    {
        return $query->where('status', 'valid');
    }

    // ==========================================
    // 🔥 Accessors 🔥
    // ==========================================
    
    // هل العقد ساري؟
    public function getIsActiveAttribute()
    {
        return $this->status === 'valid' && $this->end_date >= now();
    }

    // هل العقد منتهي؟
    public function getIsExpiredAttribute()
    {
        return $this->status === 'expired' || 
               ($this->status === 'valid' && $this->end_date < now());
    }

    // هل العقد مفسوخ؟
    public function getIsTerminatedAttribute()
    {
        return $this->status === 'terminated';
    }

    // هل العقد ينتهي قريباً؟
    public function getIsExpiringSoonAttribute()
    {
        return $this->status === 'valid' && 
               $this->end_date >= now() && 
               $this->end_date <= now()->addDays(30);
    }

    // عدد الأيام المتبقية
    public function getDaysRemainingAttribute()
    {
        if ($this->isExpired || $this->isTerminated) {
            return 0;
        }
        return max(0, now()->diffInDays($this->end_date));
    }

    // نص الحالة
    public function getStatusTextAttribute()
    {
        if ($this->isTerminated) {
            return 'مفسوخ';
        } elseif ($this->isExpired) {
            return 'منتهي';
        } elseif ($this->isExpiringSoon) {
            return "ينتهي خلال {$this->days_remaining} يوم";
        } else {
            return 'ساري';
        }
    }

    // لون الحالة
    public function getStatusColorAttribute()
    {
        if ($this->isTerminated) {
            return 'secondary';
        } elseif ($this->isExpired) {
            return 'danger';
        } elseif ($this->isExpiringSoon) {
            return 'warning';
        } else {
            return 'success';
        }
    }

    // أيقونة الحالة
    public function getStatusIconAttribute()
    {
        if ($this->isTerminated) {
            return 'bx-x-circle';
        } elseif ($this->isExpired) {
            return 'bx-time';
        } elseif ($this->isExpiringSoon) {
            return 'bx-alarm-exclamation';
        } else {
            return 'bx-check-circle';
        }
    }

    // مدة العقد بالأشهر
    public function getDurationMonthsAttribute()
    {
        if (!$this->start_date || !$this->end_date) {
            return 0;
        }
        return $this->start_date->diffInMonths($this->end_date);
    }

    // مدة العقد كنص
    public function getDurationTextAttribute()
    {
        $months = $this->duration_months;
        $years = floor($months / 12);
        $remainingMonths = $months % 12;

        if ($years == 0 && $remainingMonths == 0) {
            return 'أقل من شهر';
        }

        $text = '';
        if ($years > 0) {
            $text .= $years . ' سنة';
        }
        if ($remainingMonths > 0) {
            $text .= ($text ? ' و ' : '') . $remainingMonths . ' شهر';
        }

        return $text;
    }

    // إجمالي البدلات المحسوبة
    public function getTotalAllowancesAmountAttribute()
    {
        return $this->allowances()->sum('amount');
    }

    // الراتب الشامل (أساسي + بدلات)
    public function getTotalCompensationAttribute()
    {
        return $this->total_salary + $this->total_allowances_amount;
    }
}