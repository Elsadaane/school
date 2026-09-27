<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseReturnItem extends Model
{
    use HasFactory;

    // الحقول المسموح حفظها
    protected $fillable = [
        'purchase_return_id',
        'product_id',
        'returned_quantity',
        'unit_cost',
        'row_total',
        'is_defective',
        'return_reason',
    ];

    // ================== العلاقات (Relationships) ==================

    // البند يتبع فاتورة مرتجع رئيسية
    public function purchaseReturn()
    {
        return $this->belongsTo(PurchaseReturn::class);
    }

    // البند عبارة عن منتج معين في المخزن
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}