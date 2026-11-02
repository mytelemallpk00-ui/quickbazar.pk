<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourierApiSetting extends Model
{
    protected $fillable = [
        'platform',
        'credentials',
        'enabled',
    ];

    protected function casts(): array
    {
        return [
            'credentials' => 'encrypted:array',
            'enabled' => 'boolean',
        ];
    }

    public static function forPlatform(string $platform): ?self
    {
        return static::query()->where('platform', $platform)->first();
    }
}
