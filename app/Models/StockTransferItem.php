<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockTransferItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    // الطلب الرئيسي التابع له هذا البند
    public function transfer()
    {
        return $this->belongsTo(StockTransfer::class, 'stock_transfer_id');
    }

    // المنتج الفعلي
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}