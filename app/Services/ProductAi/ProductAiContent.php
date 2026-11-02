<?php

namespace App\Services\ProductAi;

use Illuminate\Support\Str;

class ProductAiContent
{
    /**
     * @param  list<string>  $searchTags
     * @param  list<array{title: string, description: string}>  $detailHighlights
     * @param  list<array{label: string, value: string}>  $specsRows
     */
    public function __construct(
        public string $seoTitle,
        public string $metaDescription,
        public string $slug,
        public string $shortDescription,
        public string $listingSummary,
        public string $htmlDescription,
        public string $detailHeading,
        public array $detailHighlights,
        public array $specsRows,
        public array $searchTags,
        public string $focusKeyword,
        public ?string $ogTitle = null,
        public ?string $ogDescription = null,
        public ?string $routineBody = null,
    ) {}

    /** @param  array<string, mixed>  $data */
    public static function fromArray(array $data): self
    {
        $highlights = [];
        foreach ($data['bullet_features'] ?? $data['detail_highlights'] ?? [] as $row) {
            if (! is_array($row)) {
                continue;
            }
            $title = trim((string) ($row['title'] ?? ''));
            $desc = trim((string) ($row['description'] ?? ''));
            if ($title !== '' && $desc !== '') {
                $highlights[] = ['title' => $title, 'description' => $desc];
            }
        }

        $specs = [];
        foreach ($data['specs_rows'] ?? [] as $row) {
            if (! is_array($row)) {
                continue;
            }
            $label = trim((string) ($row['label'] ?? ''));
            $value = trim((string) ($row['value'] ?? ''));
            if ($label !== '' && $value !== '') {
                $specs[] = ['label' => $label, 'value' => $value];
            }
        }

        $tags = [];
        foreach ($data['search_tags'] ?? [] as $tag) {
            $tag = trim((string) $tag);
            if ($tag !== '') {
                $tags[] = $tag;
            }
        }

        $slug = Str::slug((string) ($data['slug'] ?? ''));
        if ($slug === '') {
            $slug = Str::slug((string) ($data['seo_title'] ?? 'product'));
        }
        $slug = Str::limit($slug, 80, '');

        $focus = trim((string) ($data['seo_focus_keyword'] ?? $data['focus_keyword'] ?? ''));

        return new self(
            seoTitle: Str::limit(trim((string) ($data['seo_title'] ?? '')), 70, ''),
            metaDescription: Str::limit(trim((string) ($data['meta_description'] ?? '')), 320, ''),
            slug: $slug,
            shortDescription: Str::limit(trim((string) ($data['short_description'] ?? '')), 600, ''),
            listingSummary: Str::limit(trim((string) ($data['listing_summary'] ?? '')), 320, ''),
            htmlDescription: ProductAiService::sanitizeHtml((string) ($data['html_description'] ?? '')),
            detailHeading: trim((string) ($data['detail_heading'] ?? 'Product details')),
            detailHighlights: $highlights,
            specsRows: $specs,
            searchTags: $tags,
            focusKeyword: $focus,
            ogTitle: isset($data['og_title']) ? Str::limit(trim((string) $data['og_title']), 70, '') : null,
            ogDescription: isset($data['og_description']) ? Str::limit(trim((string) $data['og_description']), 320, '') : null,
            routineBody: isset($data['routine_body']) ? trim((string) $data['routine_body']) : null,
        );
    }

    /** @return array<string, mixed> */
    public function toFormState(): array
    {
        $keywords = $this->searchTags !== []
            ? implode(', ', $this->searchTags)
            : $this->focusKeyword;

        $state = [
            'slug' => $this->slug,
            'short_description' => $this->shortDescription,
            'description' => $this->listingSummary,
            'detail_heading' => $this->detailHeading,
            'detail_body' => $this->htmlDescription,
            'detail_highlights' => $this->detailHighlights,
            'specs_heading' => $this->specsRows !== [] ? 'Specifications' : null,
            'specs_rows' => $this->specsRows,
            'meta_title' => $this->seoTitle,
            'meta_description' => $this->metaDescription,
            'meta_keywords' => $keywords,
            'seo_focus_keyword' => $this->focusKeyword,
            'og_title' => $this->ogTitle ?: $this->seoTitle,
            'og_description' => $this->ogDescription ?: $this->metaDescription,
        ];

        if ($this->routineBody) {
            $state['routine_heading'] = 'How to use';
            $state['routine_body'] = $this->routineBody;
        }

        return $state;
    }

    /** @return array<string, mixed> */
    public function toProductPayload(): array
    {
        return $this->toFormState();
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'seo_title' => $this->seoTitle,
            'meta_description' => $this->metaDescription,
            'slug' => $this->slug,
            'short_description' => $this->shortDescription,
            'listing_summary' => $this->listingSummary,
            'html_description' => $this->htmlDescription,
            'detail_heading' => $this->detailHeading,
            'detail_highlights' => $this->detailHighlights,
            'specs_rows' => $this->specsRows,
            'search_tags' => $this->searchTags,
            'seo_focus_keyword' => $this->focusKeyword,
        ];
    }
}
