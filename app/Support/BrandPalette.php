<?php

namespace App\Support;

class BrandPalette
{
    public const PURPLE = '#0B1220';

    public const PURPLE_DARK = '#111827';

    public const PINK = '#F97316';

    public const PINK_HOVER = '#EA580C';

    public const GOLD = '#FB923C';

    /** @return array<string, string> */
    public static function themeDefaults(): array
    {
        return [
            'purple' => self::PURPLE,
            'purpleDark' => self::PURPLE_DARK,
            'pink' => self::PINK,
            'pinkHover' => self::PINK_HOVER,
            'amber' => self::GOLD,
            'gold' => self::GOLD,
        ];
    }

    /** @return array<string, list<string>> */
    public static function legacyColors(): array
    {
        return [
            'purple' => ['#5d267d'],
            'purpleDark' => ['#451b5c'],
            'pink' => ['#e6007e'],
            'pinkHover' => ['#bf0068'],
            'amber' => ['#f5a623'],
        ];
    }
}
