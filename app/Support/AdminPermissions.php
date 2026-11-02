<?php

namespace App\Support;

class AdminPermissions
{
    public const OVERVIEW = 'overview';

    public const CATEGORIES = 'categories';

    public const CATALOG = 'catalog';

    public const BUNDLES = 'bundles';

    public const CONTENT = 'content';

    public const ORDERS = 'orders';

    public const REVIEWS = 'reviews';

    public const SETTINGS = 'settings';

    public const SOCIAL = 'social';

    /** @return array<string, string> */
    public static function labels(): array
    {
        return [
            self::OVERVIEW => 'Dashboard & overview',
            self::CATEGORIES => 'Categories & navigation',
            self::CATALOG => 'Catalog (products & bulk import)',
            self::BUNDLES => 'Bundle deals',
            self::CONTENT => 'Content (pages, blog, careers, partner hub)',
            self::ORDERS => 'Orders',
            self::REVIEWS => 'Customer reviews',
            self::SETTINGS => 'Site settings (theme, homepage, SEO, analytics — not user management)',
            self::SOCIAL => 'Social automation (scheduler, API credentials, auto-posting)',
        ];
    }

    /** @return list<string> */
    public static function keys(): array
    {
        return array_keys(static::labels());
    }

    /** @param  list<string>|null  $permissions */
    public static function normalize(?array $permissions): array
    {
        $allowed = static::keys();
        $permissions = array_values(array_unique(array_filter(
            $permissions ?? [],
            fn ($key) => is_string($key) && in_array($key, $allowed, true),
        )));

        if ($permissions !== [] && ! in_array(self::OVERVIEW, $permissions, true)) {
            array_unshift($permissions, self::OVERVIEW);
        }

        return $permissions;
    }
}
