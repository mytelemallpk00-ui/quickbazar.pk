<?php

namespace App\Support\Social;

use App\Models\SocialMarketingChannel;
use Illuminate\Support\Facades\Schema;

final class SocialChannelCatalog
{
    /** @var list<SocialMarketingChannel>|null */
    private static ?array $cache = null;

    public static function flushCache(): void
    {
        self::$cache = null;
    }

    /** @return list<SocialMarketingChannel> */
    public static function allRecords(): array
    {
        if (self::$cache !== null) {
            return self::$cache;
        }

        if (! Schema::hasTable('social_marketing_channels')) {
            self::$cache = [];

            return self::$cache;
        }

        self::$cache = SocialMarketingChannel::query()
            ->orderBy('sort_order')
            ->orderBy('label')
            ->get()
            ->all();

        return self::$cache;
    }

    /** @return list<string> */
    public static function allKeys(): array
    {
        return array_map(fn (SocialMarketingChannel $c) => $c->key, self::enabled());
    }

    /** @return list<SocialMarketingChannel> */
    public static function enabled(): array
    {
        return array_values(array_filter(self::allRecords(), fn (SocialMarketingChannel $c) => $c->is_enabled));
    }

    /** @return list<string> */
    public static function schedulableKeys(): array
    {
        $publishable = config('social.auto_publishers', []);
        if (! is_array($publishable)) {
            $publishable = [];
        }

        $keys = [];
        foreach (self::enabled() as $channel) {
            if (! $channel->supports_scheduling) {
                continue;
            }
            if (! in_array($channel->key, $publishable, true)) {
                continue;
            }
            $keys[] = $channel->key;
        }

        return $keys;
    }

    public static function find(string $key): ?SocialMarketingChannel
    {
        $key = strtolower(trim($key));
        foreach (self::allRecords() as $channel) {
            if ($channel->key === $key) {
                return $channel;
            }
        }

        return null;
    }

    public static function label(string $key): string
    {
        return self::find($key)?->label ?? ucfirst(str_replace('_', ' ', $key));
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        $out = [];
        foreach (self::enabled() as $channel) {
            $out[$channel->key] = $channel->label;
        }

        return $out;
    }

    /** @return array<string, string> */
    public static function schedulableOptions(): array
    {
        $keys = self::schedulableKeys();
        $all = self::options();

        return array_intersect_key($all, array_flip($keys));
    }

    /** @return array<string, list<array{key: string, label: string, secret: bool, required?: bool}>> */
    public static function fieldMap(): array
    {
        $map = [];
        foreach (self::allRecords() as $channel) {
            $map[$channel->key] = $channel->normalizedCredentialFields();
        }

        return $map;
    }

    public static function guideFor(string $key): string
    {
        return (string) (self::find($key)?->guide ?? '');
    }
}
