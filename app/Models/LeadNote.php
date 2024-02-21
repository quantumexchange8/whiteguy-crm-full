<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    
    /**
     * Get the post that owns the comment.
     */
    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'id');
    }
}
