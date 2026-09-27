<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JournalItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'journal_entry_id',
        'financial_account_id',
        'debit',
        'credit',
        'line_description'
    ];

    // القيد الأساسي
    public function entry()
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }

    // الحساب المالي (من شجرة الحسابات)
    public function account()
    {
        return $this->belongsTo(FinancialAccount::class, 'financial_account_id');
    }
}