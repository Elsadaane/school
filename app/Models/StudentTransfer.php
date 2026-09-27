<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentTransfer extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'transfer_fee' => 'decimal:3',
        'paid_amount' => 'decimal:3',
        'remaining_amount' => 'decimal:3',
        'transfer_date' => 'date',
        'last_payment_date' => 'date',
        'ministry_approval_date' => 'date',
    ];

    // ==========================================
    // 🔗 العلاقات الأساسية
    // ==========================================
    
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function fromBranch()
    {
        return $this->belongsTo(Branche::class, 'from_branch_id');
    }

    public function toBranch()
    {
        return $this->belongsTo(Branche::class, 'to_branch_id');
    }

    // ==========================================
    // 🌟 دوال مساعدة لجدول التحويلات 🌟
    // ==========================================

    // public function isFullyPaid()
    // {
    //     return $this->remaining_amount <= 0;
    // }

    public function hasPartialPayment()
    {
        return $this->paid_amount > 0 && $this->remaining_amount > 0;
    }

    // public function getPaymentStatusLabel()
    // {
    //     if ($this->isFullyPaid()) return 'مدفوع بالكامل';
    //     if ($this->hasPartialPayment()) return 'دفع جزئي';
    //     return 'غير مدفوع';
    // }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // ==========================================
    // 🔗 العلاقات الجديدة (التتبع المالي)
    // ==========================================
    
    public function payments()
    {
        return $this->hasMany(StudentTransferPayment::class);
    }

    public function financialAccount()
    {
        return $this->belongsTo(FinancialAccount::class);
    }

    // ==========================================
    // 🎯 Helper Methods
    // ==========================================
    
    /**
     * التحقق من أن التحويل مدفوع بالكامل
     */
    public function isFullyPaid()
    {
        return $this->remaining_amount <= 0;
    }

    /**
     * الحصول على حالة الدفع
     */
    public function getPaymentStatus()
    {
        if ($this->remaining_amount <= 0) return 'paid';
        if ($this->paid_amount > 0) return 'partial';
        return 'unpaid';
    }

    /**
     * الحصول على نص حالة الدفع
     */
    public function getPaymentStatusLabel()
    {
        return match($this->getPaymentStatus()) {
            'paid' => 'مدفوع بالكامل',
            'partial' => 'مدفوع جزئياً',
            'unpaid' => 'غير مدفوع',
        };
    }

    /**
     * الحصول على لون حالة الدفع
     */
    public function getPaymentStatusColor()
    {
        return match($this->getPaymentStatus()) {
            'paid' => 'success',
            'partial' => 'warning',
            'unpaid' => 'danger',
        };
    }

    /**
     * الحصول على حالة التحويل
     */
    public function getTransferStatusLabel()
    {
        return match($this->status) {
            'pending_parent' => 'في انتظار ولي الأمر',
            'pending_ministry' => 'في انتظار الوزارة',
            'pending_payment' => 'في انتظار الدفع',
            'completed' => 'مكتمل',
            'rejected' => 'مرفوض',
            default => 'غير معروف',
        };
    }

    /**
     * الحصول على لون حالة التحويل
     */
    public function getTransferStatusColor()
    {
        return match($this->status) {
            'pending_parent' => 'info',
            'pending_ministry' => 'warning',
            'pending_payment' => 'warning',
            'completed' => 'success',
            'rejected' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * حساب نسبة الدفع
     */
    public function getPaymentPercentage()
    {
        if ($this->transfer_fee <= 0) return 0;
        return round(($this->paid_amount / $this->transfer_fee) * 100, 2);
    }
}