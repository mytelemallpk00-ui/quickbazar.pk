<x-filament-panels::page>
    @php
        $stats = $this->getQueueStats();
        $connections = $this->getConnectionSummary();
    @endphp

    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/40 p-4 mb-4 flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                Accounts: {{ $connections['ready_count'] }} / {{ $connections['total'] }} connected
            </p>
            <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">
                @if($connections['ready_count'] < $connections['total'])
                    Baqi platforms ke liye Connect accounts kholen.
                @else
                    Sab platforms ready — schedule bana sakte hain.
                @endif
            </p>
        </div>
        <x-filament::button tag="a" href="{{ \App\Filament\Pages\SocialApiCredentials::getUrl() }}" size="sm" color="gray" outlined>
            Manage connections
        </x-filament::button>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
        <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Queue mein</p>
            <p class="text-2xl font-bold text-amber-600 mt-1">{{ $stats['pending'] }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Publish ho chuki</p>
            <p class="text-2xl font-bold text-success-600 mt-1">{{ $stats['published'] }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Error / retry</p>
            <p class="text-2xl font-bold text-danger-600 mt-1">{{ $stats['failed'] }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Agli post</p>
            <p class="text-sm font-bold text-gray-900 dark:text-gray-100 mt-2 leading-snug">{{ $stats['next_label'] }}</p>
        </div>
    </div>

    <div class="beautage-page-intro mb-4">
        <p class="beautage-page-intro-text">
            Pehle <a href="{{ \App\Filament\Pages\SocialApiCredentials::getUrl() }}" class="text-primary-600 font-semibold hover:underline">social accounts connect</a> karein,
            phir neeche category choose karke AI captions schedule karein — posting automatic ho jati hai.
        </p>
    </div>

    <form wire:submit="generateSchedule" class="beautage-settings-form space-y-6">
        {{ $this->form }}

        <div class="beautage-form-actions flex flex-wrap items-center gap-3">
            <x-filament::button type="submit" size="lg" icon="heroicon-o-sparkles">
                Schedule bana dein (AI captions)
            </x-filament::button>
            <x-filament::button tag="a" href="{{ \App\Filament\Pages\SocialApiCredentials::getUrl() }}" color="gray" outlined>
                Connect accounts
            </x-filament::button>
        </div>
    </form>

    <div class="mt-10">
        <h2 class="text-base font-bold text-gray-900 dark:text-gray-100 mb-3">Your scheduled posts</h2>
        {{ $this->table }}
    </div>
</x-filament-panels::page>
