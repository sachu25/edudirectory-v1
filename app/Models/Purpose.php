<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class Purpose extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = ['name', 'status'];
}
