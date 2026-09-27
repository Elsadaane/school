<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseInstallment extends Model
{
    use HasFactory;

    // السماح بالحفظ في كل الحقول لمنع إيرور MassAssignment
    protected $guarded = [];

    // ==========================================
    // الروابط (Relationships)
    // ==========================================

    // العلاقة مع فاتورة المشتريات الأساسية
    public function invoice()
    {
        return $this->belongsTo(PurchaseInvoice::class, 'purchase_invoice_id');
    }

    // العلاقة مع المورد
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}