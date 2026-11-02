<x-filament-panels::page>
    <form wire:submit="save" class="beautage-settings-form space-y-6">
        {{ $this->form }}

        <div class="beautage-form-actions">
            <x-filament::button type="submit" size="lg">
                Save header menu
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
