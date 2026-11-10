<?php

namespace App\Providers;

use App\Models\Product;
use App\Observers\ProductSeoObserver;
use App\Support\CheckoutSettings;
use App\Support\StorefrontNavigation;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.app', function ($view): void {
            $view->with('storefrontNav', StorefrontNavigation::forLayout());
        });

        View::composer(['checkout', 'pages.shipping'], function ($view): void {
            if (! $view->offsetExists('checkoutConfig')) {
                $view->with('checkoutConfig', CheckoutSettings::forStorefront());
            }
            $view->with('freeShippingThreshold', (float) CheckoutSettings::all()['free_shipping_threshold']);
        });

        if (Schema::hasTable('products')) {
            Product::observe(ProductSeoObserver::class);
        }
    }
}
