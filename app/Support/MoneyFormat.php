<?php

namespace App\Support;

class MoneyFormat
{
    public static function pkr(float|int|string|null $amount): string
    {
        $value = (float) ($amount ?? 0);

        return 'Rs. '.number_format($value, 0, '.', ',');
    }
}
