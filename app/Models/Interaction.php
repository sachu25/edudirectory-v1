<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class Interaction extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'college_id',
        'contact_person_id',
        'user_id',
        'interaction_status_id',
        'contact_date',
        'contact_mode_id',
        'college_response',
        'remarks',
        'next_followup_date'
    ];

    protected $casts = [
        'contact_date' => 'datetime',
        'next_followup_date' => 'date',
    ];

    public function college()
    {
        return $this->belongsTo(College::class);
    }

    public function contactPerson()
    {
        return $this->belongsTo(ContactPerson::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function status()
    {
        return $this->belongsTo(InteractionStatus::class, 'interaction_status_id');
    }

    public function purposes()
    {
        return $this->belongsToMany(Purpose::class);
    }

    public function contactMode()
    {
        return $this->belongsTo(ContactMode::class, 'contact_mode_id');
    }
}
