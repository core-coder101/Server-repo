<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class teachers extends Model
{
    use HasFactory;

    protected $fillable = [
        'TeacherUserID',
        'TeacherDOB',
        'TeacherCNIC',
        'TeacherPhoneNumber',
        'TeacherHomeAddress',
        'TeacherReligion',
        'TeacherSalary',
        'TeacherSalaryPaid'
    ];
    
    public function students()
    {
        return $this->hasMany(students::class, 'StudentTeacherID');
    }
    public function users()
    {
        return $this->belongsTo(users::class, 'TeacherUserID');
    }
}
