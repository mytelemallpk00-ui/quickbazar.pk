<?php

namespace App\Services\ProductImport;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImportBatch;
use App\Services\AiSeo\AiSeoManager;
use App\Services\ProductAi\ProductAiService;
use App\Support\ProductFaqGenerator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ProductImportService
{
    public function __construct(
        protected ProductImportMapper $mapper,
        protected UrlProductFetcher $urlFetcher,
        protected CategoryListingFetcher $categoryFetcher,
        protected ImportPriceNormalizer $prices,
        protected ImportContentSanitizer $content,
    ) {}

    /**
     * @param  array{category_id?: int|null, update_existing?: bool, currency?: string}  $options
     */
    public function importCsv(string $source, string $absolutePath, ?string $fileName, array $options = []): ProductImportResult
    {
        ImportSchemaSetup::ensure();

        $handle = fopen($absolutePath, 'r');
        if ($handle === false) {
            return new ProductImportResult(failures: [['message' => 'Could not read CSV file.']]);
        }

        $header = fgetcsv($handle);
        if (! $header) {
            fclose($handle);

            return new ProductImportResult(failures: [['message' => 'CSV file is empty.']]);
        }

        $header = array_map(fn ($h) => trim((string) $h), $header);
        $result = new ProductImportResult;
        $rowNum = 1;

        while (($line = fgetcsv($handle)) !== false) {
            $rowNum++;
            if ($this->isEmptyLine($line)) {
                continue;
            }

            $result->total++;
            $assoc = [];
            foreach ($header as $i => $col) {
                $assoc[$col] = $line[$i] ?? null;
            }

            $normalized = $this->mapper->mapRow($source, $assoc);
            if (! $normalized) {
                $result->failures[] = ['row' => $rowNum, 'message' => 'Missing product title/name.'];

                continue;
            }

            $this->persistNormalized($source, $normalized, $options, $result, $rowNum);
        }

        fclose($handle);

        $this->recordBatch($source, 'csv', $fileName, $result);

        return $result;
    }

    /**
     * @param  list<string>  $urls
     * @param  array{category_id?: int|null, update_existing?: bool}  $options
     */
    public function importUrls(string $source, array $urls, array $options = []): ProductImportResult
    {
        ImportSchemaSetup::ensure();

        $result = $this->importUrlList($source, $urls, $options);
        $this->recordBatch($source, 'url', null, $result);

        return $result;
    }

    /**
     * @param  array{category_id?: int|null, update_existing?: bool, currency?: string}  $options
     */
    public function importCategoryListing(
        string $source,
        string $listingUrl,
        int $limit,
        array $options = [],
        bool $discoverUntilEmpty = false,
    ): ProductImportResult {
        ImportSchemaSetup::ensure();

        $limit = CategoryListingFetcher::normalizeLimit($limit);
        @set_time_limit(max(600, min(7200, $limit * 4)));
        $urls = $this->categoryFetcher->discoverProductUrls($source, $listingUrl, $limit, $discoverUntilEmpty);

        if ($urls === []) {
            $hint = match ($source) {
                ProductImportBatch::SOURCE_DARAZ => 'No Daraz products found. Use a category URL like https://www.daraz.pk/beauty-health/ or a search page like https://www.daraz.pk/catalog/?q=serum (copy the URL from your browser address bar).',
                ProductImportBatch::SOURCE_SHOPIFY => 'No Shopify products found. Use Marketplace = Shopify and a URL like https://rozzana.pk/collections/all — ensure products.json is reachable.',
                default => 'No product links found on this page. Open the category in your browser and confirm product cards are visible, then paste that exact URL.',
            };
            $result = new ProductImportResult(failures: [['message' => $hint]]);
            $this->recordBatch($source, 'category', Str::limit($listingUrl, 250), $result);

            return $result;
        }

        $result = $this->importUrlList($source, $urls, $options);
        $this->recordBatch($source, 'category', Str::limit($listingUrl, 250), $result);

        return $result;
    }

    /**
     * @param  list<string>  $urls
     * @param  array{category_id?: int|null, update_existing?: bool, currency?: string}  $options
     */
    private function importUrlList(string $source, array $urls, array $options): ProductImportResult
    {
        @set_time_limit(max(600, min(7200, count($urls) * 4)));

        $result = new ProductImportResult;

        foreach ($urls as $index => $url) {
            $url = trim($url);
            if ($url === '') {
                continue;
            }

            $result->total++;
            $normalized = $this->urlFetcher->fetch($source, $url, $this->priceMode($options));

            if (! $normalized) {
                $result->failures[] = ['row' => $index + 1, 'message' => "Could not fetch product data from URL: {$url}"];

                continue;
            }

            $normalized->sourceUrl ??= $url;
            $this->persistNormalized($source, $normalized, $options, $result, $index + 1);
        }

        return $result;
    }

    public function makeTemplateCsv(string $source): string
    {
        $headers = $this->mapper->templateHeaders($source);
        $samples = $this->mapper->templateSampleRows($source);

        $out = fopen('php://temp', 'r+');
        fputcsv($out, $headers);
        foreach ($samples as $sample) {
            $line = [];
            foreach ($headers as $header) {
                $line[] = $sample[$header] ?? '';
            }
            fputcsv($out, $line);
        }
        rewind($out);
        $csv = stream_get_contents($out);
        fclose($out);

        return $csv ?: '';
    }

    /** @param  array{category_id?: int|null, update_existing?: bool, currency?: string}  $options */
    private function persistNormalized(
        string $source,
        NormalizedProduct $item,
        array $options,
        ProductImportResult $result,
        int|string $rowRef,
    ): void {
        if (! $item->image) {
            $item = new NormalizedProduct(
                name: $item->name,
                price: $item->price,
                oldPrice: $item->oldPrice,
                description: $item->description,
                image: asset('images/beautagelogosquare2.png'),
                hoverImage: asset('images/beautagelogosquare2.png'),
                gallery: $item->gallery,
                externalSku: $item->externalSku,
                sourceUrl: $item->sourceUrl,
                categoryName: $item->categoryName,
                rating: $item->rating,
                reviews: $item->reviews,
                inStock: $item->inStock,
            );
        }

        $categoryId = $options['category_id'] ?? null;
        $categoryName = $item->categoryName;
        if ($categoryId) {
            $categoryName = Category::query()->whereKey($categoryId)->value('name') ?? $categoryName;
        }

        $priceMode = $this->priceMode($options);
        $price = $this->prices->toStoredPkr($this->prices->parse($item->price), $priceMode);
        $oldPrice = $this->prices->toStoredPkr($this->prices->parse($item->oldPrice ?: $item->price), $priceMode);
        if ($oldPrice < $price) {
            $oldPrice = $price;
        }
        if ($price <= 0 && $oldPrice > 0) {
            $price = $oldPrice;
        }

        $cleanDescription = $this->content->cleanDescription($item->description, $item->sourceUrl);
        if ($cleanDescription === '') {
            $cleanDescription = $item->name;
        }

        $slugBase = Str::slug($item->name);
        $payload = [
            'name' => $item->name,
            'category' => $categoryName ?: 'General',
            'category_id' => $categoryId,
            'price' => $price,
            'old_price' => $oldPrice,
            'rating' => $item->rating,
            'reviews' => $item->reviews,
            'image' => $item->image,
            'hover_image' => $item->hoverImage ?: $item->image,
            'description' => $cleanDescription,
            'short_description' => Str::limit($cleanDescription, 600),
            'in_stock' => $item->inStock,
            'gallery_images' => $item->gallery ?: [$item->image],
        ];

        if (Schema::hasColumn('products', 'import_source')) {
            $payload['import_source'] = $source;
            $payload['external_sku'] = $item->externalSku;
            $payload['source_url'] = $item->sourceUrl;
        }

        $updateExisting = (bool) ($options['update_existing'] ?? true);

        $existing = null;
        if ($updateExisting && $item->externalSku && Schema::hasColumn('products', 'external_sku')) {
            $existing = Product::query()
                ->where('import_source', $source)
                ->where('external_sku', $item->externalSku)
                ->first();
        }

        if (! $existing && $updateExisting && $item->sourceUrl && Schema::hasColumn('products', 'source_url')) {
            $existing = Product::query()->where('source_url', $item->sourceUrl)->first();
        }

        if ($existing) {
            $payload['id'] = $existing->getKey();
            $payload = $this->attachFaqIfMissing(
                $this->scrubSourceFromPayload(
                    $this->maybeEnrichWithAi($payload, $item, $options, $categoryName),
                    $item->sourceUrl,
                    (string) $item->name,
                ),
                $item,
                $categoryName,
                $existing->faq_items,
            );
            unset($payload['id']);
            $existing->fill($payload);
            $existing->save();
            $result->updated++;

            return;
        }

        $payload = $this->attachFaqIfMissing(
            $this->scrubSourceFromPayload(
                $this->maybeEnrichWithAi($payload, $item, $options, $categoryName),
                $item->sourceUrl,
                (string) $item->name,
            ),
            $item,
            $categoryName,
            null,
        );

        $slugBase = Str::slug((string) ($payload['slug'] ?? $slugBase));
        if ($slugBase === '') {
            $slugBase = Str::slug($item->name);
        }

        $slug = $this->uniqueSlug($slugBase);
        unset($payload['slug']);

        try {
            Product::query()->create(array_merge($payload, ['slug' => $slug]));
            $result->created++;
        } catch (\Throwable $e) {
            $result->failures[] = [
                'row' => $rowRef,
                'message' => 'Could not save product: '.$e->getMessage(),
            ];
        }
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  array{category_id?: int|null, update_existing?: bool, currency?: string, ai_seo?: bool, ai_target_keyword?: string|null}  $options
     * @return array<string, mixed>
     */
    private function maybeEnrichWithAi(
        array $payload,
        NormalizedProduct $item,
        array $options,
        ?string $categoryName,
    ): array {
        if (! ($options['ai_seo'] ?? true)) {
            return $payload;
        }

        $specs = $this->content->cleanForAiSpecs((string) ($item->description ?? ''), $item->sourceUrl);
        $importProvider = strtolower(trim((string) ($options['ai_provider'] ?? 'smart')));
        $keyword = isset($options['ai_target_keyword']) ? trim((string) $options['ai_target_keyword']) : '';
        $keyword = $keyword !== '' ? $keyword : null;

        if ($importProvider === 'gemini_flash') {
            /** @var ProductAiService $ai */
            $ai = app(ProductAiService::class);

            return $this->finalizeImportedSeoPayload($ai->enrichProductPayload(
                $payload,
                $item->name,
                $keyword,
                $specs !== '' ? $specs : null,
                $categoryName,
                ProductAiService::PROVIDER_GEMINI_FLASH,
            ));
        }

        $draft = new Product(array_merge($payload, [
            'name' => $item->name,
            'seo_focus_keyword' => $keyword,
        ]));

        $seoProvider = match ($importProvider) {
            'openai' => AiSeoManager::PROVIDER_OPENAI,
            'gemini_pro', 'gemini' => AiSeoManager::PROVIDER_GEMINI,
            default => AiSeoManager::PROVIDER_SMART,
        };

        try {
            /** @var AiSeoManager $manager */
            $manager = app(AiSeoManager::class);
            $seoPayload = $manager->generateForProduct($draft, $seoProvider);
            $ignoreId = null;
            if (isset($payload['id'])) {
                $ignoreId = $payload['id'];
            }

            return array_merge($payload, $manager->productAttributesFromPayload($draft, $seoPayload, $ignoreId));
        } catch (\Throwable $e) {
            report($e);
            /** @var ProductAiService $ai */
            $ai = app(ProductAiService::class);

            return $this->finalizeImportedSeoPayload($ai->enrichProductPayload(
                $payload,
                $item->name,
                $keyword,
                $specs !== '' ? $specs : null,
                $categoryName,
                ProductAiService::PROVIDER_SMART,
            ));
        }
    }

    /** @param  array<string, mixed>  $payload */
    private function finalizeImportedSeoPayload(array $payload): array
    {
        if (! empty($payload['meta_title'])) {
            $payload['seo_title'] = $payload['meta_title'];
        }
        if (! empty($payload['meta_keywords'])) {
            $payload['seo_tags'] = $payload['meta_keywords'];
        }
        $payload['is_seo_optimized'] = true;

        return $payload;
    }

    /** @param  array<string, mixed>  $payload */
    private function scrubSourceFromPayload(array $payload, ?string $sourceUrl, string $fallbackName): array
    {
        $description = $this->content->cleanDescription((string) ($payload['description'] ?? ''), $sourceUrl);
        $payload['description'] = $description !== '' ? $description : $fallbackName;
        $payload['short_description'] = Str::limit($payload['description'], 600);

        if (isset($payload['detail_body']) && is_string($payload['detail_body'])) {
            $payload['detail_body'] = $this->content->cleanHtml($payload['detail_body'], $sourceUrl);
        }

        return $payload;
    }

    private function uniqueSlug(string $base): string
    {
        $slug = $base ?: 'product';
        $i = 0;
        while (Product::query()->where('slug', $slug)->exists()) {
            $i++;
            $slug = $base.'-'.$i;
        }

        return $slug;
    }

    /** @param  array<string, mixed>  $options */
    private function priceMode(array $options): string
    {
        $mode = strtolower(trim((string) ($options['currency'] ?? 'pkr')));

        return in_array($mode, ['usd', 'usd_to_pkr'], true) ? 'usd' : 'pkr';
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function attachFaqIfMissing(
        array $payload,
        NormalizedProduct $item,
        ?string $categoryName,
        mixed $existingFaq,
    ): array {
        if (! Schema::hasColumn('products', 'faq_items')) {
            return $payload;
        }

        if (! empty($payload['faq_items']) || ! empty($existingFaq)) {
            return $payload;
        }

        $payload['faq_items'] = ProductFaqGenerator::build(
            name: (string) ($payload['name'] ?? $item->name),
            category: (string) ($payload['category'] ?? $categoryName ?? 'General'),
            price: (float) ($payload['price'] ?? 0),
            description: (string) ($payload['short_description'] ?? $payload['description'] ?? ''),
            inStock: (bool) ($payload['in_stock'] ?? $item->inStock),
        );

        return $payload;
    }

    /** @param  array<int, string|null>  $line */
    private function isEmptyLine(array $line): bool
    {
        foreach ($line as $cell) {
            if (trim((string) $cell) !== '') {
                return false;
            }
        }

        return true;
    }

    private function recordBatch(string $source, string $mode, ?string $fileName, ProductImportResult $result): void
    {
        if (! Schema::hasTable('product_import_batches')) {
            return;
        }

        ProductImportBatch::query()->create([
            'user_id' => Auth::id(),
            'source' => $source,
            'mode' => $mode,
            'file_name' => $fileName,
            'total_rows' => $result->total,
            'created_count' => $result->created,
            'updated_count' => $result->updated,
            'failed_count' => $result->failedCount(),
            'failures' => array_slice($result->failures, 0, 50),
        ]);
    }
}
