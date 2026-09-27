<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $guarded = [];

    // إضافة الـ casts للتاريخ
    protected $casts = [
        'payment_date' => 'date',
    ];
    

    public function financialAccount() { return $this->belongsTo(FinancialAccount::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function payable() { return $this->morphTo(); }
}