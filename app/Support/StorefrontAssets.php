<?php

namespace App\Support;

final class StorefrontAssets
{
    /** @var array<string, mixed>|null */
    private static ?array $manifest = null;

    public static function hasCompiledStyles(): bool
    {
        return self::compiledStylesheet() !== null;
    }

    public static function compiledStylesheet(): ?string
    {
        $href = self::manifestEntry('resources/css/storefront.css');

        return $href !== null ? asset('build/'.$href) : null;
    }

    private static function manifestEntry(string $key): ?string
    {
        $manifest = self::manifest();

        if ($manifest === null) {
            return null;
        }

        $entry = $manifest[$key] ?? null;

        if (! is_array($entry)) {
            return null;
        }

        $file = $entry['file'] ?? null;

        return is_string($file) && $file !== '' ? $file : null;
    }

    /** @return array<string, mixed>|null */
    private static function manifest(): ?array
    {
        if (self::$manifest !== null) {
            return self::$manifest;
        }

        $path = public_path('build/manifest.json');
        if (! is_file($path)) {
            self::$manifest = null;

            return null;
        }

        $decoded = json_decode((string) file_get_contents($path), true);
        self::$manifest = is_array($decoded) ? $decoded : null;

        return self::$manifest;
    }
}
