<?php

namespace App\Support\Courier;

class PakistanZoneRates
{
    public static function zoneForCity(string $city): string
    {
        $normalized = strtolower(trim($city));

        $major = ['lahore', 'karachi', 'islamabad', 'rawalpindi', 'multan'];
        foreach ($major as $name) {
            if (str_contains($normalized, $name)) {
                return 'major-metro';
            }
        }

        if (str_contains($normalized, 'faisalabad') || str_contains($normalized, 'multan')) {
            return 'faisalabad-multan';
        }

        if (str_contains($normalized, 'quetta') || str_contains($normalized, 'peshawar')) {
            return 'quetta-peshawar';
        }

        return 'remote';
    }

    /** @return array{price: float, eta: string} */
    public static function quote(string $city, string $courier): array
    {
        $zone = static::zoneForCity($city);
        $base = match ($zone) {
            'major-metro' => 200.0,
            'faisalabad-multan' => 220.0,
            'quetta-peshawar' => 250.0,
            default => 300.0,
        };

        $courierAdj = match ($courier) {
            'leopards' => 0.0,
            'blueex' => 15.0,
            'mnp' => 12.0,
            default => 10.0,
        };

        $eta = match ($zone) {
            'major-metro' => '1–2 business days',
            'faisalabad-multan' => '2–3 business days',
            'quetta-peshawar' => '3–4 business days',
            default => '4–5 business days',
        };

        return [
            'price' => round($base + $courierAdj, 2),
            'eta' => $eta,
        ];
    }
}
