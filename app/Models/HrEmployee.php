<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HrEmployee extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hr_employees';

    protected $fillable = [
        'branch_id',
        'hr_department_id',
        'hr_shift_id',
        'hr_job_grade_id',
        'user_id',
        'manager_id',
        'employee_code',
        'full_name_ar',
        'full_name_en',
        'email',
        'phone',
        'national_id',
        'nationality',
        'gender',
        'birth_date',
        'marital_status',
        'emergency_contact',
        'pasi_number',
        'is_insured',
        'job_title',
        'join_date',
        'basic_salary',
        'bank_name',
        'iban_number',
        'status',
        'termination_date',
        'termination_reason',
        'profile_image',
        'driving_license_number',
        'driving_license_type',
        'driving_license_expiry',
    ];

    // 🔥 مهم جداً: تحويل التواريخ لـ Carbon 🔥
    protected $casts = [
        'join_date' => 'date',
        'birth_date' => 'date',
        'termination_date' => 'date',
        'driving_license_expiry' => 'date',
        'basic_salary' => 'decimal:3',
        'is_insured' => 'boolean',
    ];

    // ==========================================
    // 🔥 العلاقات 🔥
    // ==========================================
    
    public function department()
    {
        return $this->belongsTo(HrDepartment::class, 'hr_department_id');
    }

    public function shift()
    {
        return $this->belongsTo(HrShift::class, 'hr_shift_id');
    }

    public function jobGrade()
    {
        return $this->belongsTo(HrJobGrade::class, 'hr_job_grade_id');
    }

    public function manager()
    {
        return $this->belongsTo(self::class, 'manager_id');
    }

    public function contracts()
    {
        return $this->hasMany(HrContract::class, 'employee_id');
    }

    public function activeContract()
    {
        return $this->hasOne(HrContract::class, 'employee_id')
            ->where('status', 'valid')
            ->latest();
    }

    public function loans()
    {
        return $this->hasMany(HrLoan::class, 'employee_id');
    }
    public function branch()
    {
        return $this->hasMany(Branche::class);
    }

    public function attendances()
    {
        return $this->hasMany(HrAttendance::class, 'employee_id');
    }

    public function custodies()
    {
        return $this->hasMany(HrCustody::class, 'employee_id');
    }

    public function leaveBalances()
    {
        return $this->hasMany(HrLeaveBalance::class, 'employee_id');
    }

    public function payrolls()
    {
        return $this->hasMany(HrPayroll::class, 'employee_id');
    }

    // ==========================================
    // 🔥 Scopes 🔥
    // ==========================================
    
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeTerminated($query)
    {
        return $query->where('status', 'terminated');
    }

    // ==========================================
    // 🔥 Accessors 🔥
    // ==========================================
    
    // مدة الخدمة كنص
    public function getServiceDurationTextAttribute()
    {
        if (!$this->join_date) {
            return 'غير محدد';
        }

        $joinDate = \Carbon\Carbon::parse($this->join_date);
        $endDate = $this->termination_date ? \Carbon\Carbon::parse($this->termination_date) : now();
        
        $years = $joinDate->diffInYears($endDate);
        $months = $joinDate->diffInMonths($endDate) % 12;

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

    // هل الموظف نشط؟
    public function getIsActiveAttribute()
    {
        return $this->status === 'active';
    }

    // لون الحالة
    public function getStatusColorAttribute()
    {
        $colors = [
            'active' => 'success',
            'on_leave' => 'info',
            'suspended' => 'warning',
            'terminated' => 'danger',
        ];
        return $colors[$this->status] ?? 'secondary';
    }
}