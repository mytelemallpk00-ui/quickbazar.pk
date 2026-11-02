<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('social:publish-posts')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();

Schedule::command('store:automations')
    ->everyFifteenMinutes()
    ->withoutOverlapping()
    ->runInBackground();

Schedule::command('seo:generate-sitemap')
    ->hourly()
    ->withoutOverlapping()
    ->runInBackground();
