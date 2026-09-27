<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryAdjustmentItem extends Model
{
    use HasFactory;

    // السماح بالإضافة في كل الحقول
    protected $guarded = [];

    // 🌟 علاقة البند بالسند الأساسي بتاعه (علاقة انتماء)
    public function adjustment()
    {
        return $this->belongsTo(InventoryAdjustment::class, 'inventory_adjustment_id');
    }

    // 🌟 علاقة البند بالمنتج المجرود
    public function product()
    {
        // ضفنا withTrashed() عشان لو المنتج اتحذف مستقبلاً، يفضل اسمه ظاهر في أرشيف الجرد القديم
        return $this->belongsTo(Product::class, 'product_id')->withTrashed();
    }
}