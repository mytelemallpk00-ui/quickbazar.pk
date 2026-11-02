<?php

namespace App\Services\AiSeo;

use App\Models\Product;
use App\Services\ProductAi\ProductAiException;
use App\Services\ProductAi\ProductSeoFallbackGenerator;
use App\Support\AiSettings;
use Illuminate\Support\Str;

class AiSeoManager
{
    public const PROVIDER_OPENAI = 'openai';

    public const PROVIDER_GEMINI = 'gemini';

    public const PROVIDER_SMART = 'smart';

    public function activeProvider(): string
    {
        return AiSettings::seoProvider();
    }

    public function setProvider(string $provider): void
    {
        AiSettings::setSeoProvider($provider);
    }

    public function optimizeProduct(Product $product, ?string $provider = null): AiSeoPayload
    {
        $provider = $provider ?? $this->activeProvider();
        $payload = $this->generateForProduct($product, $provider);
        $this->applyPayloadToProduct($product, $payload);

        return $payload;
    }

    public function generateForProduct(Product $product, ?string $provider = null): AiSeoPayload
    {
        $name = trim((string) $product->name);
        if ($name === '') {
            throw new ProductAiException('Product name is required.');
        }

        $description = trim((string) ($product->short_description ?: $product->description ?: ''));
        $specs = $this->specsTextFromProduct($product);
        $keyword = trim((string) ($product->seo_focus_keyword ?? ''));

        $provider = $provider ?? $this->activeProvider();

        if ($provider === self::PROVIDER_SMART) {
            $fallback = app(ProductSeoFallbackGenerator::class)->generate($name, $keyword ?: null, $specs ?: null, $product->category);

            return new AiSeoPayload(
                seoTitle: $fallback->seoTitle,
                metaDescription: $fallback->metaDescription,
                slug: $fallback->slug,
                htmlDescription: $fallback->htmlDescription,
                bulletPoints: array_map(
                    fn (array $row): string => trim(($row['title'] ?? '').(($row['description'] ?? '') !== '' ? ': '.$row['description'] : '')),
                    $fallback->detailHighlights,
                ),
                seoTags: $fallback->searchTags,
            );
        }

        return $this->driver($provider)->generate($name, $description, $specs, $keyword ?: null);
    }

    public function applyPayloadToProduct(Product $product, AiSeoPayload $payload): void
    {
        $product->fill($this->productAttributesFromPayload($product, $payload, $product->getKey()));
        $product->save();
    }

    /** @return array<string, mixed> */
    public function productAttributesFromPayload(Product $product, AiSeoPayload $payload, mixed $ignoreProductId = null): array
    {
        $slug = $this->uniqueSlug($payload->slug, $ignoreProductId);
        $tagsLine = implode(', ', $payload->seoTags);

        return [
            'seo_title' => $payload->seoTitle,
            'meta_title' => $payload->seoTitle,
            'meta_description' => $payload->metaDescription,
            'slug' => $slug,
            'seo_tags' => $tagsLine,
            'meta_keywords' => $tagsLine,
            'detail_body' => $payload->htmlDescription,
            'detail_heading' => $product->detail_heading ?: 'Product details',
            'detail_highlights' => $payload->detailHighlights(),
            'short_description' => Str::limit(strip_tags($payload->htmlDescription), 600),
            'description' => Str::limit(strip_tags($payload->metaDescription), 320),
            'is_seo_optimized' => true,
        ];
    }

    /** @return array{total: int, optimized: int, unoptimized: int} */
    public function stats(?int $categoryId = null): array
    {
        $query = Product::query();
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $total = (clone $query)->count();
        $optimized = (clone $query)->where('is_seo_optimized', true)->count();

        return [
            'total' => $total,
            'optimized' => $optimized,
            'unoptimized' => max(0, $total - $optimized),
        ];
    }

    protected function driver(string $provider): AiSeoDriver
    {
        return match ($provider) {
            self::PROVIDER_OPENAI => app(OpenAiSeoDriver::class),
            self::PROVIDER_GEMINI => app(GeminiSeoDriver::class, ['useProModel' => true]),
            default => throw new ProductAiException('Unknown AI SEO provider: '.$provider),
        };
    }

    protected function specsTextFromProduct(Product $product): string
    {
        $rows = $product->specs_rows;
        if (! is_array($rows)) {
            return '';
        }

        return collect($rows)
            ->map(fn ($row): string => trim(($row['label'] ?? '').': '.($row['value'] ?? '')))
            ->filter()
            ->implode("\n");
    }

    protected function uniqueSlug(string $base, mixed $ignoreId = null): string
    {
        $slug = Str::slug($base) ?: 'product';
        $slug = Str::limit($slug, 80, '');
        $query = Product::query()->where('slug', $slug);
        if ($ignoreId) {
            $query->whereKeyNot($ignoreId);
        }
        if (! $query->exists()) {
            return $slug;
        }
        $i = 2;
        while (Product::query()->where('slug', $slug.'-'.$i)->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))->exists()) {
            $i++;
        }

        return Str::limit($slug.'-'.$i, 80, '');
    }
}
