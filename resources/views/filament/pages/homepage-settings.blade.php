<x-filament-panels::page>
    <div class="beautage-page-intro">
        <p class="beautage-page-intro-text">
            Manage homepage hero banner, side promo cards, trust badges, and category tile images — matching the top section of your storefront.
        </p>
    </div>

    <form wire:submit="save" class="beautage-settings-form">
        {{ $this->form }}

        <div class="beautage-form-actions">
            <x-filament::button type="submit" size="lg">
                Save homepage
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
