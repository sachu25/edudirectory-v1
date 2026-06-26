<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class NonAcademicInteraction extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'non_academic_client_id',
        'user_id',
        'contact_date',
        'contact_mode',
        'interaction_status',
        'purpose',
        'client_response',
        'remarks',
        'next_followup_date'
    ];

    protected $casts = [
        'contact_date' => 'datetime',
        'next_followup_date' => 'date'
    ];

    public function client()
    {
        return $this->belongsTo(NonAcademicClient::class, 'non_academic_client_id');
    }

    public function employee()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
