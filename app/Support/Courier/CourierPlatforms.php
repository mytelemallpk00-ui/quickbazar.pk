<?php

namespace App\Support\Courier;

class CourierPlatforms
{
    /** @return list<string> */
    public static function all(): array
    {
        return ['tcs', 'leopards', 'blueex', 'mnp'];
    }

    public static function label(string $platform): string
    {
        return match ($platform) {
            'tcs' => 'TCS',
            'leopards' => 'Leopards Courier',
            'blueex' => 'BlueEx',
            'mnp' => 'M&P Courier',
            default => ucfirst($platform),
        };
    }

    public static function methodId(string $platform): string
    {
        return 'courier:'.$platform;
    }

    public static function platformFromMethodId(string $methodId): ?string
    {
        if (! str_starts_with($methodId, 'courier:')) {
            return null;
        }

        $platform = substr($methodId, strlen('courier:'));

        return in_array($platform, static::all(), true) ? $platform : null;
    }
}
