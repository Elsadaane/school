<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetInstallment extends Model
{
    use HasFactory;

    protected $guarded = [];

    // 🔥 العلاقة الناقصة اللي كانت عاملة الإيرور 🔥
    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}