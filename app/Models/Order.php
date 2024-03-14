<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "orders";

    protected $fillable = [
        'trade_id',
        'date',
        'action_type',
        'stock_type',
        'stock',
        'unit_price',
        'quantity',
        'total_price',
        'current_price',
        'profit',
        'status',
        'confirmed_at',
        'confirmation_name',
        'limb_stage',
        'users_id',
        'send_notification',
        'notification_title',
        'notification_description',
    ];

    /**
     * Get the user that owns the order note.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'users_id');
    }
    
    /**
     * Get the orderChangeLog for the order.
     */
    public function orderChangelogs(): HasMany
    {
        return $this->hasMany(OrderChangelog::class, 'orders_id');
    }

}
