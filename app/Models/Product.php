<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // 🔥 1. استدعاء مكتبة الحذف الآمن 🔥

class Product extends Model
{
    use HasFactory, SoftDeletes; // 🔥 2. تفعيل الحذف الآمن هنا 🔥

    protected $guarded = [];

    // علاقة المنتج بالفرع
    public function branch()
    {
        return $this->belongsTo(Branche::class, 'branch_id');
    }

    // علاقة المنتج بتفاصيل التحويلات (عشان لو حبيت تعرف المنتج ده اتحول فين وإمتى)
    public function transferItems()
    {
        return $this->hasMany(StockTransferItem::class, 'product_id');
    }
}