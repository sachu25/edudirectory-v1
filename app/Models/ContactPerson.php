<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class ContactPerson extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $table = 'contact_persons';

    protected $fillable = [
        'college_id', 'name', 'designation_id', 'department', 'email', 
        'mobile', 'whatsapp', 'status', 'is_priority'
    ];

    public function college()
    {
        return $this->belongsTo(College::class);
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }
}
