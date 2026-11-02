<x-filament-panels::page>
    <div class="beautage-page-intro mb-4">
        <p class="beautage-page-intro-text">
            Credentials encrypted save hoti hain. Har courier ke liye merchant API keys lagayein — order place hote hi booking job chalti hai aur tracking ID order par save hoti hai.
        </p>
    </div>

    <form wire:submit="save" class="beautage-settings-form">
        {{ $this->form }}

        <div class="beautage-form-actions">
            <x-filament::button type="submit" size="lg" icon="heroicon-o-check">
                Save courier settings
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
