<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class NonAcademicClient extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'name',
        'industry',
        'website',
        'email',
        'phone',
        'address',
        'contact_person_name',
        'contact_person_designation',
        'contact_person_email',
        'contact_person_phone',
        'contacted_user_id',
        'contact_reason',
        'contact_date',
        'contact_mode',
        'remarks'
    ];

    public function contactedEmployee()
    {
        return $this->belongsTo(User::class, 'contacted_user_id');
    }

    public function interactions()
    {
        return $this->hasMany(NonAcademicInteraction::class, 'non_academic_client_id');
    }
}
