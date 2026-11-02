<?php

namespace App\Support;

use Illuminate\Support\Facades\File;

final class GoogleIndexCredentialStore
{
    public static function absolutePath(): string
    {
        return (string) config('google-indexing.service_account_path');
    }

    public static function isConfigured(): bool
    {
        $path = self::absolutePath();

        return is_file($path) && is_readable($path);
    }

    /** @return array<string, mixed>|null */
    public static function serviceAccount(): ?array
    {
        if (! self::isConfigured()) {
            return null;
        }

        $decoded = json_decode((string) file_get_contents(self::absolutePath()), true);

        return is_array($decoded) ? $decoded : null;
    }

    public static function storeFromUpload(string $absoluteUploadedPath): void
    {
        if (! is_file($absoluteUploadedPath)) {
            throw new \InvalidArgumentException('Uploaded service account file not found.');
        }

        $contents = file_get_contents($absoluteUploadedPath);
        $decoded = json_decode((string) $contents, true);
        if (! is_array($decoded) || blank($decoded['client_email'] ?? null) || blank($decoded['private_key'] ?? null)) {
            throw new \InvalidArgumentException('Invalid Google service account JSON (client_email and private_key required).');
        }

        $target = self::absolutePath();
        File::ensureDirectoryExists(dirname($target));
        File::put($target, json_encode($decoded, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
        File::chmod($target, 0600);
    }

    public static function clientEmail(): ?string
    {
        $account = self::serviceAccount();

        return is_array($account) ? ($account['client_email'] ?? null) : null;
    }
}
