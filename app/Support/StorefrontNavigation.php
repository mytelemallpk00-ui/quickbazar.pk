<?php

namespace App\Support;

use App\Models\Category;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class StorefrontNavigation
{
    /** @return array<string, mixed> */
    public static function forLayout(): array
    {
        $config = SiteSettings::headerNavigation();
        $categories = Category::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->keyBy('id');

        return [
            'collectionsColumns' => static::resolveCategoryColumns(
                $config['collections_category_ids'] ?? [],
                $categories
            ),
            'collectionsFeatured' => static::normalizeFeatured($config['collections_featured'] ?? []),
            'mainNavItems' => static::ensureExclusiveOffersNavItem(
                static::normalizeMainNavConfig(
                    static::resolveMainNavItems(
                        $config['main_nav_items'] ?? [],
                        $categories
                    )
                )
            ),
        ];
    }

    /** @param  array<int, array<string, mixed>>  $items */
    /** @return array<int, array<string, mixed>> */
    protected static function ensureExclusiveOffersNavItem(array $items): array
    {
        $offersUrl = route('exclusive-offers');
        $found = false;

        foreach ($items as $index => $item) {
            $label = trim((string) ($item['label'] ?? ''));
            $url = (string) ($item['url'] ?? '');
            $isOffers = ($item['type'] ?? '') === 'bundles'
                || strcasecmp($label, 'Exclusive Offers') === 0
                || str_contains($url, 'exclusive-offers');

            if (! $isOffers) {
                continue;
            }

            $found = true;
            $items[$index]['type'] = 'custom';
            $items[$index]['label'] = $label !== '' ? $label : 'Exclusive Offers';
            $items[$index]['icon'] = (string) ($item['icon'] ?? 'flame');
            $items[$index]['icon_color'] = (string) ($item['icon_color'] ?? 'text-beautage-pink');
            $items[$index]['url'] = $offersUrl;
            $items[$index]['highlight'] = true;
            $items[$index]['has_mega'] = false;
        }

        if (! $found) {
            $items[] = static::exclusiveOffersNavItem();
        }

        return $items;
    }

    /** @param  array<int, array<string, mixed>>  $items */
    /** @return array<int, array<string, mixed>> */
    protected static function normalizeMainNavConfig(array $items): array
    {
        foreach ($items as $index => $item) {
            $label = trim((string) ($item['label'] ?? ''));
            if (strcasecmp($label, 'All Flagships') === 0) {
                $items[$index] = static::homeNavItem();
            }
        }

        return $items;
    }

    /** @return array<string, mixed> */
    protected static function homeNavItem(): array
    {
        return [
            'type' => 'custom',
            'label' => 'Home',
            'icon' => 'home',
            'icon_color' => 'text-beautage-amber',
            'url' => route('home'),
            'highlight' => false,
            'has_mega' => false,
            'has_dropdown' => false,
        ];
    }

    /** @return array<string, mixed> */
    protected static function exclusiveOffersNavItem(): array
    {
        return [
            'type' => 'custom',
            'label' => 'Exclusive Offers',
            'icon' => 'flame',
            'icon_color' => 'text-beautage-pink',
            'url' => route('exclusive-offers'),
            'highlight' => true,
            'has_mega' => false,
        ];
    }

    /** @param  array<int, int|string>  $ids */
    /** @param  Collection<int, Category>  $categories */
    /** @return array<int, array<string, mixed>> */
    protected static function resolveCategoryColumns(array $ids, Collection $categories): array
    {
        $columns = [];
        foreach ($ids as $id) {
            $category = $categories->get((int) $id);
            if (! $category) {
                continue;
            }
            $columns[] = static::categoryToColumn($category);
        }

        return $columns;
    }

    /** @return array<string, mixed> */
    protected static function categoryToColumn(Category $category): array
    {
        $links = $category->resolvedNavLinks();
        if ($links === []) {
            $links = [
                ['label' => 'Shop all '.$category->displayNavLabel(), 'url' => $category->collectionsUrl()],
            ];
        }

        return [
            'title' => $category->displayNavLabel(),
            'icon' => $category->iconName(),
            'icon_color' => $category->nav_icon_color ?: 'text-beautage-pink',
            'description' => Str::limit(strip_tags((string) ($category->description ?? '')), 100),
            'links' => array_slice($links, 0, 5),
            'url' => $category->collectionsUrl(),
        ];
    }

    /** @param  array<int, array<string, mixed>>  $items */
    /** @param  Collection<int, Category>  $categories */
    /** @return array<int, array<string, mixed>> */
    protected static function resolveMainNavItems(array $items, Collection $categories): array
    {
        $resolved = [];
        foreach ($items as $item) {
            $type = $item['type'] ?? 'category';
            if ($type === 'home') {
                $resolved[] = static::homeNavItem();

                continue;
            }

            if ($type === 'bundles') {
                $resolved[] = static::exclusiveOffersNavItem();

                continue;
            }

            if ($type === 'custom') {
                $url = trim((string) ($item['url'] ?? '/'));
                if ($url !== '' && ! str_starts_with($url, 'http') && ! str_starts_with($url, '/')) {
                    $url = '/'.$url;
                }
                if (strcasecmp((string) ($item['label'] ?? ''), 'All Flagships') === 0) {
                    $resolved[] = static::homeNavItem();

                    continue;
                }

                $resolved[] = [
                    'type' => 'custom',
                    'label' => (string) ($item['label'] ?? 'Link'),
                    'icon' => (string) ($item['icon'] ?? 'link'),
                    'icon_color' => (string) ($item['icon_color'] ?? 'text-slate-600'),
                    'url' => $url,
                    'highlight' => (bool) ($item['highlight'] ?? false),
                    'has_mega' => false,
                    'has_dropdown' => false,
                ];

                continue;
            }

            $category = $categories->get((int) ($item['category_id'] ?? 0));
            if (! $category) {
                continue;
            }

            $links = $category->resolvedNavLinks();
            $showDropdown = filter_var($item['show_dropdown'] ?? true, FILTER_VALIDATE_BOOLEAN);

            $resolved[] = [
                'type' => 'category',
                'label' => $category->displayNavLabel(),
                'icon' => $category->iconName(),
                'icon_color' => $category->nav_icon_color ?: 'text-beautage-purple',
                'url' => $category->collectionsUrl(),
                'highlight' => false,
                'has_mega' => false,
                'has_dropdown' => $showDropdown && count($links) > 0,
                'dropdown_links' => $links,
            ];
        }

        return $resolved;
    }

    /** @param  array<string, mixed>  $featured */
    /** @return array<string, mixed> */
    protected static function normalizeFeatured(array $featured): array
    {
        return [
            'badge' => (string) ($featured['badge'] ?? 'Featured Innovation'),
            'title' => (string) ($featured['title'] ?? 'Explore Beautage'),
            'text' => (string) ($featured['text'] ?? 'Discover our curated flagship collections.'),
            'button_label' => (string) ($featured['button_label'] ?? 'Shop collections'),
            'button_url' => (string) ($featured['button_url'] ?? route('collections')),
        ];
    }
}
