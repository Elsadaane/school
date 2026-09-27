<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentContractItem extends Model
{
    use HasFactory;

    // 🔥 فك حظر الحفظ المجمع 🔥
    protected $guarded = [];

    // البند ده تابع لعقد مين؟
    public function contract()
    {
        return $this->belongsTo(StudentContract::class, 'student_contract_id');
    }

    // البند ده عبارة عن إيه؟ (رسوم، زي مدرسي، كتب..)
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
    
}