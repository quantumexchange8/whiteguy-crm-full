<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = "core_user";

    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'password',
        'last_login',
        'is_superuser',
        'first_name',
        'last_name',
        'is_staff',
        'is_active',
        'date_joined',
        'username',
        'full_name',
        'email',
        'phone_number',
        'profile_picture',
        'is_email_verified',
        'timezone',
        'country',
        'address',
        'account_type',
        'account_holder',
        'customer_type',
        'rank',
        'remark',
        'wallet_balance',
        'edited_at',
        'created_at',
        'account_manager_id',
        'site_id',
        'has_crm_access',
        'lead_status',
        'client_stage',
        'has_leads_access',
        'identification_document_1',
        'identification_document_2',
        'identification_document_3',
        'kyc_status',
        'proof_of_address_document_1',
        'proof_of_address_document_2',
        'account_id',
        'previous_broker_name',
    ];

    // /**
    //  * The attributes that should be hidden for serialization.
    //  *
    //  * @var array<int, string>
    //  */
    // protected $hidden = [
    //     // 'password',
    //     'remember_token',
    // ];

    // /**
    //  * The attributes that should be cast.
    //  *
    //  * @var array<string, string>
    //  */
    // protected $casts = [
    //     'email_verified_at' => 'datetime',
    // ];

    /**
     * Get the site of the user.
     */
    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class, 'site_id');
    }

    /**
     * Get user's orders.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'user_id');
    }
}
