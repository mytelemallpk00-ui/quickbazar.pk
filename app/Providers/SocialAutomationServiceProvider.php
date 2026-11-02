<?php

namespace App\Providers;

use App\Services\Social\FacebookService;
use App\Services\Social\InstagramService;
use App\Services\Social\LinkedInService;
use App\Services\Social\SocialPostPublisher;
use App\Services\Social\TelegramService;
use App\Services\Social\YouTubeService;
use Illuminate\Support\ServiceProvider;

class SocialAutomationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SocialPostPublisher::class, function ($app) {
            return new SocialPostPublisher([
                'facebook' => $app->make(FacebookService::class),
                'instagram' => $app->make(InstagramService::class),
                'linkedin' => $app->make(LinkedInService::class),
                'youtube' => $app->make(YouTubeService::class),
                'telegram' => $app->make(TelegramService::class),
            ]);
        });
    }
}
