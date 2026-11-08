<?php

namespace App\Services\Chat;

use App\Support\LiveChatSettings;
use Illuminate\Support\Str;

class SimpleSupportChatbot
{
    public function __construct(
        protected ChatOrderLookup $orders,
        protected ChatProductLookup $products,
        protected ChatCheckoutFacts $checkoutFacts,
    ) {}

    /**
     * @return array{reply: string, checkout_url: ?string, track_url: ?string, escalate: bool, products: list<array<string, mixed>>, whatsapp_url: ?string}
     */
    public function replyFor(string $customerMessage): array
    {
        $normalized = Str::lower(trim($customerMessage));
        if ($normalized === '') {
            return $this->whatsappHandoff('Khali message — WhatsApp par team se poochein.');
        }

        if ($this->isGreeting($normalized)) {
            return $this->plain($this->helpIntro());
        }

        $reference = $this->orders->extractReference($customerMessage);
        if ($reference !== null) {
            return $this->orderTrackingReply($reference);
        }

        if ($this->isTrackingQuestion($normalized)) {
            return $this->plain(
                "Sirf apna order number ya tracking ID bhejein (misal: BTG-123456).\n\nMain Shop Pakistan database se exact status bataunga.",
            );
        }

        if ($this->isPaymentQuestion($normalized)) {
            return $this->plain($this->checkoutFacts->paymentBlock());
        }

        if ($this->isDeliveryQuestion($normalized)) {
            return $this->plain($this->checkoutFacts->deliveryBlock());
        }

        $productReply = $this->productPriceReply($customerMessage);
        if ($productReply !== null) {
            return $productReply;
        }

        return $this->whatsappHandoff(
            'Yeh topic bot ke scope se bahar hai — WhatsApp par team se rabta karein.',
        );
    }

    protected function helpIntro(): string
    {
        return implode("\n", [
            'Salam! Main sirf live database se jawab deta hoon:',
            '',
            '1) Order / tracking # → delivery status (order record)',
            '2) Product naam → price & stock (catalog)',
            '3) Delivery / payment → checkout settings',
            '',
            'Courier live scan jald add hogi — abhi order DB accurate hai.',
            'Baqi sawal: WhatsApp button.',
        ]);
    }

    /** @return array{reply: string, checkout_url: ?string, track_url: ?string, escalate: bool, products: list<array<string, mixed>>, whatsapp_url: ?string} */
    protected function orderTrackingReply(string $reference): array
    {
        $order = $this->orders->findByReference($reference);

        if (! $order) {
            return $this->plain(
                "Database mein \"{$reference}\" se koi order nahi mila.\n\nOrder # (BTG-…) ya courier tracking exactly likhein.",
                whatsapp: true,
            );
        }

        return $this->plain($this->orders->formatStatusBlock($order));
    }

    /**
     * @return array{reply: string, checkout_url: ?string, track_url: ?string, escalate: bool, products: list<array<string, mixed>>, whatsapp_url: ?string}|null
     */
    protected function productPriceReply(string $message): ?array
    {
        $normalized = Str::lower(trim($message));
        $wantsProduct = $this->matchesAny($normalized, [
            'price', 'kitna', 'kitne', 'cost', 'rate', 'stock', 'available', 'milta', 'product',
        ]);

        $resolved = $this->products->resolve($message);

        if ($resolved === null) {
            if (! $wantsProduct && strlen(trim($message)) < 4) {
                return null;
            }

            return $this->plain('Product ka exact naam likhein — main catalog database se price aur stock bataunga.');
        }

        if ($resolved['type'] === 'none') {
            $query = (string) ($resolved['query'] ?? '');

            return $this->plain("Catalog mein \"{$query}\" se koi product match nahi hua.\n\nPoora product naam copy karke bhejein.");
        }

        if ($resolved['type'] === 'multiple') {
            return $this->plain(
                $this->products->formatMultiple($resolved['products'], (string) $resolved['query']),
            );
        }

        $product = $resolved['product'];
        $reply = $this->products->formatSingle($product);

        return [
            'reply' => $reply,
            'checkout_url' => null,
            'track_url' => null,
            'escalate' => false,
            'products' => $this->products->productPayload($product),
            'whatsapp_url' => $product->in_stock
                ? null
                : LiveChatSettings::whatsappUrl('Hi, is '.$product->name.' available?'),
        ];
    }

    protected function isGreeting(string $normalized): bool
    {
        return $this->matchesAny($normalized, [
            'hello', 'hi', 'hey', 'salam', 'assalam', 'aoa', 'good morning', 'good evening',
        ]) && ! $this->matchesAny($normalized, ['price', 'track', 'order', 'shipping', 'payment', 'btg-']);
    }

    protected function isTrackingQuestion(string $normalized): bool
    {
        return $this->matchesAny($normalized, [
            'track', 'tracking', 'order status', 'delivery status', 'where is my order', 'parcel', 'kahan hai', 'kab milega',
        ]);
    }

    protected function isPaymentQuestion(string $normalized): bool
    {
        return $this->matchesAny($normalized, [
            'payment', 'pay', 'cod', 'cash on delivery', 'jazzcash', 'easypaisa', 'card', 'online pay', 'payment method',
        ]);
    }

    protected function isDeliveryQuestion(string $normalized): bool
    {
        return $this->matchesAny($normalized, [
            'shipping', 'delivery', 'dispatch', 'courier', 'kitne din', 'free shipping', 'ship', 'bhejna',
        ]);
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

    /** @return array{reply: string, checkout_url: ?string, track_url: ?string, escalate: bool, products: list<array<string, mixed>>, whatsapp_url: ?string} */
    protected function whatsappHandoff(string $reason): array
    {
        $url = LiveChatSettings::whatsappUrl('Hi Shop Pakistan, I need help: '.Str::limit(trim($reason), 120));

        return [
            'reply' => $reason."\n\nWhatsApp: ".$url,
            'checkout_url' => null,
            'track_url' => null,
            'escalate' => false,
            'products' => [],
            'whatsapp_url' => $url,
        ];
    }

    /** @return array{reply: string, checkout_url: ?string, track_url: ?string, escalate: bool, products: list<array<string, mixed>>, whatsapp_url: ?string} */
    protected function plain(string $reply, bool $whatsapp = false): array
    {
        return [
            'reply' => $reply,
            'checkout_url' => null,
            'track_url' => null,
            'escalate' => false,
            'products' => [],
            'whatsapp_url' => $whatsapp ? LiveChatSettings::whatsappUrl() : null,
        ];
    }
}
