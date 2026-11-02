<x-filament-panels::page>
    <div class="beautage-page-intro mb-4">
        <p class="beautage-page-intro-text">
            Har platform ka token yahan save hota hai (<strong>encrypted</strong>). Purana token rehne dein — sirf naya value likh kar <strong>Save</strong> karein.
            Green tick = posting ke liye ready.
        </p>
    </div>

    <form wire:submit="save" class="beautage-settings-form">
        {{ $this->form }}

        <div class="beautage-form-actions">
            <x-filament::button type="submit" size="lg" icon="heroicon-o-check">
                Save connections
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
