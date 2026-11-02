<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class GoogleIndexLog extends Model
{
    public const ACTION_UPDATED = 'URL_UPDATED';

    public const ACTION_DELETED = 'URL_DELETED';

    public const STATUS_SUCCESS = 'success';

    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'url',
        'action',
        'status',
        'http_status',
        'message',
        'response_body',
        'indexable_type',
        'indexable_id',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'response_body' => 'array',
        ];
    }

    public function indexable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isSuccess(): bool
    {
        return $this->status === self::STATUS_SUCCESS;
    }
}
