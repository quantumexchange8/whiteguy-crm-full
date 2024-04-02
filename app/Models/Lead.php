<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lead extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "core_lead";
    
    public $timestamps = false;
    
    protected $fillable = [
        'date', // from date_oppd_in
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
        'contacted_at', // from last_called
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

    /**
     * User Model
     * Get the user that created the lead.
     */
    public function leadCreator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    /**
     * User Model
     * Get the user assigned to the lead.
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    /**
     * LeadFront Model
     * Get the linked lead front.
     */
    public function leadfront(): HasOne
    {
        return $this->hasOne(LeadFront::class, 'lead_id');
    }

    /**
     * LeadNote Model
     * Get the linked lead notes.
     */
    public function leadnotes(): HasMany
    {
        return $this->hasMany(LeadNote::class, 'lead_id');
    }
    
    /**
     * LeadStage Model
     * Get the lead stage of the lead.
     */
    public function stage(): BelongsTo
    {
        return $this->belongsTo(LeadStage::class, 'stage_id');
    }
    
    /**
     * LeadContactOutcome Model
     * Get the lead stage of the lead.
     */
    public function contactOutcome(): BelongsTo
    {
        return $this->belongsTo(LeadContactOutcome::class, 'contact_outcome_id');
    }
    
    /**
     * LeadAppointmentLabel Model
     * Get the lead stage of the lead.
     */
    public function appointmentLabel(): BelongsTo
    {
        return $this->belongsTo(LeadAppointmentLabel::class, 'appointment_label_id');
    }


    // /**
    //  * LeadChangelog Model
    //  * Get the linked lead changelogs.
    //  */
    // public function leadchangelogs(): HasMany
    // {
    //     return $this->hasMany(LeadChangelog::class, 'lead_id');
    // }
}
