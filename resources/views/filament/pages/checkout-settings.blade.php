<x-filament-panels::page>
    <div class="beautage-page-intro">
        <p class="beautage-page-intro-text">
            Control <strong>delivery fees</strong>, <strong>payment methods</strong>, tax rate, and checkout page text.
            Changes apply to <a href="{{ route('checkout') }}" target="_blank" rel="noopener noreferrer" class="text-primary-600 font-semibold hover:underline">/checkout</a> immediately after save.
        </p>
    </div>

    <form wire:submit="save" class="beautage-settings-form">
        {{ $this->form }}

        <div class="beautage-form-actions">
            <x-filament::button type="submit" size="lg">
                Save checkout settings
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
