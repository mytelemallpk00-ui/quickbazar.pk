<?php

namespace App\Models\Concerns;

trait HasSeoMeta
{
    public function initializeHasSeoMeta(): void
    {
        $this->fillable = array_values(array_unique(array_merge(
            $this->fillable,
            static::seoFillable(),
        )));
    }

    /** @return list<string> */
    public static function seoFillable(): array
    {
        return [
            'meta_title',
            'meta_description',
            'meta_keywords',
            'og_title',
            'og_description',
            'og_image',
            'canonical_url',
            'robots',
            'seo_focus_keyword',
        ];
    }

    /** @return array<string, mixed> */
    public function toSeoArray(): array
    {
        $title = filled($this->meta_title)
            ? $this->meta_title
            : (filled($this->seo_title ?? null)
                ? $this->seo_title
                : ($this->title ?? $this->name ?? config('app.name')));

        $description = $this->meta_description
            ?: ($this->snippet ?? $this->excerpt ?? $this->description ?? null);

        if (is_string($description) && strlen(strip_tags($description)) > 160) {
            $description = str(strip_tags($description))->limit(160)->toString();
        }

        return [
            'title' => $title,
            'description' => $description,
            'keywords' => $this->meta_keywords,
            'og_title' => $this->og_title ?: $title,
            'og_description' => $this->og_description ?: $description,
            'og_image' => $this->og_image ?: ($this->image ?? $this->hero_image ?? null),
            'canonical' => $this->canonical_url,
            'robots' => $this->robots ?: 'index,follow',
            'focus_keyword' => $this->seo_focus_keyword,
        ];
    }
}
