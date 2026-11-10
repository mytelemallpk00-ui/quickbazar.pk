<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CheckoutShippingController;
use App\Http\Controllers\Customer\AccountController;
use App\Http\Controllers\Customer\Auth\LoginController;
use App\Http\Controllers\Customer\Auth\RegisterController;
use App\Http\Controllers\TrackOrderController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\DeployFlushController;
use App\Http\Controllers\ExclusiveOffersController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsletterSubscribeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductReviewController;
use Illuminate\Support\Facades\Route;

Route::get('/robots.txt', function () {
    $base = rtrim((string) (config('app.url') ?: url('/')), '/');

    $lines = [
        'User-agent: *',
        'Allow: /',
        'Disallow: /admin',
        'Disallow: /checkout',
        'Disallow: /account',
        '',
        'Sitemap: '.$base.'/sitemap_index.xml',
        '',
    ];

    return response(implode("\n", $lines), 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
})->name('robots');

Route::get('/_deploy/flush-cache', DeployFlushController::class)
    ->middleware('throttle:6,1');

Route::middleware('web')->group(function (): void {
    Route::get('/', HomeController::class)->name('home');

    Route::get('/collections', CollectionController::class)->name('collections');
    Route::get('/shop/{landing:slug}', \App\Http\Controllers\ProgrammaticSeoLandingController::class)
        ->name('pseo.show');

    Route::get('/exclusive-offers', ExclusiveOffersController::class)->name('exclusive-offers');

    Route::get('/products/{product:slug}', [ProductController::class, 'show'])
        ->name('product.show');

    Route::post('/products/{product:slug}/reviews', [ProductReviewController::class, 'store'])
        ->name('product.reviews.store');

    Route::get('/checkout', CheckoutController::class)->name('checkout');
    Route::post('/checkout/shipping-rates', CheckoutShippingController::class)->name('checkout.shipping-rates');
    Route::post('/checkout/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::post('/store/cart/sync', \App\Http\Controllers\Store\AbandonedCartSyncController::class)
        ->middleware('throttle:40,1')
        ->name('store.cart.sync');
    Route::get('/orders/track', TrackOrderController::class)->name('orders.track');

    Route::post('/api/chat/send', [ChatController::class, 'send'])
        ->middleware('throttle:60,1')
        ->name('chat.send');

    Route::get('/api/chat/history', [ChatController::class, 'history'])
        ->middleware('throttle:120,1')
        ->name('chat.history');

    Route::middleware('guest')->group(function (): void {
        Route::get('/login', [LoginController::class, 'create'])->name('login');
        Route::post('/login', [LoginController::class, 'store'])->name('login.store');
        Route::get('/register', [RegisterController::class, 'create'])->name('register');
        Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
    });

    Route::middleware('auth')->group(function (): void {
        Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
        Route::get('/account', [AccountController::class, 'index'])->name('account.index');
        Route::get('/account/orders/{order:order_number}', [AccountController::class, 'show'])->name('account.orders.show');
    });

    Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');

    Route::get('/blog/{post:slug}', [BlogController::class, 'show'])->name('blog.show');

    Route::view('/our-story', 'pages.our-story')->name('our-story');
    Route::view('/careers', 'pages.careers')->name('careers');
    Route::view('/press', 'pages.press')->name('press');
    Route::view('/help', 'pages.help')->name('help');
    Route::view('/returns', 'pages.returns')->name('returns');
    Route::view('/shipping', 'pages.shipping')->name('shipping');
    Route::view('/size-guide', 'pages.size-guide')->name('size-guide');
    Route::view('/track-order', 'pages.track-order')->name('track-order');

    Route::get('/press-releases', fn (PageController $controller) => $controller->showBySlug('press-releases'))
        ->name('press-releases');
    Route::get('/faq', fn (PageController $controller) => $controller->showBySlug('faq'))
        ->name('faq');
    Route::get('/events', fn (PageController $controller) => $controller->showBySlug('events'))
        ->name('events');

    Route::post('/newsletter/subscribe', NewsletterSubscribeController::class)
        ->middleware('throttle:20,1')
        ->name('newsletter.subscribe');

    Route::redirect('/investor-relations', '/', 301);
    Route::redirect('/investor-portal', '/', 301);
    Route::any('/investor/{path?}', fn () => abort(404))
        ->where('path', '.*')
        ->name('investor.removed');
});
