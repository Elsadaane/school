<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetInstallments extends Model
{
    protected $guarded = [];

    public function asset() {
        return $this->belongsTo(Asset::class, 'asset_id');
    }
    // 🔥 العلاقة الجديدة: القسط بيسجل قيد مدفوعات في الخزنة 🔥
    public function payments()
    {
        return $this->morphMany(Payment::class, 'payable');
    }
}