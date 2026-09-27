<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseReturn extends Model
{
    use HasFactory;

    // الحقول المسموح حفظها
    protected $fillable = [
        'purchase_invoice_id',
        'supplier_id',
        'return_number',
        'return_date',
        'total_return_amount',
        'notes',
    ];

    // ================== العلاقات (Relationships) ==================

    // المرتجع يتبع مورد معين
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    // المرتجع مرتبط بفاتورة مشتريات أصلية
    public function purchaseInvoice()
    {
        return $this->belongsTo(PurchaseInvoice::class);
    }

    // المرتجع يحتوي على عدة أصناف (بنود)
    public function items()
    {
        return $this->hasMany(PurchaseReturnItem::class);
    }
}