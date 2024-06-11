<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class students extends Model
{
    use HasFactory;
    protected $fillable = [
        'StudentCNIC', 
        'StudentClassID', 
        'StudentUserID',
        'StudentGender', 
        'StudentTeacherID',
        'StudentDOB', 
        'StudentAdmissionApproval', 
        'StudentPhoneNumber', 
        'StudentHomeAddress', 
        'StudentMonthlyFee', 
        'StudentReligion'
    ];


    public function user()
    {
        return $this->belongsTo(users::class, 'StudentUserID');
    }

    public function class()
    {
        return $this->belongsTo(classes::class, 'StudentClassID');
    }

    // Define the relationship with the Teacher model
    public function teacher()
    {
        return $this->belongsTo(teachers::class, 'StudentTeacherID');
    }
    
    public function parents()
    {
        return $this->hasOne(parents::class, 'StudentID');
    }

}
