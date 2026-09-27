<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branche extends Model
{
    use HasFactory;
    
    // 🔥 فتح كل الحقول للإضافة (لأننا عاملين Validation قوي في الكنترولر) 🔥
    protected $guarded = [];

    // ==========================================
    // 🌟 العلاقات (Relationships) 🌟
    // ==========================================

    /**
     * علاقة الفرع بالطلاب (الفرع الواحد فيه كذا طالب)
     */
    public function students()
    {
        return $this->hasMany(Student::class, 'branch_id');
    }
    public function academicYears()
    {
        // بنحدد الموديل، واسم جدول الوسيط، واسم عمود الفرع، واسم عمود السنة
        return $this->belongsToMany(AcademicYear::class, 'academic_year_branch', 'branch_id', 'academic_year_id');
    }
    // داخل ملف app/Models/Branche.php


    /**
     * علاقة الفرع بالمعلمين
     */
    public function teachers()
    {
        return $this->hasMany(Teacher::class, 'branch_id');
    }

    /**
     * علاقة الفرع بأولياء الأمور
     */
    public function guardians()
    {
        return $this->hasMany(Guardian::class, 'branch_id');
    }

    /**
     * علاقة الفرع بالسنوات الدراسية (كل فرع ليه سنواته وتقويماته)
     */
    // public function academicYears()
    // {
    //     return $this->hasMany(AcademicYear::class, 'branch_id');
    // }

    /**
     * علاقة الفرع بالصفوف الدراسية (لو الصفوف بتختلف من فرع للتاني)
     */
    public function grades()
    {
        return $this->hasMany(Grade::class, 'branch_id');
    }
    /**
     * علاقة الفرع بالشعب الدراسية (الفرع الواحد فيه كذا شعبة)
     */
    public function sections()
    {
        return $this->hasMany(Section::class, 'branch_id');
    }
    // التحويلات الصادرة من هذا الفرع
    public function outgoingTransfers()
    {
        return $this->hasMany(StockTransfer::class, 'from_branch_id');
    }

    // التحويلات الواردة لهذا الفرع
    public function incomingTransfers()
    {
        return $this->hasMany(StockTransfer::class, 'to_branch_id');
    }

    // التحويلات الصادرة من هذا الفرع
    // public function outgoingTransfers()
    // {
    //     return $this->hasMany(StockTransfer::class, 'from_branch_id');
    // }

    // التحويلات الواردة لهذا الفرع
    // public function incomingTransfers()
    // {
    //     return $this->hasMany(StockTransfer::class, 'to_branch_id');
    // }
}