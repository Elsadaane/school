<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bus extends Model
{
    use HasFactory, SoftDeletes; 

    protected $guarded = [];

    // ==========================================
    // 1. علاقة الفرع
    // ==========================================
    public function branch()
    {
        return $this->belongsTo(Branche::class, 'branch_id');
    }

    // ==========================================
    // 2. علاقة السنة الدراسية
    // ==========================================
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    // ==========================================
    // 3. 🔄 علاقات عقود الطلاب (المحدثة)
    // ==========================================
    
    // العقود التي تستخدم هذا الباص للذهاب (الصباح)
    public function morningContracts()
    {
        return $this->hasMany(StudentContract::class, 'morning_bus_id');
    }

    // العقود التي تستخدم هذا الباص للعودة (المساء)
    public function afternoonContracts()
    {
        return $this->hasMany(StudentContract::class, 'afternoon_bus_id');
    }

    // 🛡️ دالة مساعدة لمنع حذف الباص إذا كان مرتبطاً بأي عقد (ذهاب أو إياب)
    public function hasActiveContracts()
    {
        return ($this->morningContracts()->count() > 0) || ($this->afternoonContracts()->count() > 0);
    }

    // ==========================================
    // 4. علاقة خط السير (Transport Route)
    // ==========================================
    public function transportRoute()
    {
        return $this->belongsTo(TransportRoute::class, 'transport_route_id');
    }

    // ==========================================
    // 5. علاقة السائق (من الموارد البشرية)
    // ==========================================
    public function driver()
    {
        return $this->belongsTo(HrEmployee::class, 'driver_id');
    }

    // ==========================================
    // 6. 🆕 علاقة المشرف (من الموارد البشرية)
    // ==========================================
    public function supervisor()
    {
        return $this->belongsTo(HrEmployee::class, 'supervisor_id');
    }
    // ==========================================
// 🚌 السمات المحسوبة للمقاعد المتاحة
// ==========================================

/**
 * حساب المقاعد المتبقية في رحلة الصباح
 * (يُستخدم فقط عند الحاجة الفردية، ليس في القوائم)
 */
public function getAvailableMorningSeatsAttribute()
{
    $booked = \App\Models\StudentContract::where('morning_bus_id', $this->id)
        ->whereIn('contract_status', ['active', 'draft']) // نحسب النشط والمسودة
        ->where('academic_year_id', \App\Models\AcademicYear::where('is_current', true)->value('id'))
        ->count();
    
    return max($this->capacity - $booked, 0);
}

/**
 * حساب المقاعد المتبقية في رحلة المساء
 */
public function getAvailableAfternoonSeatsAttribute()
{
    $booked = \App\Models\StudentContract::where('afternoon_bus_id', $this->id)
        ->whereIn('contract_status', ['active', 'draft'])
        ->where('academic_year_id', \App\Models\AcademicYear::where('is_current', true)->value('id'))
        ->count();
    
    return max($this->capacity - $booked, 0);
}

/**
 * هل الباص ممتلئ صباحاً؟
 */
public function getIsFullMorningAttribute()
{
    return $this->available_morning_seats <= 0;
}

/**
 * هل الباص ممتلئ مساءً؟
 */
public function getIsFullAfternoonAttribute()
{
    return $this->available_afternoon_seats <= 0;
}
}