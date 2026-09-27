<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $guarded = [];

    // المورد له دفعات (سندات صرف)
    public function payments()
    {
        return $this->morphMany(Payment::class, 'payable');
    }
    public function branch()
    {
        return $this->belongsTo(Branche::class, 'branch_id');
    }

    public function purchaseInvoices()
    {
        return $this->hasMany(PurchaseInvoice::class, 'supplier_id');
    }
}