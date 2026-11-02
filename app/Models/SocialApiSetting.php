<?php

namespace App\Models;

use App\Support\Social\SocialPlatforms;
use Illuminate\Database\Eloquent\Model;

class SocialApiSetting extends Model
{
    protected $fillable = [
        'platform',
        'credentials',
    ];

    protected function casts(): array
    {
        return [
            'credentials' => 'encrypted:array',
        ];
    }

    public static function forPlatform(string $platform): ?self
    {
        return static::query()->where('platform', $platform)->first();
    }

    /** @return list<string> */
    public static function platformKeys(): array
    {
        return SocialPlatforms::all();
    }
}
