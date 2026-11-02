<x-filament-panels::page>
    <div class="mb-6 rounded-2xl border border-purple-100 bg-gradient-to-r from-purple-50 via-white to-pink-50 px-5 py-4">
        <p class="text-xs font-bold uppercase tracking-wider text-purple-700">Bulk email themes</p>
        <p class="mt-1 text-sm text-slate-600">
            <strong>Store</strong> emails highlight products, bundles, and sales.
            <strong>Partner / IR</strong> emails use the corporate purple briefing theme and can pull content from partner pages.
        </p>
    </div>

    <form wire:submit.prevent="save">
        {{ $this->form }}
    </form>
</x-filament-panels::page>
