<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeadFront extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "lead_front";

    protected $fillable = [
        'name', 
        'assignee', 
        'product', 
        'quantity', 
        'price', 
        'total', 
        'commission', 
        'vc', 
        'sdm', 
        'liquid', 
        'mimo', 
        'bank_name', 
        'bank_account', 
        'note', 
        'linked_lead',
        'edited_at', 
    ];
}
