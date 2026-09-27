<?php

namespace App\Models;

use App\Traits\Messageable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Guardian extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable,Messageable;

    // 🔥 1. ربط الموديل بجدول guardians اللي عملناه في المايجريشن 🔥
    protected $table = 'guardians'; 
    
    // الجارد لسه اسمه parent عادي في إعدادات الـ auth.php
    protected $guard = 'parent';

    // 🔥 2. تحديث الحقول لتطابق المايجريشن ثنائي اللغة والفرع 🔥
    protected $fillable = [
        'branch_id',
        'name_ar', 'name_en', 
        'email', 'password', 'phone', 
        'national_id', 'national_id_expiry',
        'job_title_ar', 'job_title_en', 
        'work_place_ar', 'work_place_en', 
        'nationality_ar', 'nationality_en',
        'id_card_image'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    // ==========================================
    // 🌟 العلاقات (Relationships) 🌟
    // ==========================================

    /**
     * علاقة ولي الأمر بالطلاب (ولي الأمر الواحد عنده طالب أو أكثر)
     * ملاحظة: تأكد إن حقل الربط في جدول الطلاب اسمه 'parent_id' أو 'guardian_id'
     */
    public function students()
    {
        // لو حقل الربط في جدول الطلاب اسمه guardian_id غير الكلمة هنا
        return $this->hasMany(Student::class, 'parent_id'); 
    }

    /**
     * علاقة ولي الأمر بالفرع
     */
    public function branch()
    {
        return $this->belongsTo(Branche::class, 'branch_id');
    }
}