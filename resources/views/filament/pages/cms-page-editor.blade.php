<x-filament-panels::page>
    <div class="beautage-ir-admin-header mb-6 flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-purple-100 bg-gradient-to-r from-purple-50 to-white px-5 py-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-wider text-purple-600">Storefront page</p>
            <p class="text-sm text-slate-600">Edit hero, body copy, and SEO. This page is linked from the footer Partner hub.</p>
        </div>
        <a href="{{ $this->getLivePreviewUrl() }}" target="_blank" rel="noopener noreferrer"
           class="inline-flex items-center gap-2 rounded-xl bg-[#5E1F70] px-4 py-2.5 text-xs font-bold uppercase tracking-wide text-white shadow-sm hover:bg-purple-900 transition">
            View live page
            <x-filament::icon icon="heroicon-o-arrow-top-right-on-square" class="h-4 w-4" />
        </a>
    </div>

    <form wire:submit="save">
        {{ $this->form }}

        <div class="mt-6 flex justify-end">
            <x-filament::button type="submit" size="lg">
                Save page
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
