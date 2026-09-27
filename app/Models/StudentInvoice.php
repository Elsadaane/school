<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentInvoice extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    // ✅ تم التصحيح
    public function branch() 
    { 
        return $this->belongsTo(Branche::class); 
    }
    
    public function academicYear() 
    { 
        return $this->belongsTo(AcademicYear::class); 
    }
    
    public function student() 
    { 
        return $this->belongsTo(Student::class); 
    }
    
    public function parent() 
    { 
        return $this->belongsTo(Guardian::class, 'parent_id'); 
    }
    
    public function installments() 
    { 
        return $this->hasMany(StudentInstallment::class); 
    }
}