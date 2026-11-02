<?php

namespace App\Services\Chat;

use App\Support\CheckoutSettings;

class ChatCheckoutFacts
{
    public function paymentBlock(): string
    {
        $checkout = CheckoutSettings::all();
        $methods = array_values(array_filter(
            $checkout['payment_methods'] ?? [],
            fn ($m) => ! empty($m['enabled']),
        ));

        $lines = [
            'Payment methods (checkout settings — live):',
            '',
        ];

        if ($methods === []) {
            $lines[] = '• (No enabled methods in admin — default: Cash on Delivery where offered on checkout.)';
        } else {
            foreach ($methods as $method) {
                $label = trim((string) ($method['label'] ?? 'Payment'));
                $id = trim((string) ($method['id'] ?? ''));
                $lines[] = '• '.$label.($id !== '' ? " [{$id}]" : '');
            }
        }

        $currency = strtoupper((string) ($checkout['currency'] ?? 'PKR'));
        $lines[] = '';
        $lines[] = 'Store currency: '.$currency;
        $lines[] = 'Source: Admin checkout settings (database).';

        return implode("\n", $lines);
    }

    public function deliveryBlock(): string
    {
        $checkout = CheckoutSettings::all();
        $threshold = (float) ($checkout['free_shipping_threshold'] ?? 0);
        $methods = array_values(array_filter(
            $checkout['shipping_methods'] ?? [],
            fn ($m) => ! empty($m['enabled']),
        ));

        $lines = [
            'Delivery / shipping (checkout settings — live):',
            '',
        ];

        if ($methods === []) {
            $lines[] = '• (No shipping methods enabled in admin.)';
        } else {
            foreach ($methods as $method) {
                $label = trim((string) ($method['label'] ?? 'Standard'));
                $desc = trim((string) ($method['description'] ?? ''));
                $price = (float) ($method['price'] ?? 0);
                $priceText = $price <= 0 ? 'Rs 0' : 'Rs '.number_format($price, 0);
                $lines[] = "• {$label} — {$priceText}".($desc !== '' ? " — {$desc}" : '');
            }
        }

        if ($threshold > 0) {
            $lines[] = '';
            $lines[] = 'Free shipping threshold: Rs '.number_format($threshold, 0).' (subtotal par apply hota hai jahan configured ho).';
        }

        $lines[] = '';
        $lines[] = 'Order-specific tracking: apna order / tracking number bhejein.';
        $lines[] = 'Source: Admin checkout settings (database).';

        return implode("\n", $lines);
    }
}
