<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeadNote extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "lead_notes";

    protected $fillable = [
        'linked_lead',
        'note', 
        'user_editable', 
        'created_by',
    ];
}
