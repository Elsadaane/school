<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseInvoice extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branche::class, 'branch_id');
    }
    // ================== العلاقات (Relationships) ==================

    // الفاتورة دي ممكن يتعمل عليها أكتر من عملية إرجاع
    public function returns()
    {
        return $this->hasMany(PurchaseReturn::class, 'purchase_invoice_id');
    }

    public function items()
    {
        return $this->hasMany(PurchaseInvoiceItem::class, 'purchase_invoice_id');
    }
}