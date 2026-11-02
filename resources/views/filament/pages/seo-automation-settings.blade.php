<x-filament-panels::page>
    <div class="beautage-page-intro mb-4">
        <p class="beautage-page-intro-text">
            Sitemap regeneration runs <strong>hourly</strong> via <code>seo:generate-sitemap</code> and after catalog changes (delayed job).
            Submit <code>{{ \App\Services\Seo\SitemapGenerator::indexUrl() }}</code> in Search Console
            (auto-splits at <strong>50,000 URLs</strong> per <code>sitemap-0001.xml</code>, …).
        </p>
    </div>

    <form wire:submit="save" class="beautage-settings-form space-y-4">
        {{ $this->form }}

        <div class="beautage-form-actions flex flex-wrap gap-3">
            <x-filament::button type="submit" size="lg">Save settings</x-filament::button>
            <x-filament::button type="button" color="gray" size="lg" wire:click="regenerateSitemap">
                Regenerate sitemap now
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
