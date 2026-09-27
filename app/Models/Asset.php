<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
    use SoftDeletes;
    protected $guarded = [];

    public function category() {
        return $this->belongsTo(AssetCategory::class, 'asset_category_id');
    }

    public function subCategory() {
        return $this->belongsTo(AssetSubCategory::class, 'asset_sub_category_id');
    }

    public function supplier() {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
    // علاقة الأصل بالفرع التابع له
    public function branch()
    {
        return $this->belongsTo(Branche::class, 'branch_id');
    }

    public function financialAccount() {
        return $this->belongsTo(FinancialAccount::class, 'financial_account_id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
    // جلب محضر العهدة الفعال حالياً لهذا الأصل
    public function currentCustody()
    {
        return $this->hasOne(\App\Models\HrCustody::class, 'asset_id')
                    ->where('status', 'with_employee')
                    ->latest();
    }
    

    public function installments() {
        return $this->hasMany(AssetInstallments::class, 'asset_id');
    }
    // علاقة الأصل بالموظف (صاحب العهدة الحالي)
    public function employee() {
        return $this->belongsTo(HrEmployee::class, 'current_employee_id');
    }
}