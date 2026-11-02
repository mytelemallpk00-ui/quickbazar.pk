<?php

namespace App\Support\Social;

use App\Models\SocialApiSetting;

class SocialCredentialResolver
{
    /** @return array<string, mixed> */
    public function forPlatform(string $platform): array
    {
        $platform = strtolower(trim($platform));
        $fromEnv = config("social.platforms.{$platform}", []);
        if (! is_array($fromEnv)) {
            $fromEnv = [];
        }

        $fromEnv = array_filter($fromEnv, fn ($value) => filled($value));

        $record = SocialApiSetting::forPlatform($platform);
        $fromDb = $record?->credentials ?? [];
        if (! is_array($fromDb)) {
            $fromDb = [];
        }

        $fromDb = array_filter($fromDb, fn ($value) => filled($value));

        return array_merge($fromEnv, $fromDb);
    }

    public function get(string $platform, string $key, ?string $default = null): ?string
    {
        $all = $this->forPlatform($platform);
        $value = $all[$key] ?? $default;

        return is_string($value) ? trim($value) : (is_scalar($value) ? (string) $value : $default);
    }

    /**
     * @return array{ready: list<string>, missing: list<string>, ready_count: int, total: int}
     */
    public function connectionSummary(): array
    {
        $ready = [];
        $missing = [];

        foreach (SocialPlatforms::all() as $platform) {
            if ($this->isConfigured($platform)) {
                $ready[] = $platform;
            } else {
                $missing[] = $platform;
            }
        }

        return [
            'ready' => $ready,
            'missing' => $missing,
            'ready_count' => count($ready),
            'total' => count(SocialPlatforms::all()),
        ];
    }

    public function isConfigured(string $platform): bool
    {
        $platform = strtolower(trim($platform));
        $fields = SocialApiCredentialStore::fieldMap()[$platform] ?? [];
        if ($fields === []) {
            return false;
        }

        $requiredKeys = [];
        foreach ($fields as $field) {
            if ($field['required'] ?? true) {
                $requiredKeys[] = $field['key'];
            }
        }

        if ($requiredKeys === []) {
            foreach ($fields as $field) {
                if (filled($this->get($platform, $field['key']))) {
                    return true;
                }
            }

            return false;
        }

        foreach ($requiredKeys as $key) {
            if (! filled($this->get($platform, $key))) {
                return false;
            }
        }

        return true;
    }
}
