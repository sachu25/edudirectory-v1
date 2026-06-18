<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class College extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'university_id', 'name', 'code', 'type', 'naac_grade', 
        'nirf_ranking', 'established_year', 'website', 'address', 
        'district', 'state', 'country', 'pin_code', 'office_phone', 
        'office_mobile', 'official_email', 'student_strength', 
        'faculty_strength', 'courses_offered', 'hostel_facility', 
        'placement_cell', 'remarks'
    ];

    public function university()
    {
        return $this->belongsTo(University::class);
    }

    public function contactPersons()
    {
        return $this->hasMany(ContactPerson::class);
    }

    public function interactions()
    {
        return $this->hasMany(Interaction::class);
    }
}
