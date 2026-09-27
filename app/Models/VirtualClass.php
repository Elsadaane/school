<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VirtualClass extends Model
{
    use HasFactory;

    /**
     * اسم الجدول المرتبط بالموديل في قاعدة البيانات (اختياري إذا كان مطابقاً للجمع)
     */
    protected $table = 'virtual_classes';

    /**
     * الحقول المسموح بتعبئتها تلقائياً (Mass Assignment)
     * متوافقة بالمللي مع المايجريشن والفورم
     */
    protected $fillable = [
        'section_id',
        'subject_id',
        'teacher_id',
        'meeting_id',
        'topic',
        'start_at',
        'duration',
        'join_url',
        'is_active',
        'status'
    ];

    /**
     * تحويل أنواع البيانات تلقائياً عند جلبها من الداتابيز (Data Casting)
     */
    protected $casts = [
        'start_at'  => 'datetime',
        'duration'  => 'integer',
        'is_active' => 'boolean',
    ];

    // =======================================================
    // 🔥 العلاقات الأساسية (Relationships) 🔥
    // =======================================================

    /**
     * علاقة الفصل الافتراضي بالشعبة (كل فصل ينتمي لشعبة واحدة)
     */
    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    /**
     * علاقة الفصل الافتراضي بالمادة الدراسية
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    /**
     * علاقة الفصل الافتراضي بالمعلم (مقدم الحصة)
     */
    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }
}