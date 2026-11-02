<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Chat extends Model
{
    public const STATUS_ACTIVE = 'active';

    public const STATUS_CLOSED = 'closed';

    public const STATUS_PENDING_AGENT = 'pending_agent';

    protected $fillable = [
        'session_id',
        'customer_name',
        'customer_phone',
        'status',
    ];

    /** @return HasMany<Message, $this> */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public static function generateSessionId(): string
    {
        return 'chat_'.Str::lower(Str::random(32));
    }

    public static function findOrCreateForSession(
        ?string $sessionId,
        ?string $customerName = null,
        ?string $customerPhone = null,
    ): self {
        if (filled($sessionId)) {
            $existing = static::query()->where('session_id', $sessionId)->first();
            if ($existing) {
                if ($customerName !== null && $customerName !== '') {
                    $existing->forceFill(['customer_name' => $customerName])->save();
                }
                if ($customerPhone !== null && $customerPhone !== '') {
                    $existing->forceFill(['customer_phone' => $customerPhone])->save();
                }

                return $existing;
            }
        }

        return static::query()->create([
            'session_id' => filled($sessionId) ? $sessionId : static::generateSessionId(),
            'customer_name' => $customerName,
            'customer_phone' => $customerPhone,
            'status' => self::STATUS_ACTIVE,
        ]);
    }
}
