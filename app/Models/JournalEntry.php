<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JournalEntry extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'branch_id',
        'entry_number',
        'entry_date',
        'description',
        'reference_type',
        'reference_id',
        'created_by'
    ];

    // لجلب أطراف القيد (المدين والدائن)
    public function items()
    {
        return $this->hasMany(JournalItem::class);
    }

    // لمعرفة القيد ده بتاع إيه بالظبط (فاتورة طالب، مصروفات، إلخ)
    public function reference()
    {
        return $this->morphTo();
    }

    // دالة مساعدة سريعة للمحاسبين: تتأكد إن القيد متزن (المدين = الدائن)
    public function isBalanced()
    {
        $totalDebit = $this->items()->sum('debit');
        $totalCredit = $this->items()->sum('credit');
        
        return round($totalDebit, 3) === round($totalCredit, 3);
    }
}