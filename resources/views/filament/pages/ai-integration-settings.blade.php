<x-filament-panels::page>
    <form wire:submit="save" class="beautage-settings-form">
        {{ $this->form }}

        <div class="beautage-form-actions">
            <x-filament::button type="submit" size="lg">
                Save AI keys
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
