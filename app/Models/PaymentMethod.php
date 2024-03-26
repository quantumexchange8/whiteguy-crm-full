<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $table = "core_paymentmethod";

    public $timestamps = false;

    protected $fillable = [
        'title', 
        'description',
        'bank_name_label',
        'bank_name',
        'account_name_label',
        'account_name',
        'account_number_label',
        'account_number',
        'logo',
        'currency',
        'edited_at',
        'created_at', 
    ];
    
    /**
     * Get the payment submissions that uses this payment method.
     */
    public function paymentSubmissions(): HasMany
    {
        return $this->hasMany(PaymentSubmission::class, 'payment_method_id');
    }
}
