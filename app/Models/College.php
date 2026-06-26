<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class College extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    public static $states = [
        'Andaman and Nicobar Islands',
        'Andhra Pradesh',
        'Arunachal Pradesh',
        'Assam',
        'Bihar',
        'Chandigarh',
        'Chhattisgarh',
        'Dadra and Nagar Haveli and Daman and Diu',
        'Delhi',
        'Goa',
        'Gujarat',
        'Haryana',
        'Himachal Pradesh',
        'Jammu and Kashmir',
        'Jharkhand',
        'Karnataka',
        'Kerala',
        'Ladakh',
        'Lakshadweep',
        'Madhya Pradesh',
        'Maharashtra',
        'Manipur',
        'Meghalaya',
        'Mizoram',
        'Nagaland',
        'Odisha',
        'Puducherry',
        'Punjab',
        'Rajasthan',
        'Sikkim',
        'Tamil Nadu',
        'Telangana',
        'Tripura',
        'Uttar Pradesh',
        'Uttarakhand',
        'West Bengal'
    ];

    public static function sanitizeState(string $stateName): string
    {
        $cleanInput = preg_replace('/[^a-z0-9]/', '', strtolower(trim($stateName)));
        
        foreach (self::$states as $stdState) {
            $cleanStd = preg_replace('/[^a-z0-9]/', '', strtolower($stdState));
            if ($cleanInput === $cleanStd) {
                return $stdState;
            }
        }
        
        return ucwords(trim($stateName));
    }

    protected $fillable = [
        'name', 'is_university', 'university_id', 'code', 'type', 'naac_grade', 
        'nirf_ranking', 'established_year', 'website', 'address', 
        'district', 'state', 'country', 'pin_code', 'office_phone', 
        'office_mobile', 'official_email', 'student_strength', 
        'faculty_strength', 'courses_offered', 'hostel_facility', 
        'placement_cell', 'remarks', 'status'
    ];

    protected $casts = [
        'is_university' => 'boolean',
    ];

    public function university()
    {
        return $this->belongsTo(University::class, 'university_id');
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
