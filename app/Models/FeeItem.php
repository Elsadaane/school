<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeItem extends Model
{
    protected $guarded = [];

    // إضافة علاقة الفرع
    public function branch()
    {
        return $this->belongsTo(Branche::class, 'branch_id');
    }
}