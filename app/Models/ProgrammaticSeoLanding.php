<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgrammaticSeoLanding extends Model
{
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected $fillable = [
        'slug',
        'category_id',
        'location',
        'use_case',
        'headline',
        'meta_title',
        'meta_description',
        'intro_html',
        'robots',
        'is_published',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
        ];
    }

    /** @return BelongsTo<Category, $this> */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /** @return array<string, mixed> */
    public function toSeoArray(): array
    {
        $categoryName = $this->category?->name ?? 'Beauty';

        $replacements = [
            '{location}' => (string) ($this->location ?? ''),
            '{category}' => $categoryName,
            '{use_case}' => (string) ($this->use_case ?? ''),
            '{brand}' => 'Beautage',
        ];

        $title = $this->applyTokens($this->meta_title ?: $this->headline, $replacements);
        $description = $this->applyTokens(
            $this->meta_description ?: 'Shop '.$categoryName.' at Beautage Pakistan.',
            $replacements
        );

        return [
            'title' => $title,
            'description' => $description,
            'canonical' => route('pseo.show', $this),
            'robots' => $this->robots ?: 'index,follow',
            'og_title' => $title,
            'og_description' => $description,
            'og_type' => 'website',
        ];
    }

    /** @param  array<string, string>  $tokens */
    public function applyTokens(string $text, array $tokens): string
    {
        return str_replace(array_keys($tokens), array_values($tokens), $text);
    }
}
