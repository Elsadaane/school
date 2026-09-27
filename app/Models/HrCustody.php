<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrCustody extends Model
{
    protected $guarded = [];

    public function employee()
    {
        return $this->belongsTo(HrEmployee::class, 'employee_id');
    }


    // 🔥 العلاقة مع جدول الأصول الثابتة 🔥
    public function asset()
    {
        return $this->belongsTo(\App\Models\Asset::class, 'asset_id');
    }
}