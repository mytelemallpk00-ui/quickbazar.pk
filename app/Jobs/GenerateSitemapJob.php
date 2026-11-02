<?php

namespace App\Jobs;

use App\Services\Seo\SitemapGenerator;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateSitemapJob implements ShouldQueue
{
    use Queueable;

    public function handle(SitemapGenerator $generator): void
    {
        $generator->generate();
    }
}
