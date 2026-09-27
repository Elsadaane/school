<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Income extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    // 🔥 علاقة الإيراد بالقسم الرئيسي 🔥
    public function category()
    {
        return $this->belongsTo(IncomeCategory::class, 'income_category_id');
    }
    public function branch() {
    return $this->belongsTo(\App\Models\Branche::class, 'branch_id');
}

    // 🔥 علاقة الإيراد بالقسم الفرعي 🔥
    public function subCategory()
    {
        return $this->belongsTo(IncomeSubCategory::class, 'income_sub_category_id');
    }

    // 🔥 علاقة الإيراد بالخزنة (الحساب المالي) 🔥
    public function financialAccount()
    {
        return $this->belongsTo(FinancialAccount::class, 'financial_account_id');
    }

    // 🔥 علاقة الإيراد بالموظف الذي أدخله 🔥
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}