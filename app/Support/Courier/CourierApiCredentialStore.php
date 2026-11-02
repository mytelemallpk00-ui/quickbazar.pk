<?php

namespace App\Support\Courier;

use App\Models\CourierApiSetting;

class CourierApiCredentialStore
{
    /** @return array<string, list<array{key: string, label: string, secret: bool}>> */
    public static function fieldMap(): array
    {
        return [
            'tcs' => [
                ['key' => 'client_id', 'label' => 'Client / Merchant ID', 'secret' => false],
                ['key' => 'api_key', 'label' => 'API Key / Token', 'secret' => true],
                ['key' => 'account_no', 'label' => 'Account Number', 'secret' => false],
            ],
            'leopards' => [
                ['key' => 'api_key', 'label' => 'API Key', 'secret' => true],
                ['key' => 'api_password', 'label' => 'API Password', 'secret' => true],
                ['key' => 'shipper_id', 'label' => 'Shipper ID', 'secret' => false],
            ],
            'blueex' => [
                ['key' => 'username', 'label' => 'Username', 'secret' => false],
                ['key' => 'password', 'label' => 'Password', 'secret' => true],
                ['key' => 'account_code', 'label' => 'Account Code', 'secret' => false],
            ],
            'mnp' => [
                ['key' => 'username', 'label' => 'API username', 'secret' => false],
                ['key' => 'password', 'label' => 'API password', 'secret' => true],
                ['key' => 'account_no', 'label' => 'Account number', 'secret' => false],
                ['key' => 'location_id', 'label' => 'Pickup location ID', 'secret' => false],
                ['key' => 'insert_type', 'label' => 'Insert type (optional)', 'secret' => false],
                ['key' => 'return_location', 'label' => 'Return location ID (optional)', 'secret' => false],
                ['key' => 'sub_account_id', 'label' => 'Sub-account ID (optional)', 'secret' => false],
                ['key' => 'service', 'label' => 'Service (e.g. Overnight)', 'secret' => false],
            ],
        ];
    }

    /** @return array<string, mixed> */
    public static function formState(): array
    {
        $state = [];
        foreach (CourierPlatforms::all() as $platform) {
            $stored = CourierApiSetting::forPlatform($platform)?->credentials ?? [];
            if (! is_array($stored)) {
                $stored = [];
            }
            foreach (static::fieldMap()[$platform] ?? [] as $field) {
                $key = $field['key'];
                $state["{$platform}.{$key}"] = '';
            }
            $state["{$platform}.enabled"] = CourierApiSetting::forPlatform($platform)?->enabled ?? true;
        }

        return $state;
    }

    /** @param  array<string, mixed>  $state */
    public static function saveFromForm(array $state): void
    {
        foreach (CourierPlatforms::all() as $platform) {
            $existing = CourierApiSetting::forPlatform($platform)?->credentials ?? [];
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

            CourierApiSetting::query()->updateOrCreate(
                ['platform' => $platform],
                [
                    'credentials' => $next,
                    'enabled' => (bool) ($state["{$platform}.enabled"] ?? true),
                ],
            );
        }
    }
}
