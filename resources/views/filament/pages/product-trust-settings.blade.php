<x-filament-panels::page>
    <div class="beautage-page-intro">
        <p class="beautage-page-intro-text">
            Manage the <strong>Product Trust & Authenticity</strong> and <strong>Quality Promise</strong> blocks shown in the third column on desktop product pages.
        </p>
    </div>

    <form wire:submit="save" class="beautage-settings-form">
        {{ $this->form }}

        <div class="beautage-form-actions">
            <x-filament::button type="submit" size="lg">
                Save Trust &amp; Authenticity
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
