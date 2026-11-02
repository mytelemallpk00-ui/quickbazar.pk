<?php

namespace App\Support;

use App\Models\Product;
use Illuminate\Support\Str;

class ProductFaqGenerator
{
    /**
     * @return list<array{question: string, answer: string}>
     */
    public static function forProduct(Product $product): array
    {
        return self::build(
            name: (string) $product->name,
            category: (string) ($product->category ?: 'General'),
            price: (float) $product->price,
            description: (string) ($product->short_description ?: $product->description ?: ''),
            inStock: (bool) $product->in_stock,
        );
    }

    /**
     * @return list<array{question: string, answer: string}>
     */
    public static function build(
        string $name,
        string $category,
        float $price,
        string $description = '',
        bool $inStock = true,
    ): array {
        $name = trim($name) !== '' ? trim($name) : 'this product';
        $shortName = Str::limit($name, 52, '…');
        $category = trim($category) !== '' ? trim($category) : 'General';
        $priceLabel = MoneyFormat::pkr(max(0, $price));
        $usage = self::usageHint($name, $category, $description);
        $stockLine = $inStock
            ? 'It is currently listed as in stock on Beautage.'
            : 'Availability can change quickly—use the Add to Bag button to see live stock status.';

        return [
            [
                'question' => "What is {$shortName}?",
                'answer' => self::introAnswer($shortName, $category, $description),
            ],
            [
                'question' => 'Is this product 100% original on Beautage?',
                'answer' => "Yes. Beautage is an authorized storefront. {$shortName} is sourced for authentic quality with secure checkout and customer support if you need help with your order.",
            ],
            [
                'question' => "How fast is delivery for {$shortName} in Pakistan?",
                'answer' => 'Most orders ship within 1–2 business days. Metro cities (Karachi, Lahore, Islamabad/Rawalpindi) often receive parcels in 2–4 business days after dispatch. Remote areas may take a little longer.',
            ],
            [
                'question' => 'Can I pay Cash on Delivery (COD)?',
                'answer' => "Cash on Delivery is available across Pakistan on eligible orders. Your order total for this item starts at {$priceLabel}; free express shipping applies on orders over Rs 7,500 where offered at checkout.",
            ],
            [
                'question' => "What is the return policy for {$shortName}?",
                'answer' => 'Unused items in original packaging may be returned within 30 days of delivery for a refund or exchange, subject to Beautage return guidelines. Contact support with your order ID if something arrives damaged or incorrect.',
            ],
            [
                'question' => "How should I use or care for {$shortName}?",
                'answer' => $usage.' '.$stockLine,
            ],
        ];
    }

    private static function introAnswer(string $shortName, string $category, string $description): string
    {
        $desc = trim(strip_tags($description));
        if ($desc !== '') {
            $snippet = Str::limit($desc, 220);

            return "{$shortName} is listed under {$category} at Beautage. {$snippet}";
        }

        return "{$shortName} is a {$category} pick on Beautage, chosen for everyday value, reliable delivery across Pakistan, and secure checkout including COD where available.";
    }

    private static function usageHint(string $name, string $category, string $description): string
    {
        $blob = strtolower($name.' '.$category.' '.$description);

        if (Str::contains($blob, ['leash', 'collar', 'harness', 'pet', 'dog', 'cat'])) {
            return 'Check sizing and hardware before first use. Attach securely and supervise your pet during walks; rinse or wipe clean after outdoor use.';
        }
        if (Str::contains($blob, ['hair', 'shampoo', 'oil', 'serum', 'mask'])) {
            return 'Patch-test on a small area first. Follow label directions, avoid contact with eyes, and store in a cool, dry place away from direct sunlight.';
        }
        if (Str::contains($blob, ['skin', 'cream', 'lotion', 'facial', 'beauty'])) {
            return 'Apply to clean, dry skin as directed on the packaging. Discontinue use if irritation occurs and consult a professional for sensitive skin concerns.';
        }
        if (Str::contains($blob, ['tool', 'device', 'electric', 'rechargeable', 'battery'])) {
            return 'Read the safety instructions before first use. Charge fully if rechargeable, keep away from water unless rated waterproof, and unplug when not in use.';
        }

        return 'Follow any instructions on the packaging or product label. Store in a clean, dry place and keep out of reach of children when not in use.';
    }
}
