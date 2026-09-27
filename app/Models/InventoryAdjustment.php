<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryAdjustment extends Model
{
    use HasFactory;

    // السماح بالإضافة في كل الحقول
    protected $guarded = [];

    // 🌟 علاقة السند بالبنود بتاعته (علاقة 1 إلى متعدد)
    public function items()
    {
        return $this->hasMany(InventoryAdjustmentItem::class, 'inventory_adjustment_id');
    }

    // 🌟 علاقة السند بالفرع (المدرسة)
    public function branch()
    {
        return $this->belongsTo(Branche::class, 'branch_id');
    }

    // 🌟 علاقة السند بالموظف اللي أنشأه
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // 🌟 علاقة السند بالمدير اللي اعتمده أو رفضه
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}