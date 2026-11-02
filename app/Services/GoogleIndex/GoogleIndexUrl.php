<?php

namespace App\Services\GoogleIndex;

use App\Models\BlogPost;
use App\Models\Product;
use App\Support\GoogleAnalytics;

final class GoogleIndexUrl
{
    public static function forProduct(Product $product): string
    {
        $path = route('product.show', $product, false);

        return rtrim(GoogleAnalytics::storefrontUrl(), '/').$path;
    }

    public static function forBlogPost(BlogPost $post): string
    {
        $path = route('blog.show', $post, false);

        return rtrim(GoogleAnalytics::storefrontUrl(), '/').$path;
    }

    public static function normalize(string $url): string
    {
        $url = trim($url);
        if ($url === '') {
            return $url;
        }

        if (! str_starts_with($url, 'http://') && ! str_starts_with($url, 'https://')) {
            return rtrim(GoogleAnalytics::storefrontUrl(), '/').'/'.ltrim($url, '/');
        }

        return $url;
    }
}
