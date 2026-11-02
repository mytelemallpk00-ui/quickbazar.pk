<?php

namespace App\Models;

use App\Models\Concerns\HasSeoMeta;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasSeoMeta;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image_url',
        'icon',
        'nav_icon_color',
        'nav_label',
        'nav_links',
        'mega_promo_badge',
        'mega_promo_title',
        'mega_promo_text',
        'mega_promo_button_label',
        'mega_promo_button_url',
        'mega_promo_style',
        'is_featured',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'nav_links' => 'array',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** @return HasMany<Product, $this> */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    public function displayNavLabel(): string
    {
        return $this->nav_label ?: $this->name;
    }

    public function collectionsUrl(): string
    {
        return route('collections', ['cat' => $this->slug]);
    }

    public function iconName(): string
    {
        return $this->icon ?: 'tag';
    }

    protected static function booted(): void
    {
        static::saving(function (Category $category): void {
            if (blank($category->slug) && filled($category->name)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    /** @return array<int, array{label: string, url: string}> */
    public function resolvedNavLinks(): array
    {
        $links = [];
        foreach ($this->nav_links ?? [] as $link) {
            $label = trim((string) ($link['label'] ?? ''));
            if ($label === '') {
                continue;
            }
            $url = trim((string) ($link['url'] ?? ''));
            if ($url === '') {
                $url = $this->collectionsUrl();
            } elseif (! str_starts_with($url, 'http') && ! str_starts_with($url, '/')) {
                $url = route('collections', ['cat' => $url]);
            }

            $links[] = ['label' => $label, 'url' => $url];
        }

        if ($links === []) {
            $links[] = [
                'label' => 'Shop '.$this->name,
                'url' => $this->collectionsUrl(),
            ];
        }

        return $links;
    }
}
