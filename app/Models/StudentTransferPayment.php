<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentTransferPayment extends Model
{
    protected $guarded = [];

    public function transfer()
    {
        return $this->belongsTo(StudentTransfer::class, 'student_transfer_id');
    }

    public function financialAccount()
    {
        return $this->belongsTo(FinancialAccount::class);
    }

    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}