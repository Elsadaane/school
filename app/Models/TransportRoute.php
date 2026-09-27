<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransportRoute extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    // ==========================================
    // 1. علاقة الفرع التابع له خط السير
    // ==========================================
    public function branch()
    {
        return $this->belongsTo(Branche::class, 'branch_id');
    }

    // ==========================================
    // 2. علاقة الحافلات التي تعمل على هذا الخط
    // ==========================================
    public function buses()
    {
        return $this->hasMany(Bus::class, 'transport_route_id');
    }

    // ==========================================
    // 3. علاقة عقود الطلاب التي تستخدم هذا الخط
    // ==========================================
    public function studentContracts()
    {
        return $this->hasMany(StudentContract::class, 'transport_route_id');
    }
}