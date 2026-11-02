<?php

namespace App\Support\Social;

use App\Models\SocialApiSetting;

class SocialApiCredentialStore
{
    /** @return array<string, list<array{key: string, label: string, secret: bool, required?: bool}>> */
    public static function fieldMap(): array
    {
        $fromCatalog = SocialChannelCatalog::fieldMap();
        if ($fromCatalog !== []) {
            return $fromCatalog;
        }

        return self::legacyFieldMap();
    }

    /** @return array<string, list<array{key: string, label: string, secret: bool}>> */
    protected static function legacyFieldMap(): array
    {
        return [
            'facebook' => [
                ['key' => 'app_id', 'label' => 'App ID', 'secret' => false],
                ['key' => 'app_secret', 'label' => 'App Secret', 'secret' => true],
                ['key' => 'page_id', 'label' => 'Page ID', 'secret' => false],
                ['key' => 'access_token', 'label' => 'Page Access Token', 'secret' => true],
            ],
            'instagram' => [
                ['key' => 'business_account_id', 'label' => 'Business Account ID', 'secret' => false],
                ['key' => 'access_token', 'label' => 'Access Token', 'secret' => true],
            ],
            'linkedin' => [
                ['key' => 'client_id', 'label' => 'Client ID', 'secret' => false],
                ['key' => 'client_secret', 'label' => 'Client Secret', 'secret' => true],
                ['key' => 'access_token', 'label' => 'Access Token', 'secret' => true],
                ['key' => 'organization_urn', 'label' => 'Organization URN', 'secret' => false],
            ],
            'youtube' => [
                ['key' => 'client_id', 'label' => 'Client ID', 'secret' => false],
                ['key' => 'client_secret', 'label' => 'Client Secret', 'secret' => true],
                ['key' => 'refresh_token', 'label' => 'Refresh Token', 'secret' => true],
                ['key' => 'access_token', 'label' => 'Access Token (optional)', 'secret' => true],
                ['key' => 'channel_id', 'label' => 'Channel ID', 'secret' => false],
            ],
            'telegram' => [
                ['key' => 'bot_token', 'label' => 'Bot Token', 'secret' => true],
                ['key' => 'chat_id', 'label' => 'Chat / Channel ID', 'secret' => false],
            ],
        ];
    }

    /** @return array<string, mixed> */
    public static function formState(): array
    {
        $state = [];
        foreach (SocialPlatforms::all() as $platform) {
            $stored = SocialApiSetting::forPlatform($platform)?->credentials ?? [];
            if (! is_array($stored)) {
                $stored = [];
            }
            foreach (static::fieldMap()[$platform] ?? [] as $field) {
                $key = $field['key'];
                $state["{$platform}.{$key}"] = '';
                $state["{$platform}.{$key}_masked"] = static::mask((string) ($stored[$key] ?? ''));
            }
        }

        return $state;
    }

    /** @param  array<string, mixed>  $state */
    public static function saveFromForm(array $state): void
    {
        foreach (SocialPlatforms::all() as $platform) {
            $existing = SocialApiSetting::forPlatform($platform)?->credentials ?? [];
            if (! is_array($existing)) {
                $existing = [];
            }

            $next = $existing;
            foreach (static::fieldMap()[$platform] ?? [] as $field) {
                $key = $field['key'];
                $inputKey = "{$platform}.{$key}";
                $value = trim((string) ($state[$inputKey] ?? ''));
                if ($value !== '') {
                    $next[$key] = $value;
                }
            }

            if ($next !== []) {
                SocialApiSetting::query()->updateOrCreate(
                    ['platform' => $platform],
                    ['credentials' => $next],
                );
            }
        }
    }

    public static function mask(string $value): string
    {
        if ($value === '') {
            return '';
        }

        if (strlen($value) <= 8) {
            return str_repeat('•', strlen($value));
        }

        return substr($value, 0, 4).str_repeat('•', max(4, strlen($value) - 8)).substr($value, -4);
    }
}
