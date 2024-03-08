<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'site',
        'username',
        'password',
        'account_id',
        'first_name',
        'last_name',
        'full_legal_name',
        'email',
        'phone_number',
        'address',
        'country_of_citizenship',
        'account_holder',
        'account_type',
        'customer_type',
        'account_manager',
        'lead_status',
        'client_stage',
        'rank',
        'remark',
        'previous_broker_name',
        'kyc_status',
        'is_active',
        'has_crm_access',
        'has_leads_access',
        'is_staff',
        'is_superuser',
        'last_login',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        // 'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
