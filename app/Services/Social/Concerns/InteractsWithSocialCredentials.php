<?php

namespace App\Services\Social\Concerns;

use App\Support\Social\SocialCredentialResolver;

trait InteractsWithSocialCredentials
{
    abstract public function platform(): string;

    protected function credentials(): SocialCredentialResolver
    {
        return app(SocialCredentialResolver::class);
    }

    protected function cred(string $key, ?string $default = null): ?string
    {
        return $this->credentials()->get($this->platform(), $key, $default);
    }

    protected function ensureCredential(string $key, string $label): string
    {
        $value = $this->cred($key);
        if (! filled($value)) {
            throw new \RuntimeException("Missing {$label} for {$this->platform()}. Add it under Social Automation → API Settings or .env.");
        }

        return $value;
    }
}
