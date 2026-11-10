<?php

namespace App\Services\Courier;

use App\Services\Courier\Contracts\CourierClient;
use App\Support\CheckoutSettings;
use App\Support\Courier\CourierPlatforms;

class CourierRateService
{
    public function __construct(
        protected TcsCourierClient $tcs,
        protected LeopardsCourierClient $leopards,
        protected BlueExCourierClient $blueex,
        protected MnpCourierClient $mnp,
    ) {}

    public function estimateWeightKg(int $itemCount): float
    {
        $per = (float) config('courier.weight_per_item_kg', 0.35);
        $base = (float) config('courier.default_weight_kg', 0.5);

        return max($base, $itemCount * $per);
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function shippingMethodsFor(string $city, float $discountedSubtotal, float $weightKg, bool $cod = true): array
    {
        $threshold = (float) CheckoutSettings::all()['free_shipping_threshold'];
        $methods = [];

        if (config('courier.smart_shipping_enabled', false)) {
            foreach ($this->clients() as $client) {
                $platform = $client->platform();
                $quote = $client->quote($city, $weightKg, $cod ? $discountedSubtotal : 0);
                if ($quote === null) {
                    continue;
                }

                $price = (float) $quote['price'];
                if ($discountedSubtotal >= $threshold) {
                    $price = 0;
                }

                $methods[] = [
                    'id' => CourierPlatforms::methodId($platform),
                    'label' => CourierPlatforms::label($platform),
                    'description' => $quote['eta'].' · '.($quote['source'] === 'api' ? 'Live rate' : 'Estimated rate'),
                    'price' => $price,
                    'free_over_threshold' => true,
                    'enabled' => true,
                    'courier' => $platform,
                    'eta' => $quote['eta'],
                    'source' => $quote['source'],
                ];
            }
        }

        foreach (CheckoutSettings::all()['shipping_methods'] as $legacy) {
            if (! ($legacy['enabled'] ?? false)) {
                continue;
            }
            $id = (string) ($legacy['id'] ?? '');
            if ($id === '') {
                continue;
            }
            $price = CheckoutSettings::shippingPriceFor($id, $discountedSubtotal);
            $methods[] = [
                'id' => $id,
                'label' => (string) ($legacy['label'] ?? $id),
                'description' => (string) ($legacy['description'] ?? ''),
                'price' => $price,
                'free_over_threshold' => (bool) ($legacy['free_over_threshold'] ?? false),
                'enabled' => true,
                'courier' => null,
                'eta' => null,
                'source' => 'checkout_settings',
            ];
        }

        if ($methods === []) {
            return CheckoutSettings::forStorefront()['shipping_methods'] ?? [];
        }

        usort($methods, fn (array $a, array $b): int => ((float) $a['price']) <=> ((float) $b['price']));

        return $methods;
    }

    public function priceForMethod(string $methodId, string $city, float $discountedSubtotal, float $weightKg): float
    {
        foreach ($this->shippingMethodsFor($city, $discountedSubtotal, $weightKg) as $method) {
            if (($method['id'] ?? '') === $methodId) {
                return (float) ($method['price'] ?? 0);
            }
        }

        return CheckoutSettings::shippingPriceFor($methodId, $discountedSubtotal);
    }

    public function isAllowedMethod(string $methodId, string $city, float $discountedSubtotal, float $weightKg): bool
    {
        foreach ($this->shippingMethodsFor($city, $discountedSubtotal, $weightKg) as $method) {
            if (($method['id'] ?? '') === $methodId) {
                return true;
            }
        }

        return false;
    }

    /** @return array{courier_code: ?string, courier_label: ?string} */
    public function courierMetaForMethod(string $methodId): array
    {
        $platform = CourierPlatforms::platformFromMethodId($methodId);
        if ($platform === null) {
            return ['courier_code' => null, 'courier_label' => null];
        }

        return [
            'courier_code' => $platform,
            'courier_label' => CourierPlatforms::label($platform),
        ];
    }

    /** @return list<CourierClient> */
    protected function clients(): array
    {
        return [$this->tcs, $this->leopards, $this->blueex, $this->mnp];
    }

    public function clientForPlatform(string $platform): ?CourierClient
    {
        return match ($platform) {
            'tcs' => $this->tcs,
            'leopards' => $this->leopards,
            'blueex' => $this->blueex,
            'mnp' => $this->mnp,
            default => null,
        };
    }
}
