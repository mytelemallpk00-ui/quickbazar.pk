<?php

namespace App\Support;

use Carbon\Carbon;

class DeliveryEstimate
{
    /** @return array{min: int, max: int} */
    public static function parseBusinessDayRange(string $description): array
    {
        $description = strtolower($description);
        if (preg_match('/next\s*day|overnight|1\s*day/', $description)) {
            return ['min' => 1, 'max' => 2];
        }
        if (preg_match('/(\d+)\s*[-–—to]+\s*(\d+)/', $description, $m)) {
            return ['min' => max(1, (int) $m[1]), 'max' => max(1, (int) $m[2])];
        }
        if (preg_match('/(\d+)/', $description, $m)) {
            $n = max(1, (int) $m[1]);

            return ['min' => $n, 'max' => $n];
        }

        return ['min' => 2, 'max' => 3];
    }

    public static function regionDayOffset(string $region): int
    {
        return match ($region) {
            'faisalabad-multan' => 1,
            'quetta-peshawar' => 2,
            'remote' => 3,
            default => 0,
        };
    }

    /**
     * @param  array<string, mixed>  $shippingMethod
     * @return array<string, mixed>
     */
    public static function timelineForMethod(array $shippingMethod, string $region = 'major-metro', ?Carbon $orderDate = null): array
    {
        $orderDate = ($orderDate ?? Carbon::now())->copy()->startOfDay();
        $range = static::parseBusinessDayRange((string) ($shippingMethod['description'] ?? ''));
        $regionExtra = static::regionDayOffset($region);

        $processed = static::addBusinessDays($orderDate, 1);
        $shipped = static::addBusinessDays($orderDate, 1);

        $deliverMin = static::addBusinessDays($orderDate, $range['min'] + 1 + $regionExtra);
        $deliverMax = static::addBusinessDays($orderDate, $range['max'] + 1 + $regionExtra);

        return [
            'ordered' => [
                'label' => 'Today',
                'date' => $orderDate->copy(),
            ],
            'processed' => [
                'label' => static::formatShortDate($processed),
                'date' => $processed,
            ],
            'shipped' => [
                'label' => static::formatShortDate($shipped),
                'date' => $shipped,
            ],
            'delivered' => [
                'label' => static::formatDeliveryWindow($deliverMin, $deliverMax),
                'date_min' => $deliverMin,
                'date_max' => $deliverMax,
            ],
            'business_days' => $range,
            'region_offset' => $regionExtra,
        ];
    }

    public static function formatShortDate(Carbon $date): string
    {
        if ($date->isToday()) {
            return 'Today';
        }
        if ($date->isTomorrow()) {
            return 'Tomorrow';
        }

        return $date->format('M j');
    }

    public static function formatDeliveryWindow(Carbon $min, Carbon $max): string
    {
        if ($min->isSameDay($max)) {
            return static::formatShortDate($min);
        }

        if ($min->month === $max->month) {
            return $min->format('M j').' – '.$max->format('M j');
        }

        return $min->format('M j').' – '.$max->format('M j');
    }

    public static function addBusinessDays(Carbon $start, int $days): Carbon
    {
        $date = $start->copy();
        if ($days <= 0) {
            return $date;
        }
        $added = 0;
        while ($added < $days) {
            $date->addDay();
            if (! $date->isWeekend()) {
                $added++;
            }
        }

        return $date;
    }

    /** @param  list<array<string, mixed>>  $shippingMethods */
    public static function courierLabel(array $shippingMethods): string
    {
        $labels = array_values(array_filter(array_map(
            fn (array $m): string => trim((string) ($m['label'] ?? '')),
            $shippingMethods,
        )));

        if ($labels === []) {
            return 'TCS & Leopards Courier Service';
        }

        if (count($labels) === 1) {
            return $labels[0];
        }

        return implode(' & ', array_slice($labels, 0, 2));
    }

    /** @param  list<array<string, mixed>>  $paymentMethods */
    public static function paymentSummary(array $paymentMethods): string
    {
        $parts = array_values(array_filter(array_map(function (array $m): string {
            return trim((string) ($m['short_label'] ?? $m['label'] ?? ''));
        }, $paymentMethods)));

        return $parts !== [] ? implode(', ', $parts) : 'Cash on Delivery';
    }

    public static function codEnabled(array $paymentMethods): bool
    {
        foreach ($paymentMethods as $method) {
            if (($method['id'] ?? '') === 'cod' && ($method['enabled'] ?? true)) {
                return true;
            }
        }

        return false;
    }
}
