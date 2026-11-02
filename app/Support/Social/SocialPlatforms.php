<?php

namespace App\Support\Social;

class SocialPlatforms
{
    /** @return list<string> */
    public static function all(): array
    {
        $keys = SocialChannelCatalog::allKeys();
        if ($keys !== []) {
            return $keys;
        }

        return ['facebook', 'instagram', 'linkedin', 'youtube', 'telegram'];
    }

    /** @return list<string> */
    public static function schedulable(): array
    {
        $keys = SocialChannelCatalog::schedulableKeys();
        if ($keys !== []) {
            return $keys;
        }

        return ['facebook', 'instagram', 'linkedin', 'youtube', 'telegram'];
    }

    public static function label(string $platform): string
    {
        return SocialChannelCatalog::label($platform);
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        $options = SocialChannelCatalog::options();
        if ($options !== []) {
            return $options;
        }

        return [
            'facebook' => 'Facebook Page & Feed',
            'instagram' => 'Instagram Business',
            'linkedin' => 'LinkedIn Company Page',
            'youtube' => 'YouTube (Long-form)',
            'telegram' => 'Telegram Channel',
        ];
    }

    /** @return array<string, string> */
    public static function schedulableOptions(): array
    {
        $options = SocialChannelCatalog::schedulableOptions();
        if ($options !== []) {
            return $options;
        }

        return static::options();
    }
}
