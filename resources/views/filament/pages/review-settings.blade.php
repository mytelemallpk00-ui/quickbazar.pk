<x-filament-panels::page>
    <div class="beautage-page-intro">
        <p class="beautage-page-intro-text">
            Choose whether customer reviews (and uploaded photos) publish instantly or wait for your approval in <strong>All reviews</strong>.
        </p>
    </div>

    <form wire:submit="save" class="beautage-settings-form">
        {{ $this->form }}

        <div class="beautage-form-actions">
            <x-filament::button type="submit" size="lg">
                Save review settings
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
