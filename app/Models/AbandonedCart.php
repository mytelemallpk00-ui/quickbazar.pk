<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class AbandonedCart extends Model
{
    protected $fillable = [
        'email',
        'user_id',
        'session_token',
        'cart_items',
        'subtotal',
        'currency',
        'last_activity_at',
        'reminders_sent',
        'last_reminder_at',
        'recovered_order_id',
        'recovered_at',
        'unsubscribed_at',
    ];

    protected function casts(): array
    {
        return [
            'cart_items' => 'array',
            'subtotal' => 'float',
            'last_activity_at' => 'datetime',
            'last_reminder_at' => 'datetime',
            'recovered_at' => 'datetime',
            'unsubscribed_at' => 'datetime',
        ];
    }

    public static function issueToken(): string
    {
        return Str::random(48);
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<Order, $this> */
    public function recoveredOrder(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'recovered_order_id');
    }
}
