<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeadDuplicated extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "core_leadduplicated";
    
    public $timestamps = false;
    
    protected $fillable = [
        'date',
        'first_name', 
        'last_name', 
        'country', 
        'date_of_birth',
        'occupation',
        'vc', 
        'sdm',
        'email', 
        'email_alt_1',
        'email_alt_2',
        'email_alt_3',
        'phone_number',
        'phone_number_alt_1',
        'phone_number_alt_2',
        'phone_number_alt_3',
        'attachment',
        'private_remark',
        'remark',
        'data_source',
        'appointment_start_at',
        'appointment_end_at',
        'contacted_at',
        'assignee_read_at',
        'edited_at',
        'created_at',
        'appointment_label_id',
        'assignee_id', 
        'contact_outcome_id',
        'created_by_id',
        'stage_id',
        'give_up_at', 
        'account_manager',
        'address',
        'agents_book',
        'campaign_product',
        'data_code',
        'data_type',
        'deleted_at',
        'deleted_note',
        'sort_id',
    ];
}
