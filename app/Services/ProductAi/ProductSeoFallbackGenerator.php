<?php

namespace App\Services\ProductAi;

use Illuminate\Support\Str;

/** Rule-based SEO copy when no paid AI API key is configured. */
class ProductSeoFallbackGenerator
{
    public function generate(
        string $productName,
        ?string $targetKeyword = null,
        ?string $specs = null,
        ?string $categoryName = null,
    ): ProductAiContent {
        $productName = trim($productName);
        $keyword = trim((string) $targetKeyword);
        if ($keyword === '') {
            $keyword = Str::limit($productName, 50, '');
        }

        $category = trim((string) $categoryName) ?: 'Beautage';
        $slug = Str::limit(Str::slug($keyword !== '' ? $keyword : $productName), 80, '');

        $short = "Shop {$productName} at Beautage Pakistan — authentic quality, fast nationwide delivery, and secure checkout.";
        if ($keyword !== '') {
            $short = "Discover {$productName} — optimized for {$keyword}. Beautage offers trusted beauty & wellness with COD across Pakistan.";
        }

        $listing = Str::limit(strip_tags($short), 300, '');

        $html = '<h2>About this product</h2>'
            .'<p>'.e($productName).' is available at Beautage with reliable delivery across Pakistan.</p>'
            .'<h3>Why customers choose Beautage</h3>'
            .'<ul>'
            .'<li><strong>Trusted catalog:</strong> Curated '.e($category).' products.</li>'
            .'<li><strong>Nationwide delivery:</strong> Major cities & remote towns.</li>'
            .'<li><strong>Secure checkout:</strong> Multiple payment options including COD.</li>'
            .'</ul>';

        if ($specs !== null && trim($specs) !== '') {
            $html .= '<h3>Specifications</h3><p>'.nl2br(e(trim($specs))).'</p>';
        }

        $tags = array_values(array_unique(array_filter([
            Str::lower($keyword),
            Str::lower(Str::slug($productName, ' ')),
            Str::lower($category),
            'beautage pakistan',
            'online shopping pakistan',
            'beauty products pakistan',
        ])));

        return ProductAiContent::fromArray([
            'seo_title' => Str::limit($productName.' | Buy Online in Pakistan — Beautage', 70, ''),
            'meta_description' => Str::limit("Buy {$productName} online in Pakistan at Beautage. {$keyword}. Fast delivery & secure checkout.", 160, ''),
            'slug' => $slug,
            'short_description' => $short,
            'listing_summary' => $listing,
            'html_description' => $html,
            'detail_heading' => 'Product details',
            'bullet_features' => [
                ['title' => 'Authentic quality', 'description' => 'Sourced for Beautage customers in Pakistan.'],
                ['title' => 'Fast delivery', 'description' => 'Dispatch from our fulfillment network nationwide.'],
                ['title' => 'Easy returns policy', 'description' => 'See our returns page for eligibility.'],
            ],
            'specs_rows' => $this->specRowsFromText($specs, $productName),
            'search_tags' => $tags,
            'seo_focus_keyword' => $keyword,
        ]);
    }

    /** @return list<array{label: string, value: string}> */
    protected function specRowsFromText(?string $specs, string $productName): array
    {
        $rows = [
            ['label' => 'Product', 'value' => $productName],
            ['label' => 'Brand', 'value' => 'Beautage'],
            ['label' => 'Country of sale', 'value' => 'Pakistan'],
        ];

        if ($specs === null || trim($specs) === '') {
            return $rows;
        }

        foreach (preg_split('/\r\n|\r|\n/', $specs) ?: [] as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }
            if (str_contains($line, ':')) {
                [$label, $value] = array_map('trim', explode(':', $line, 2));
                if ($label !== '' && $value !== '') {
                    $rows[] = ['label' => $label, 'value' => $value];
                }
            }
        }

        return array_slice($rows, 0, 8);
    }
}
