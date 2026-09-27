<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmergencyOnlineDay extends Model
{
    use HasFactory;

    protected $fillable = [
        'target_date',
        'branch_id',
        'reason',
        'created_by',
    ];

    // عشان لو عايزين نعرف الكاستم ده لفرع معين
    public function branch()
    {
        return $this->belongsTo(Branche::class, 'branch_id'); // تأكد إن اسم المودل عندك Branche
    }

    // عشان نعرف مين اللي أصدر القرار
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}