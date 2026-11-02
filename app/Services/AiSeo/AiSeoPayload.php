<?php

namespace App\Services\AiSeo;

use Illuminate\Support\Str;

class AiSeoPayload
{
    /**
     * @param  list<string>  $bulletPoints
     * @param  list<string>  $seoTags
     */
    public function __construct(
        public string $seoTitle,
        public string $metaDescription,
        public string $slug,
        public string $htmlDescription,
        public array $bulletPoints,
        public array $seoTags,
    ) {}

    /** @param  array<string, mixed>  $data */
    public static function fromArray(array $data): self
    {
        $bullets = [];
        foreach ($data['bullet_points'] ?? [] as $point) {
            if (is_string($point) && trim($point) !== '') {
                $bullets[] = trim($point);
            } elseif (is_array($point)) {
                $line = trim((string) ($point['title'] ?? ''));
                $desc = trim((string) ($point['description'] ?? ''));
                if ($line !== '') {
                    $bullets[] = $desc !== '' ? $line.': '.$desc : $line;
                }
            }
        }

        $tags = [];
        foreach ($data['seo_tags'] ?? [] as $tag) {
            $tag = trim((string) $tag);
            if ($tag !== '') {
                $tags[] = $tag;
            }
        }

        $slug = Str::slug((string) ($data['slug'] ?? ''));
        if ($slug === '') {
            $slug = Str::slug((string) ($data['seo_title'] ?? 'product'));
        }

        return new self(
            seoTitle: Str::limit(trim((string) ($data['seo_title'] ?? '')), 70, ''),
            metaDescription: Str::limit(trim((string) ($data['meta_description'] ?? '')), 320, ''),
            slug: Str::limit($slug, 80, ''),
            htmlDescription: strip_tags((string) ($data['html_description'] ?? ''), '<h2><h3><p><ul><ol><li><strong><em><br>'),
            bulletPoints: $bullets,
            seoTags: $tags,
        );
    }

    /** @return list<array{title: string, description: string}> */
    public function detailHighlights(): array
    {
        return array_map(function (string $point): array {
            if (str_contains($point, ':')) {
                [$title, $desc] = array_map('trim', explode(':', $point, 2));

                return ['title' => $title, 'description' => $desc];
            }

            return ['title' => $point, 'description' => ''];
        }, $this->bulletPoints);
    }
}
