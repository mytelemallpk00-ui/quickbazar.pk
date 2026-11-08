<?php

namespace App\Services\Chat;

use App\Models\Product;
use App\Support\BundleDealsCatalog;
use App\Support\CheckoutSettings;
use App\Support\LiveChatSettings;
use App\Support\SiteSettings;
use Illuminate\Support\Str;

class SiteKnowledgeChat
{
    /**
     * Answers from live site settings, checkout config, and product FAQs.
     *
     * @return array{reply: string, checkout_url: ?string, track_url: ?string, escalate: bool, products: list<array<string, mixed>>}|null
     */
    public function tryReply(string $customerMessage): ?array
    {
        $normalized = Str::lower(trim($customerMessage));
        if ($normalized === '') {
            return null;
        }

        if ($this->matchesAny($normalized, [
            'bundle', 'bundles', 'combo', 'pack', 'deal', 'deals', 'offer', 'offers',
            'exclusive', 'saath', 'set', 'frequently bought',
        ])) {
            return $this->bundleDealsReply();
        }

        if ($this->matchesAny($normalized, [
            'shipping', 'delivery', 'courier', 'dispatch', 'kitne din', 'kab milega',
            'parcel', 'tcs', 'leopard', 'm&p', 'ship',
        ])) {
            return $this->shippingReply();
        }

        if ($this->matchesAny($normalized, [
            'cod', 'cash on delivery', 'payment method', 'jazzcash', 'easypaisa', 'pay',
            'online payment', 'card',
        ])) {
            return $this->paymentReply();
        }

        if ($this->matchesAny($normalized, [
            'return', 'refund', 'exchange', 'wapsi', 'replacement',
        ])) {
            return $this->returnsReply();
        }

        if ($this->matchesAny($normalized, [
            'contact', 'phone', 'whatsapp', 'call', 'email', 'rabta', 'number', 'helpline',
        ])) {
            return $this->contactReply();
        }

        $faqReply = $this->matchProductFaq($normalized);
        if ($faqReply !== null) {
            return $faqReply;
        }

        return null;
    }

    /** @return array{reply: string, checkout_url: ?string, track_url: ?string, escalate: bool, products: list<array<string, mixed>>} */
    protected function bundleDealsReply(): array
    {
        $offersUrl = route('exclusive-offers');
        $deals = BundleDealsCatalog::activeDeals(5);

        if ($deals->isEmpty()) {
            return $this->plain(
                "Bundle deals jald aa rahe hain. Abhi collections browse karein:\n".route('collections'),
            );
        }

        $lines = [
            'Live bundle deals (site se):',
            '',
            $offersUrl,
            '',
        ];

        foreach ($deals as $index => $deal) {
            $n = $index + 1;
            $lines[] = "{$n}. {$deal['title']} — {$deal['discountedFormatted']} (save {$deal['savingsFormatted']})";
            $lines[] = '   '.$deal['url'];
        }

        $lines[] = '';
        $lines[] = 'Product page par scroll karein — "Frequently bought together" se bundle cart mein add ho sakta hai.';

        return $this->plain(implode("\n", $lines));
    }

    /** @return array{reply: string, checkout_url: ?string, track_url: ?string, escalate: bool, products: list<array<string, mixed>>} */
    protected function shippingReply(): array
    {
        $checkout = CheckoutSettings::all();
        $threshold = (float) ($checkout['free_shipping_threshold'] ?? 0);
        $methods = array_values(array_filter(
            $checkout['shipping_methods'] ?? [],
            fn ($m) => ! empty($m['enabled']),
        ));

        $lines = ['Delivery (site settings se live):', ''];

        foreach ($methods as $method) {
            $label = trim((string) ($method['label'] ?? 'Standard'));
            $desc = trim((string) ($method['description'] ?? ''));
            $price = (float) ($method['price'] ?? 0);
            $priceText = $price <= 0 ? 'Free' : 'Rs '.number_format($price, 0);
            $lines[] = "• {$label} — {$priceText}".($desc !== '' ? " ({$desc})" : '');
        }

        if ($threshold > 0) {
            $lines[] = '';
            $lines[] = 'Free shipping threshold: Rs '.number_format($threshold, 0).' (jab cart is se upar ho).';
        }

        $lines[] = '';
        $lines[] = 'Checkout: '.route('checkout');
        $lines[] = 'City ke hisaab se rate confirm karne ke liye checkout par address add karein.';

        return $this->plain(implode("\n", $lines));
    }

    /** @return array{reply: string, checkout_url: ?string, track_url: ?string, escalate: bool, products: list<array<string, mixed>>} */
    protected function paymentReply(): array
    {
        $checkout = CheckoutSettings::all();
        $methods = array_values(array_filter(
            $checkout['payment_methods'] ?? [],
            fn ($m) => ! empty($m['enabled']),
        ));

        $lines = ['Payment options (checkout par jo enabled hain):', ''];

        if ($methods === []) {
            $lines[] = '• Cash on Delivery (COD) — typical for Pakistan orders.';
        } else {
            foreach ($methods as $method) {
                $lines[] = '• '.trim((string) ($method['label'] ?? 'Payment'));
            }
        }

        $lines[] = '';
        $lines[] = 'Order place karne ke liye: '.route('checkout');

        return $this->plain(implode("\n", $lines), route('checkout'));
    }

    /** @return array{reply: string, checkout_url: ?string, track_url: ?string, escalate: bool, products: list<array<string, mixed>>} */
    protected function returnsReply(): array
    {
        $url = route('returns');

        return $this->plain(
            "Returns & refunds ki policy yahan hai (site page):\n{$url}\n\nDamaged ya galat item par support team help karti hai — WhatsApp button se bhi rabta kar sakte hain.",
        );
    }

    /** @return array{reply: string, checkout_url: ?string, track_url: ?string, escalate: bool, products: list<array<string, mixed>>} */
    protected function contactReply(): array
    {
        $phone = trim((string) SiteSettings::get('support_phone', ''));
        $email = trim((string) SiteSettings::get('support_email', SiteSettings::get('contact_email', '')));
        $wa = LiveChatSettings::whatsappUrl();

        $lines = ['Shop Pakistan support:', ''];
        if ($phone !== '') {
            $lines[] = "Phone: {$phone}";
        }
        $lines[] = "WhatsApp: {$wa}";
        if ($email !== '') {
            $lines[] = "Email: {$email}";
        }
        $lines[] = '';
        $lines[] = 'Live chat ya WhatsApp — dono se ek hi message mein jawab milna chahiye.';

        return $this->plain(implode("\n", $lines));
    }

    /**
     * @return array{reply: string, checkout_url: ?string, track_url: ?string, escalate: bool, products: list<array<string, mixed>>}|null
     */
    protected function matchProductFaq(string $normalized): ?array
    {
        $products = Product::query()
            ->whereNotNull('faq_items')
            ->limit(80)
            ->get(['id', 'name', 'slug', 'faq_items']);

        $bestScore = 0;
        $best = null;

        foreach ($products as $product) {
            $items = $product->faq_items;
            if (! is_array($items)) {
                continue;
            }
            foreach ($items as $item) {
                if (! is_array($item)) {
                    continue;
                }
                $question = Str::lower(trim((string) ($item['question'] ?? $item['q'] ?? '')));
                $answer = trim((string) ($item['answer'] ?? $item['a'] ?? ''));
                if ($question === '' || $answer === '') {
                    continue;
                }
                $score = $this->overlapScore($normalized, $question);
                if ($score > $bestScore) {
                    $bestScore = $score;
                    $best = [$product, $question, $answer];
                }
            }
        }

        if ($best === null || $bestScore < 2) {
            return null;
        }

        [$product, , $answer] = $best;
        $url = route('product.show', $product);

        return $this->plain(
            "{$product->name} — FAQ se:\n\n".Str::limit(strip_tags($answer), 500)."\n\nDetail: {$url}",
        );
    }

    protected function overlapScore(string $message, string $candidate): int
    {
        $msgWords = array_filter(preg_split('/\s+/u', $message) ?: [], fn ($w) => strlen($w) >= 3);
        $score = 0;
        foreach ($msgWords as $word) {
            if (str_contains($candidate, $word)) {
                $score++;
            }
        }

        return $score;
    }

    /** @param  list<string>  $needles */
    protected function matchesAny(string $haystack, array $needles): bool
    {
        foreach ($needles as $needle) {
            if (str_contains($haystack, Str::lower($needle))) {
                return true;
            }
        }

        return false;
    }

    /** @return array{reply: string, checkout_url: ?string, track_url: ?string, escalate: bool, products: list<array<string, mixed>>} */
    protected function plain(string $reply, ?string $checkoutUrl = null): array
    {
        return [
            'reply' => $reply,
            'checkout_url' => $checkoutUrl,
            'track_url' => null,
            'escalate' => false,
            'products' => [],
        ];
    }
}
