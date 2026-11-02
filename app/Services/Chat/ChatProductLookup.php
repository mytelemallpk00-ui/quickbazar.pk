<?php

namespace App\Services\Chat;

use App\Models\Product;
use App\Support\CheckoutSettings;
use App\Support\SiteSettings;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ChatProductLookup
{
    /** @var list<string> */
    protected array $stopWords = [
        'the', 'and', 'for', 'with', 'your', 'you', 'please', 'tell', 'show', 'about',
        'kya', 'hai', 'hain', 'ka', 'ki', 'ke', 'ko', 'se', 'mein', 'par', 'price', 'kitna', 'kitne',
        'batao', 'bata', 'den', 'mujhe', 'chahiye', 'product', 'stock', 'available', 'milta', 'rate', 'cost',
    ];

    /**
     * @return array{type: string, product?: Product, products?: Collection<int, Product>, query?: string}|null
     */
    public function resolve(string $message): ?array
    {
        $cleanName = $this->cleanProductQuery($message);
        if ($cleanName !== '') {
            $exact = Product::query()
                ->where(function ($q) use ($cleanName): void {
                    $q->whereRaw('LOWER(name) = ?', [Str::lower($cleanName)])
                        ->orWhere('slug', Str::slug($cleanName));
                })
                ->first();

            if ($exact) {
                return ['type' => 'single', 'product' => $exact];
            }
        }

        $terms = $this->searchTerms($message);
        if ($terms === []) {
            return null;
        }

        $matches = $this->rankedMatches($terms);
        if ($matches->isEmpty()) {
            return ['type' => 'none', 'query' => implode(' ', $terms)];
        }

        if ($matches->count() === 1) {
            return ['type' => 'single', 'product' => $matches->first()];
        }

        $top = $matches->first();
        $second = $matches->get(1);
        $topScore = $this->score($top, $terms);
        $secondScore = $second ? $this->score($second, $terms) : 0;

        if ($topScore >= 8 && ($topScore - $secondScore) >= 4) {
            return ['type' => 'single', 'product' => $top];
        }

        return ['type' => 'multiple', 'products' => $matches->take(5), 'query' => implode(' ', $terms)];
    }

    public function formatSingle(Product $product): string
    {
        $symbol = $this->currencySymbol();
        $price = $symbol.number_format((float) $product->price, 0, '.', ',');
        $stock = (bool) $product->in_stock;

        $lines = [
            'Product (database):',
            '',
            'Name: '.$product->name,
            'Price: '.$price,
        ];

        if ($product->old_price && (float) $product->old_price > (float) $product->price) {
            $lines[] = 'Compare at: '.$symbol.number_format((float) $product->old_price, 0, '.', ',');
        }

        $lines[] = 'Stock: '.($stock ? 'In stock (in_stock = yes)' : 'Out of stock (in_stock = no)');
        $lines[] = 'Product ID: '.$product->id;
        $lines[] = '';
        $lines[] = route('product.show', $product);

        return implode("\n", $lines);
    }

    /** @param  Collection<int, Product>  $products */
    public function formatMultiple(Collection $products, string $query): string
    {
        $lines = [
            "Database mein \"{$query}\" se kai products match hue — exact naam bhejein:",
            '',
        ];

        foreach ($products as $index => $product) {
            $n = $index + 1;
            $price = $this->currencySymbol().number_format((float) $product->price, 0);
            $stock = $product->in_stock ? 'in stock' : 'out of stock';
            $lines[] = "{$n}. {$product->name} — {$price} ({$stock})";
        }

        $lines[] = '';
        $lines[] = 'Sirf product ka poora naam copy karke dubara bhejein.';

        return implode("\n", $lines);
    }

    /** @return list<array<string, mixed>> */
    public function productPayload(Product $product): array
    {
        $symbol = $this->currencySymbol();
        $price = $symbol.number_format((float) $product->price, 0, '.', ',');

        return [[
            'id' => $product->id,
            'name' => $product->name,
            'url' => route('product.show', $product),
            'price_label' => $price,
            'in_stock' => (bool) $product->in_stock,
            'image' => $product->image,
        ]];
    }

    protected function cleanProductQuery(string $message): string
    {
        $q = trim($message);
        $q = preg_replace('/\b(price|kitna|kitne|cost|rate|stock|available|milta|batao|bata|den|kya|hai)\b/ui', ' ', $q) ?? $q;
        $q = preg_replace('/\s+/u', ' ', trim($q)) ?? trim($q);

        return $q;
    }

    /** @return list<string> */
    protected function searchTerms(string $message): array
    {
        $parts = preg_split('/[\s,.!?;:]+/u', Str::lower($message)) ?: [];
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

    /** @param  list<string>  $terms */
    protected function rankedMatches(array $terms): Collection
    {
        if ($terms === []) {
            return collect();
        }

        $candidates = Product::query()
            ->where(function ($q) use ($terms): void {
                foreach ($terms as $term) {
                    $like = '%'.$term.'%';
                    $q->orWhere('name', 'like', $like)
                        ->orWhere('slug', 'like', $like);
                }
            })
            ->limit(40)
            ->get();

        return $candidates
            ->map(fn (Product $p) => ['product' => $p, 'score' => $this->score($p, $terms)])
            ->filter(fn (array $row) => $row['score'] >= 4)
            ->sortByDesc('score')
            ->pluck('product')
            ->values();
    }

    /** @param  list<string>  $terms */
    protected function score(Product $product, array $terms): int
    {
        $name = Str::lower((string) $product->name);
        $slug = Str::lower((string) $product->slug);
        $score = 0;

        foreach ($terms as $term) {
            if ($name === $term || $slug === $term) {
                $score += 20;
            } elseif (str_starts_with($name, $term)) {
                $score += 10;
            } elseif (str_contains($name, $term)) {
                $score += strlen($term) >= 4 ? 6 : 4;
            } elseif (str_contains($slug, $term)) {
                $score += 3;
            }
        }

        return $score;
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
}
