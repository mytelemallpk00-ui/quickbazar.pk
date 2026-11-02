<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsletterSubscriber extends Model
{
    public const LIST_STORE = 'store';

    public const LIST_PARTNER = 'partner';

    protected $fillable = [
        'email',
        'list',
        'source',
    ];

    /** @return array<string, string> */
    public static function listLabels(): array
    {
        return [
            self::LIST_STORE => 'Store newsletter',
            self::LIST_PARTNER => 'Partner / IR updates',
        ];
    }
}
