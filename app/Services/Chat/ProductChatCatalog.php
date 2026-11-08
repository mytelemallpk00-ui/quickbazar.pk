<?php

namespace App\Services\Chat;

use App\Models\Category;
use App\Models\Product;
use App\Support\CheckoutSettings;
use App\Support\SiteSettings;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ProductChatCatalog
{
    /** @var list<string> */
    protected array $stopWords = [
        'the', 'and', 'for', 'with', 'your', 'you', 'please', 'tell', 'show', 'about',
        'kya', 'hai', 'hain', 'ka', 'ki', 'ke', 'ko', 'se', 'mein', 'par', 'yeh', 'woh',
        'mujhe', 'chahiye', 'batao', 'bata', 'dein', 'den', 'koi', 'konsa', 'konse',
        'beautage', 'website', 'online', 'shop', 'store',
    ];

    /**
     * @return array{reply: string, checkout_url: ?string, track_url: ?string, escalate: bool, products: list<array<string, mixed>>}|null
     */
    public function tryReply(string $customerMessage): ?array
    {
        $normalized = Str::lower(trim($customerMessage));
        if ($normalized === '') {
            return null;
        }

        if ($this->isCatalogListIntent($normalized)) {
            return $this->replyPopularProducts();
        }

        $category = $this->matchCategoryInMessage($normalized);
        if ($category !== null) {
            return $this->replyCategoryProducts($category);
        }

        $explicitQuery = $this->extractProductQuery($normalized);
        if ($explicitQuery !== null) {
            return $this->replyForSearchQuery($explicitQuery);
        }

        if ($this->looksLikeProductQuestion($normalized)) {
            $terms = $this->meaningfulTerms($normalized);
            if ($terms !== []) {
                return $this->replyForSearchQuery(implode(' ', $terms));
            }
        }

        return $this->fallbackProductSearch($normalized);
    }

    /**
     * Last pass: fuzzy DB product match so user does not need to rephrase.
     *
     * @return array{reply: string, checkout_url: ?string, track_url: ?string, escalate: bool, products: list<array<string, mixed>>}|null
     */
    public function fallbackProductSearch(string $message): ?array
    {
        $normalized = Str::lower(trim($message));
        if ($this->containsAny($normalized, [
            'checkout', 'track', 'order status', 'agent', 'human', 'shipping', 'return',
            'salam', 'hello', 'hi ', 'hey', 'delivery', 'cod', 'payment', 'contact',
        ])) {
            return null;
        }

        $terms = $this->meaningfulTerms($normalized);
        if ($terms === []) {
            return null;
        }

        $result = $this->replyForSearchQuery(implode(' ', $terms));

        return ($result !== null && ($result['products'] ?? []) !== []) ? $result : null;
    }

    /** @return array{reply: string, checkout_url: ?string, track_url: ?string, escalate: bool, products: list<array<string, mixed>>} */
    protected function replyPopularProducts(): array
    {
        $products = $this->baseProductQuery()
            ->orderByDesc('is_flash')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->limit(6)
            ->get();

        if ($products->isEmpty()) {
            return $this->emptyCatalogReply();
        }

        $collectionsUrl = route('collections');
        $lines = [
            'Yeh hamare popular products hain (live prices):',
            '',
            ...$this->formatProductLines($products),
            '',
            "Aur dekhne ke liye: {$collectionsUrl}",
            'Kisi product ka naam type karein — main detail aur link bhej dunga.',
        ];

        return $this->buildResult(implode("\n", $lines), $products);
    }

    /** @return array{reply: string, checkout_url: ?string, track_url: ?string, escalate: bool, products: list<array<string, mixed>>} */
    protected function replyCategoryProducts(Category $category): array
    {
        $products = $this->baseProductQuery()
            ->where('category_id', $category->id)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->limit(6)
            ->get();

        if ($products->isEmpty()) {
            return [
                'reply' => "Category \"{$category->name}\" mein abhi koi product listed nahi. Collections browse karein: ".route('collections'),
                'checkout_url' => null,
                'track_url' => null,
                'escalate' => false,
                'products' => [],
            ];
        }

        $lines = [
            "{$category->name} — available products:",
            '',
            ...$this->formatProductLines($products),
            '',
            'Detail ke liye product ka naam dubara likhein ya neeche link par click karein.',
        ];

        return $this->buildResult(implode("\n", $lines), $products);
    }

    /**
     * @return array{reply: string, checkout_url: ?string, track_url: ?string, escalate: bool, products: list<array<string, mixed>>}|null
     */
    protected function replyForSearchQuery(string $query): ?array
    {
        $query = trim(Str::lower($query));
        if ($query === '') {
            return null;
        }

        $terms = $this->meaningfulTerms($query);
        if ($terms === []) {
            $terms = array_values(array_filter(preg_split('/\s+/u', $query) ?: []));
        }

        $products = $this->searchProducts($terms, 8);
        if ($products->isEmpty()) {
            return null;
        }

        if ($products->count() === 1) {
            return $this->replySingleProduct($products->first());
        }

        $best = $products->first();
        $lines = [
            "In products se match mila (\"{$query}\"):",
            '',
            ...$this->formatProductLines($products->take(5)),
        ];

        if ($products->count() > 5) {
            $lines[] = '';
            $lines[] = 'Aur options ke liye collections dekhein: '.route('collections');
        } else {
            $lines[] = '';
            $lines[] = "Sab se close match: {$best->name} — detail ke liye link use karein.";
        }

        return $this->buildResult(implode("\n", $lines), $products->take(5));
    }

    /** @return array{reply: string, checkout_url: ?string, track_url: ?string, escalate: bool, products: list<array<string, mixed>>} */
    protected function replySingleProduct(Product $product): array
    {
        $url = route('product.show', $product);
        $priceLine = $this->formatPriceLine($product);
        $stock = $product->in_stock
            ? 'In stock — order kar sakte hain.'
            : 'Currently out of stock — WhatsApp par availability pooch sakte hain.';

        $summary = $this->productSummaryText($product);

        $ratingPart = $product->reviews > 0
            ? sprintf('Rating: %.1f/5 (%d reviews)', (float) $product->rating, (int) $product->reviews)
            : null;

        $lines = array_filter([
            $product->name,
            $product->category ? "Category: {$product->category}" : null,
            $priceLine,
            $ratingPart,
            $stock,
            '',
            $summary,
            '',
            "Product page: {$url}",
            'Checkout ke liye "checkout" likhein ya product page se cart mein add karein.',
        ]);

        return $this->buildResult(implode("\n", $lines), collect([$product]), route('checkout'));
    }

    /** @param  list<string>  $terms */
    protected function searchProducts(array $terms, int $limit = 6): Collection
    {
        if ($terms === []) {
            return collect();
        }

        $candidates = $this->baseProductQuery()
            ->where(function (Builder $q) use ($terms): void {
                foreach ($terms as $term) {
                    $like = '%'.$term.'%';
                    $q->orWhere(function (Builder $inner) use ($like): void {
                        $inner->where('name', 'like', $like)
                            ->orWhere('slug', 'like', $like)
                            ->orWhere('category', 'like', $like)
                            ->orWhere('short_description', 'like', $like)
                            ->orWhere('description', 'like', $like);
                    });
                }
            })
            ->limit(40)
            ->get();

        return $candidates
            ->map(fn (Product $product) => ['product' => $product, 'score' => $this->scoreProductMatch($product, $terms)])
            ->filter(fn (array $row) => $row['score'] >= 3)
            ->sortByDesc('score')
            ->take($limit)
            ->pluck('product')
            ->values();
    }

    /** @param  list<string>  $terms */
    protected function scoreProductMatch(Product $product, array $terms): int
    {
        $haystacks = [
            Str::lower((string) $product->name) => 5,
            Str::lower((string) $product->slug) => 4,
            Str::lower((string) $product->category) => 2,
            Str::lower(strip_tags((string) ($product->short_description ?? ''))) => 2,
            Str::lower(strip_tags((string) ($product->description ?? ''))) => 1,
        ];

        $score = 0;
        foreach ($terms as $term) {
            if (strlen($term) < 2) {
                continue;
            }
            foreach ($haystacks as $text => $weight) {
                if ($text !== '' && str_contains($text, $term)) {
                    $score += $weight;
                }
            }
        }

        return $score;
    }

    protected function baseProductQuery(): Builder
    {
        return Product::query()->with('productCategory');
    }

    protected function isCatalogListIntent(string $normalized): bool
    {
        return $this->containsAny($normalized, [
            'all products', 'show products', 'list products', 'product list', 'catalog',
            'collections', 'kya sell', 'kya milta', 'kya available', 'range kya',
            'products dikhao', 'products batao', 'shop kya', 'menu',
        ]);
    }

    protected function looksLikeProductQuestion(string $normalized): bool
    {
        return $this->containsAny($normalized, [
            'price', 'kitna', 'kitne', 'cost', 'rate', 'detail', 'details', 'info',
            'information', 'about', 'kya hai', 'benefit', 'use', 'kaise use',
            'product', 'serum', 'cream', 'facial', 'mask', 'oil', 'lotion',
        ]);
    }

    protected function extractProductQuery(string $normalized): ?string
    {
        $patterns = [
            '/(?:price|rate|cost|kitna|kitne)\s+(?:of|for|)?\s*(.+)$/u',
            '/(?:about|info|information|details|detail)\s+(?:on|of|for|)?\s*(.+)$/u',
            '/(?:product|item)\s+(?:name|)?\s*(.+)$/u',
            '/^(.+?)\s+(?:price|rate|cost|kitna|kitne)$/u',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $normalized, $m)) {
                $q = trim($m[1]);
                if ($q !== '' && ! in_array($q, $this->stopWords, true)) {
                    return $q;
                }
            }
        }

        return null;
    }

    protected function matchCategoryInMessage(string $normalized): ?Category
    {
        if (! $this->containsAny($normalized, [
            'category', 'collection', 'skincare', 'hair', 'makeup', 'serum', 'cream',
        ]) && ! str_contains($normalized, ' products')) {
            // Still try category name match if message mentions a known category label
        }

        $categories = Category::query()
            ->where('is_active', true)
            ->orderByDesc('sort_order')
            ->get();

        foreach ($categories as $category) {
            $name = Str::lower(trim($category->name));
            if ($name !== '' && str_contains($normalized, $name)) {
                return $category;
            }
            $slug = Str::lower(str_replace('-', ' ', (string) $category->slug));
            if ($slug !== '' && str_contains($normalized, $slug)) {
                return $category;
            }
        }

        return null;
    }

    /** @return list<string> */
    protected function meaningfulTerms(string $text): array
    {
        $parts = preg_split('/[\s,.!?;:]+/u', Str::lower($text)) ?: [];
        $terms = [];

        foreach ($parts as $part) {
            $part = trim($part);
            if ($part === '' || strlen($part) < 2) {
                continue;
            }
            if (in_array($part, $this->stopWords, true)) {
                continue;
            }
            $terms[] = $part;
        }

        return array_values(array_unique($terms));
    }

    /** @param  Collection<int, Product>  $products */
    protected function formatProductLines(Collection $products): array
    {
        $lines = [];
        foreach ($products as $index => $product) {
            $n = $index + 1;
            $stock = $product->in_stock ? '' : ' (out of stock)';
            $lines[] = "{$n}. {$product->name} — ".$this->formatPriceLine($product).$stock;
            $lines[] = '   '.route('product.show', $product);
        }

        return $lines;
    }

    protected function formatPriceLine(Product $product): string
    {
        $symbol = $this->currencySymbol();
        $price = $symbol.number_format((float) $product->price, 0, '.', ',');

        if ($product->old_price && (float) $product->old_price > (float) $product->price) {
            $old = $symbol.number_format((float) $product->old_price, 0, '.', ',');

            return "{$price} (was {$old})";
        }

        return $price;
    }

    protected function currencySymbol(): string
    {
        $currency = strtoupper((string) (CheckoutSettings::all()['currency'] ?? SiteSettings::get('analytics_gmc_currency', 'PKR')));

        return match ($currency) {
            'PKR' => 'Rs ',
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            default => $currency.' ',
        };
    }

    protected function productSummaryText(Product $product): string
    {
        $text = trim((string) ($product->short_description ?: $product->description ?: $product->detail_body));
        if ($text === '') {
            return 'Premium QuickBazar.pk product — full ingredients aur usage product page par hain.';
        }

        return Str::limit(strip_tags($text), 320);
    }

    /**
     * @param  Collection<int, Product>  $products
     * @return array{reply: string, checkout_url: ?string, track_url: ?string, escalate: bool, products: list<array<string, mixed>>}
     */
    protected function buildResult(string $reply, Collection $products, ?string $checkoutUrl = null): array
    {
        return [
            'reply' => $reply,
            'checkout_url' => $checkoutUrl,
            'track_url' => null,
            'escalate' => false,
            'products' => $products->map(fn (Product $p) => $this->productPayload($p))->values()->all(),
        ];
    }

    /** @return array<string, mixed> */
    protected function productPayload(Product $product): array
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'url' => route('product.show', $product),
            'price_label' => $this->formatPriceLine($product),
            'in_stock' => (bool) $product->in_stock,
            'image' => $product->image,
        ];
    }

    /** @return array{reply: string, checkout_url: ?string, track_url: ?string, escalate: bool, products: list<array<string, mixed>>} */
    protected function emptyCatalogReply(): array
    {
        return [
            'reply' => 'Abhi catalog load ho raha hai — thori der baad try karein ya collections page dekhein: '.route('collections'),
            'checkout_url' => null,
            'track_url' => null,
            'escalate' => false,
            'products' => [],
        ];
    }

    /** @param  list<string>  $needles */
    protected function containsAny(string $haystack, array $needles): bool
    {
        foreach ($needles as $needle) {
            if (str_contains($haystack, Str::lower($needle))) {
                return true;
            }
        }

        return false;
    }
}
