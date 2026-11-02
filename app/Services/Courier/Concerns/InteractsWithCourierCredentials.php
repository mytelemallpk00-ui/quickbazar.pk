<?php

namespace App\Services\Courier\Concerns;

use App\Support\Courier\CourierCredentialResolver;

trait InteractsWithCourierCredentials
{
    protected function courierCredentials(): CourierCredentialResolver
    {
        return app(CourierCredentialResolver::class);
    }

    protected function credential(string $key, ?string $default = null): ?string
    {
        return $this->courierCredentials()->get($this->platform(), $key, $default);
    }
}
