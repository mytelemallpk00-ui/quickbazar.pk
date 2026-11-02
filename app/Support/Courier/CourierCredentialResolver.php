<?php

namespace App\Support\Courier;

use App\Models\CourierApiSetting;

class CourierCredentialResolver
{
    /** @return array<string, mixed> */
    public function forPlatform(string $platform): array
    {
        $platform = strtolower(trim($platform));
        $fromEnv = config("courier.platforms.{$platform}", []);
        if (! is_array($fromEnv)) {
            $fromEnv = [];
        }

        $record = CourierApiSetting::forPlatform($platform);
        if ($record && ! $record->enabled) {
            return [];
        }

        $fromDb = $record?->credentials ?? [];
        if (! is_array($fromDb)) {
            $fromDb = [];
        }

        return array_merge(
            array_filter($fromEnv, fn ($v) => filled($v)),
            array_filter($fromDb, fn ($v) => filled($v)),
        );
    }

    public function get(string $platform, string $key, ?string $default = null): ?string
    {
        $all = $this->forPlatform($platform);
        $value = $all[$key] ?? $default;

        return is_string($value) ? trim($value) : (is_scalar($value) ? (string) $value : $default);
    }

    public function isConfigured(string $platform): bool
    {
        return match ($platform) {
            'tcs' => filled($this->get($platform, 'api_key')) && filled($this->get($platform, 'client_id')),
            'leopards' => filled($this->get($platform, 'api_key')) && filled($this->get($platform, 'api_password')),
            'blueex' => filled($this->get($platform, 'username')) && filled($this->get($platform, 'password')),
            'mnp' => filled($this->get($platform, 'username'))
                && filled($this->get($platform, 'password'))
                && filled($this->get($platform, 'account_no'))
                && filled($this->get($platform, 'location_id')),
            default => false,
        };
    }
}
