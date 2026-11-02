<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    public const PAYMENT_PENDING = 'pending';

    public const PAYMENT_PAID = 'paid';

    public const PAYMENT_REFUNDED = 'refunded';

    public const FULFILLMENT_UNFULFILLED = 'unfulfilled';

    public const FULFILLMENT_FULFILLED = 'fulfilled';

    public const STATUS_OPEN = 'open';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_CANCELLED = 'cancelled';

    public const STATUS_RETURNED = 'returned';

    protected $fillable = [
        'user_id',
        'order_number',
        'customer_email',
        'customer_phone',
        'customer_first_name',
        'customer_last_name',
        'shipping_address_line',
        'shipping_city',
        'shipping_postal',
        'shipping_country',
        'shipping_method_id',
        'courier_code',
        'courier_label',
        'tracking_number',
        'courier_booking_ref',
        'courier_booking_status',
        'courier_error',
        'subtotal',
        'discount_total',
        'tax_total',
        'shipping_total',
        'total',
        'currency',
        'payment_method',
        'payment_status',
        'fulfillment_status',
        'status',
        'promo_code',
        'notes',
        'fulfilled_at',
        'delivered_at',
        'returned_at',
        'invoice_sent_at',
        'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'float',
            'discount_total' => 'float',
            'tax_total' => 'float',
            'shipping_total' => 'float',
            'total' => 'float',
            'fulfilled_at' => 'datetime',
            'delivered_at' => 'datetime',
            'returned_at' => 'datetime',
            'invoice_sent_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return HasMany<OrderItem, $this> */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /** @param  Builder<Order>  $query */
    public function scopeForAccount(Builder $query, User $user): Builder
    {
        return $query->where(function (Builder $inner) use ($user): void {
            $inner->where('user_id', $user->id)
                ->orWhere('customer_email', $user->email);
        });
    }

    public function belongsToAccount(User $user): bool
    {
        if ($this->user_id && (int) $this->user_id === (int) $user->id) {
            return true;
        }

        return strcasecmp((string) $this->customer_email, (string) $user->email) === 0;
    }

    public function customerName(): string
    {
        return trim($this->customer_first_name.' '.$this->customer_last_name);
    }

    public function getRouteKeyName(): string
    {
        return 'order_number';
    }

    public static function generateOrderNumber(): string
    {
        do {
            $number = 'BTG-'.random_int(100000, 999999);
        } while (static::query()->where('order_number', $number)->exists());

        return $number;
    }

    public function markFulfilled(): void
    {
        $this->update([
            'fulfillment_status' => self::FULFILLMENT_FULFILLED,
            'fulfilled_at' => now(),
        ]);
    }

    public function markUnfulfilled(): void
    {
        $this->update([
            'fulfillment_status' => self::FULFILLMENT_UNFULFILLED,
            'fulfilled_at' => null,
            'delivered_at' => null,
        ]);
    }

    public function markDelivered(): void
    {
        $updates = [
            'delivered_at' => now(),
            'status' => self::STATUS_COMPLETED,
        ];

        if ($this->fulfillment_status !== self::FULFILLMENT_FULFILLED) {
            $updates['fulfillment_status'] = self::FULFILLMENT_FULFILLED;
            $updates['fulfilled_at'] = now();
        }

        $this->update($updates);
    }

    public function markReturned(): void
    {
        $this->update([
            'status' => self::STATUS_RETURNED,
            'returned_at' => now(),
            'payment_status' => self::PAYMENT_REFUNDED,
        ]);
    }

    public function cancelOrder(): void
    {
        $this->update([
            'status' => self::STATUS_CANCELLED,
            'cancelled_at' => now(),
        ]);
    }

    public function markInvoiceSent(): void
    {
        $this->update(['invoice_sent_at' => now()]);
    }

    /** @return array<string, string> */
    public static function paymentStatusLabels(): array
    {
        return [
            self::PAYMENT_PENDING => 'Pending',
            self::PAYMENT_PAID => 'Paid',
            self::PAYMENT_REFUNDED => 'Refunded',
        ];
    }

    /** @return array<string, string> */
    public static function fulfillmentStatusLabels(): array
    {
        return [
            self::FULFILLMENT_UNFULFILLED => 'Unfulfilled',
            self::FULFILLMENT_FULFILLED => 'Fulfilled',
        ];
    }

    /** @return array<string, string> */
    public static function statusLabels(): array
    {
        return [
            self::STATUS_OPEN => 'Open',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_CANCELLED => 'Cancelled',
            self::STATUS_RETURNED => 'Returned',
        ];
    }
}
