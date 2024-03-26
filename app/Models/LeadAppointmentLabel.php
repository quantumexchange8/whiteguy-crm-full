<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeadAppointmentLabel extends Model
{
    use HasFactory;

    protected $table = "core_leadappointmentlabel";

    protected $fillable = [
        'title',
        'description',
        'edited_at',
        'created_at',
    ];

    /**
     * Get the leads for the lead appointment label.
     */
    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class, 'appointment_label_id');
    }
}
