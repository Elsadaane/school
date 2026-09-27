<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentAttendance extends Model
{
    protected $guarded = [];

    public function student() 
    { 
        return $this->belongsTo(Student::class); 
    }

    public function academicYear() 
    { 
        return $this->belongsTo(AcademicYear::class); 
    }

    public function grade() 
    { 
        return $this->belongsTo(Grade::class); 
    }

    public function section() 
    { 
        return $this->belongsTo(Section::class); 
    }

    public function recordedBy() 
    { 
        return $this->belongsTo(User::class, 'recorded_by'); 
    }
}