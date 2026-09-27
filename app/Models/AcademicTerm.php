<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class AcademicTerm extends Model
{
    protected $guarded = [];

    public function academicYear() { return $this->belongsTo(AcademicYear::class); }
}