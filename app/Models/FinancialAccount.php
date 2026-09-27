<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialAccount extends Model
{
    use HasFactory;

    protected $guarded = [];

    // ==========================================
    // 🔥 العلاقات 🔥
    // ==========================================
    
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // public function salesInvoices()
    // {
    //     return $this->hasMany(salesInvoices::class, 'financial_account_id');
    // }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    // ==========================================
    // 🔥 Accessors 🔥
    // ==========================================
    
    public function getTypeNameArAttribute()
    {
        $types = [
            'asset' => 'أصول',
            'liability' => 'خصوم',
            'equity' => 'حقوق ملكية',
            'revenue' => 'إيرادات',
            'expense' => 'مصروفات',
        ];
        return $types[$this->account_type] ?? 'غير محدد';
    }

    public function getTypeColorAttribute()
    {
        $colors = [
            'asset' => 'primary',
            'liability' => 'danger',
            'equity' => 'purple',
            'revenue' => 'success',
            'expense' => 'warning',
        ];
        return $colors[$this->account_type] ?? 'secondary';
    }

    public function getTypeIconAttribute()
    {
        $icons = [
            'asset' => 'bx-building-house',
            'liability' => 'bx-file',
            'equity' => 'bx-diamond',
            'revenue' => 'bx-trending-up',
            'expense' => 'bx-trending-down',
        ];
        return $icons[$this->account_type] ?? 'bx-help-circle';
    }
}