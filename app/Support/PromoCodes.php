<?php

namespace App\Support;

class PromoCodes
{
    /** @return array{percent: float, label: string}|null */
    public static function resolve(?string $code): ?array
    {
        $code = strtoupper(trim((string) $code));
        if ($code === '') {
            return null;
        }

        return match ($code) {
            'BEAUTAGE15', 'SAVE15' => ['percent' => 15.0, 'label' => $code],
            'WELCOME' => ['percent' => 10.0, 'label' => $code],
            default => null,
        };
    }
}
