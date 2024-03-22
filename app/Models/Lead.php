<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lead extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "core_lead";

    protected $fillable = [
        'date', 
        'first_name', 
        'last_name', 
        'country', 
        'date_of_birth',
        'occupation',
        'address',
        'date_oppd_in', 
        'campaign_product',
        'sdm',
        'agents_book',
        'account_manager',
        'vc', 
        'data_type',
        'data_source',
        'data_code',
        'email', 
        'email_alt_1',
        'email_alt_2',
        'email_alt_3',
        'phone_number',
        'phone_number_alt_1',
        'phone_number_alt_2',
        'phone_number_alt_3',
        'private_remark',
        'remark',
        'appointment_start_at',
        'appointment_end_at',
        'last_called', 
        'assignee_read_at',
        'give_up_at', 
        'appointment_label',
        'contact_outcome',
        'stage',
        'assignee', 
        'created_by',
        'delete_at',
    ];

    /**
     * Get the linked lead front.
     */
    public function leadfront(): HasOne
    {
        return $this->hasOne(LeadFront::class, 'linked_lead');
    }

    /**
     * Get the linked lead notes.
     */
    public function leadnotes(): HasMany
    {
        return $this->hasMany(LeadNote::class, 'linked_lead');
    }

    /**
     * Get the linked lead changelogs.
     */
    public function leadchangelogs(): HasMany
    {
        return $this->hasMany(LeadChangelog::class, 'lead_id');
    }
}
