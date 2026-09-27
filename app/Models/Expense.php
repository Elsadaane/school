<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use HasFactory;
// ضيف use SoftDeletes; جوه الكلاس
protected $guarded = [];

public function category() { return $this->belongsTo(ExpenseCategory::class, 'expense_category_id'); }
public function subCategory() { return $this->belongsTo(ExpenseSubCategory::class, 'expense_sub_category_id'); }
public function financialAccount() { return $this->belongsTo(FinancialAccount::class, 'financial_account_id'); }
public function supplier() { return $this->belongsTo(Supplier::class, 'supplier_id'); }
public function user() { return $this->belongsTo(User::class, 'user_id'); }
public function branch(){return $this->belongsTo(\App\Models\Branche::class, 'branch_id');}
}
