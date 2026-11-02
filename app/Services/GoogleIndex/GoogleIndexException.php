<?php

namespace App\Services\GoogleIndex;

use Exception;

class GoogleIndexException extends Exception
{
    public static function missingServiceAccount(): self
    {
        return new self('Google service account JSON is missing. Upload it under Social Automation → Connect accounts → Google Indexing tab.');
    }

    public static function fromApi(int $status, string $message): self
    {
        return new self("Google Indexing API (HTTP {$status}): {$message}");
    }
}
