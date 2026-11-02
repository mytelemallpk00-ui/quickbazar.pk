<?php

namespace App\Services\ProductImport;

class ImportPriceNormalizer
{
    public function usdToPkrRate(): float
    {
        $rate = (float) config('services.product_import.usd_to_pkr', 280);

        return $rate > 0 ? $rate : 280;
    }

    public function parse(mixed $value): float
    {
        if ($value === null || $value === '') {
            return 0.0;
        }

        if (is_int($value) || is_float($value)) {
            return (float) $value;
        }

        $string = trim((string) $value);
        if ($string === '') {
            return 0.0;
        }

        $string = str_replace([',', ' '], '', $string);
        $string = preg_replace('/[^0-9.]/', '', $string) ?? '0';

        return (float) ($string !== '' ? $string : 0);
    }

    /** @return float Price stored on product (whole PKR rupees) */
    public function toStoredPkr(float $amount, string $mode): float
    {
        $amount = max(0, $amount);

        if ($mode === 'usd') {
            $amount = $amount * $this->usdToPkrRate();
        }

        return round($amount, 0);
    }

}
