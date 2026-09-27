<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseSubCategory extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function category() {
    return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
}
}
