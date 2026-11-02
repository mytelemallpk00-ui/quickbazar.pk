<x-filament-panels::page>
    <div class="beautage-page-intro">
        <p class="beautage-page-intro-text">
            Logo, favicon, brand identity, theme colors, marquee, and default SEO. For homepage hero and category images, use Settings → Homepage banners.
        </p>
    </div>

    <form wire:submit="save" class="beautage-settings-form">
        {{ $this->form }}

        <div class="beautage-form-actions">
            <x-filament::button type="submit" size="lg">
                Save all settings
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
