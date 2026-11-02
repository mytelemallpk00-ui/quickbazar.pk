<?php

namespace App\Console\Commands;

use App\Services\Seo\SitemapGenerator;
use Illuminate\Console\Command;

class GenerateSitemapCommand extends Command
{
    protected $signature = 'seo:generate-sitemap';

    protected $description = 'Regenerate sitemap_index.xml and chunked sitemap-NNNN.xml files (max 50k URLs each)';

    public function handle(SitemapGenerator $generator): int
    {
        $count = $generator->generate();
        $this->info($count > 0
            ? 'Sitemaps written: '.SitemapGenerator::indexUrl().' ('.$count.' file(s))'
            : 'Sitemap generation skipped (disabled in admin).');

        return self::SUCCESS;
    }
}
