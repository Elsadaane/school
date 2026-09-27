<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ExamCategory extends Model
{
    protected $guarded = [];

    public function term() { return $this->belongsTo(AcademicTerm::class, 'academic_term_id'); }
    public function grade() { return $this->belongsTo(Grade::class); }
    public function subject() { return $this->belongsTo(Subject::class); }
    
    // الربط مع الدرجات اللي اترصدت للتقييم ده
    public function marks() { return $this->hasMany(StudentMark::class); }
}