<?php

namespace App\Support;

class BrandPalette
{
    public const PURPLE = '#0054F0';

    public const PURPLE_DARK = '#0039B5';

    public const PINK = '#F02424';

    public const PINK_HOVER = '#CC1212';

    public const GOLD = '#22A34A';

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
            'purple' => ['#5d267d', '#0b1220', '#0b132b', '#0e7c6b', '#0b5e52'],
            'purpleDark' => ['#451b5c', '#111827', '#1c2541', '#08463d'],
            'pink' => ['#e6007e', '#f97316', '#d4af37'],
            'pinkHover' => ['#bf0068', '#ea580c', '#c4a033'],
            'amber' => ['#f5a623', '#fb923c', '#f4e8c1'],
        ];
    }
}
