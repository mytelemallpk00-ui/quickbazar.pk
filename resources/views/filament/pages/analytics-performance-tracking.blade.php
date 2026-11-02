<x-filament-panels::page>
    <div class="beautage-page-intro">
        <p class="beautage-page-intro-text">
            GA4, Meta Pixel, webmaster tools (Google, Bing/Yahoo, Yandex), aur Google Shopping feed — sab yahan se connect karein. Save ke baad storefront par <strong>dataLayer + e-commerce events</strong> (View Item, Add to Cart, Checkout, Purchase) automatic chalte hain. Koi theme code edit nahi.
        </p>
    </div>

    <form wire:submit="save" class="beautage-settings-form">
        {{ $this->form }}

        <div class="beautage-form-actions">
            <x-filament::button type="submit" size="lg" icon="heroicon-o-check">
                Save analytics settings
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
