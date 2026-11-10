<x-filament-panels::page>
    <div class="beautage-page-intro mb-4">
        <p class="beautage-page-intro-text">
            Professional lifecycle emails for your storefront: <strong>abandoned cart recovery</strong>,
            <strong>post-purchase upsell sequences</strong>, <strong>smart re-order reminders</strong>, and
            <strong>daily low-stock alerts</strong> to your team. Templates match the purple &amp; pink Quick Bazar mail theme.
        </p>
    </div>

    <form wire:submit="save" class="beautage-settings-form space-y-4">
        {{ $this->form }}

        <div class="beautage-form-actions flex flex-wrap gap-3">
            <x-filament::button type="submit" size="lg" icon="heroicon-o-check">
                Save automations
            </x-filament::button>
            <x-filament::button type="button" color="gray" size="lg" icon="heroicon-o-play" wire:click="runNow">
                Run queue now
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
