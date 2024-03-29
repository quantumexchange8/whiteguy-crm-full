<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;

    protected $table = "core_order";

    protected $fillable = [
        'trade_id',
        'date',
        'action_type',
        'stock_type',
        'stock',
        'unit_price',
        'quantity',
        'current_unit_price',
        'profit',
        'status',
        'users_id',
        'is_deleted',
        'limb_stage',
        'confirmation_name',
        'confirmed_at',
    ];

    /**
     * Get the user that owns the order note.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    /**
     * Get the orderChangeLog for the order.
     */
    public function orderChangelogs(): HasMany
    {
        return $this->hasMany(OrderChangelog::class, 'orders_id');
    }

}
