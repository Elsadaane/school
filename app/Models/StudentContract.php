<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class StudentContract extends Model
{
    use HasFactory, LogsActivity;

    protected $guarded = [];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('contracts');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    // ✅ تم التصحيح
    public function branch()
    {
        return $this->belongsTo(Branche::class, 'branch_id');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    // ✅ علاقات إضافية
    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function items()
    {
        return $this->hasMany(StudentContractItem::class);
    }

    public function installments()
    {
        return $this->hasMany(StudentInstallment::class, 'student_contract_id');
    }
    
      // ==========================================
    // 🚌 علاقات المواصلات المحدثة
    // ==========================================
    
    // علاقة خط السير
    public function transportRoute()
    {
        return $this->belongsTo(\App\Models\TransportRoute::class, 'transport_route_id');
    }

    // علاقة باص الذهاب (الصباحي)
    public function morningBus()
    {
        return $this->belongsTo(\App\Models\Bus::class, 'morning_bus_id');
    }

    // علاقة باص العودة (المسائي)
    public function afternoonBus()
    {
        return $this->belongsTo(\App\Models\Bus::class, 'afternoon_bus_id');
    }
}