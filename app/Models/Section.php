<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    protected $guarded = [];

    // 🔥 العلاقة اللي لارافيل بيدور عليها ومش لاقيها (علاقة الشعبة بالسنة الدراسية) 🔥
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    // علاقة الشعبة بالصف الدراسي
    public function grade()
    {
        return $this->belongsTo(Grade::class, 'grade_id');
    }

    // علاقة الشعبة بالفرع (لو الشعبة مربوطة بفرع معين زي ما عملنا في الـ Seeder)
    public function branch()
    {
        // تأكد بس إن اسم الموديل عندك Branche ولا Branch
        return $this->belongsTo(Branche::class, 'branch_id'); 
    }
    
    // لو الشعبة مربوطة بمعلمين معينين (Many-to-Many) ممكن تضيفها برضه لاحقاً
    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'section_teacher');
    }
    public function students()
    {
        return $this->hasMany(Student::class, 'section_id');
    }
}