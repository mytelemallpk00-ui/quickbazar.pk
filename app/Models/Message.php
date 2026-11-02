<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    public const SENDER_CUSTOMER = 'customer';

    public const SENDER_BOT = 'bot';

    public const SENDER_AGENT = 'agent';

    protected $fillable = [
        'chat_id',
        'message',
        'sender_type',
    ];

    /** @return BelongsTo<Chat, $this> */
    public function chat(): BelongsTo
    {
        return $this->belongsTo(Chat::class);
    }
}
