<?php

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$fetcher = app(\App\Services\ProductImport\CategoryListingFetcher::class);
$url = 'https://rozzana.pk/collections/all';

foreach (['shopify', 'daraz'] as $source) {
    $urls = $fetcher->discoverProductUrls($source, $url, 5, false);
    echo $source.': '.count($urls).PHP_EOL;
    if ($urls !== []) {
        echo '  first: '.$urls[0].PHP_EOL;
    }
}
