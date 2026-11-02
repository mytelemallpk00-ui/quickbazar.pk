<?php

namespace App\Support;

class BrandPalette
{
    public const PURPLE = '#5D267D';

    public const PURPLE_DARK = '#451B5C';

    public const PINK = '#E6007E';

    public const PINK_HOVER = '#BF0068';

    public const GOLD = '#F5A623';

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
}
