<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetCustody extends Model
{
    // السماح بإدخال كل الحقول
    protected $guarded = [];

    // علاقة العهدة بالأصل
    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    // علاقة العهدة بالموظف المستلم
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}