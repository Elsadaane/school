<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockTransfer extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    // الفرع المُرسل
    public function fromBranch()
    {
        return $this->belongsTo(Branche::class, 'from_branch_id');
    }

    // الفرع المُستقبل (الوجهة)
    public function toBranch()
    {
        return $this->belongsTo(Branche::class, 'to_branch_id');
    }

    // بنود التحويل (المنتجات)
    public function items()
    {
        return $this->hasMany(StockTransferItem::class, 'stock_transfer_id');
    }

    // الموظف اللي أنشأ الطلب
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // الموظف اللي شحن البضاعة
    public function shippedBy()
    {
        return $this->belongsTo(User::class, 'shipped_by');
    }

    // الموظف اللي استلم البضاعة في الفرع الجديد
    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}