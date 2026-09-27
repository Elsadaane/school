<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Student extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $guard = 'student';

    // 🔥 ضفنا هنا الـ branch_id عشان يسمح بحفظ الفرع 🔥
    // protected $fillable = [
    //     'parent_id', 'branch_id', 'academic_year_id', 'grade_id', 'section_id',
    //     'first_name', 'second_name', 'third_name', 'fourth_name', 'tribe', 'dob', 'gender', 'nationality',
    //     'email', 'password',
    //     'mother_name', 'mother_phone', 'mother_nationality', 'mother_workplace', 'mother_work_location', 'mother_national_id', 'mother_id_expiry',
    //     'emergency_name', 'emergency_relation', 'emergency_phone',
    //     'region', 'house_number', 'street_number', 'pickup_point',
    //     'has_allergies', 'has_hearing_issues', 'has_vision_issues', 'health_notes',
    //     'transport_type',
    //     'student_photo', 'passport_copy', 'birth_certificate_copy', 'health_reports', 'vaccination_card',
    //     'agreed_to_terms', 'agreed_at', 'agreed_terms_ar', 'agreed_terms_en', 'signature_data'
    // ];
    protected $guarded = [];
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getFullNameAttribute()
    {
        // نجمع أجزاء الاسم في مصفوفة
        $nameParts = [
            $this->first_name,
            $this->second_name,
            $this->third_name,
            $this->fourth_name,
            $this->tribe
        ];

        // array_filter بتمسح أي قيمة فاضية (عشان لو مفيش اسم تالت أو رابع ميعملش مسافات زيادة)
        // implode بتدمجهم بمسافة
        return implode(' ', array_filter($nameParts));
    }
    // ==========================================
    // العلاقات (Relationships)
    // ==========================================

    // 1. علاقة الطالب بولي الأمر
    public function parent()
    {
        return $this->belongsTo(Guardian::class, 'parent_id');
    }

    // 2. علاقة الطالب بالصف الدراسي
    public function grade()
    {
        return $this->belongsTo(Grade::class, 'grade_id');
    }
    public function activeTransfers()
    {
        return $this->hasMany(StudentTransfer::class)
            ->whereIn('status', ['pending_parent', 'pending_ministry', 'pending_payment']);
    }

    public function transfers()
    {
        return $this->hasMany(StudentTransfer::class);
    }
    public function promotions()
    {
        return $this->hasMany(\App\Models\StudentPromotion::class, 'student_id');
    }

    // 3. علاقة الطالب بالسنة الدراسية
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    // 4. علاقة الطالب بالشعبة
    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }
// ==========================================
    // 🔥 علاقة الطالب بسجل الحضور والغياب 🔥
    // ==========================================
    public function attendances()
    {
        return $this->hasMany(StudentAttendance::class, 'student_id');
    }
    // 5. علاقة الطالب بالفرع
    public function branch()
    {
        return $this->belongsTo(Branche::class, 'branch_id'); // تأكد من اسم موديل الفرع عندك (Branche أو Branch)
    }
    public function bus()
    {
        return $this->belongsTo(Bus::class, 'bus_id');
    }
    // ==========================================
    // الروابط (Relationships)
    // ==========================================

    // عقود الطالب
    public function contracts()
    {
        return $this->hasMany(StudentContract::class, 'student_id');
    }
    // 🔥 علاقة الطالب بدرجاته (عشان التحليل الذكي ورصد الدرجات) 🔥
    public function marks()
    {
        return $this->hasMany(StudentMark::class, 'student_id');
    }

}