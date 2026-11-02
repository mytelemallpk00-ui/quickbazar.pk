<x-filament-panels::page>
    <div class="space-y-6">
        {!! $this->getHeaderWidgetsProperty() !!}

        @if ($claudePreview)
            <div class="rounded-xl border border-info-300 bg-info-50 dark:bg-info-950/30 p-4 text-sm">
                <p class="font-semibold text-info-900 dark:text-info-100">Latest Claude SEO insight — score {{ $claudePreview['score'] }}/100</p>
                <p class="mt-1 text-info-800 dark:text-info-200">{{ $claudePreview['summary'] }}</p>
                @if (! empty($claudePreview['suggestions']))
                    <ul class="mt-2 list-disc pl-5 text-info-800 dark:text-info-200">
                        @foreach ($claudePreview['suggestions'] as $tip)
                            <li>{{ $tip }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @endif

        <form wire:submit.prevent="pushCustomUrl" class="space-y-4">
            {{ $this->form }}

            <div class="flex flex-wrap items-center gap-3">
                <x-filament::button type="submit" icon="heroicon-o-rocket-launch" wire:loading.attr="disabled" wire:target="pushCustomUrl">
                    <span wire:loading.remove wire:target="pushCustomUrl">Push custom URL</span>
                    <span wire:loading wire:target="pushCustomUrl" class="inline-flex items-center gap-2">
                        <x-filament::loading-indicator class="h-4 w-4" />
                        Sending…
                    </span>
                </x-filament::button>

                <x-filament::button type="button" color="success" icon="heroicon-o-queue-list" wire:click="runBulkIndexing" wire:loading.attr="disabled" wire:target="runBulkIndexing,bulkPushSelected">
                    <span wire:loading.remove wire:target="runBulkIndexing,bulkPushSelected">Run bulk indexing</span>
                    <span wire:loading wire:target="runBulkIndexing,bulkPushSelected" class="inline-flex items-center gap-2">
                        <x-filament::loading-indicator class="h-4 w-4" />
                        Bulk push in progress…
                    </span>
                </x-filament::button>
            </div>
        </form>

        @if ($bulkSummary)
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4 text-sm">
                <p class="font-semibold text-gray-900 dark:text-gray-100">Last bulk run</p>
                <p class="mt-1 text-gray-600 dark:text-gray-300">
                    Success: {{ $bulkSummary['success'] }} · Failed: {{ $bulkSummary['failed'] }}
                    @if (($bulkSummary['skipped'] ?? 0) > 0)
                        · Skipped: {{ $bulkSummary['skipped'] }}
                    @endif
                </p>
                @if (! empty($bulkSummary['stopped_reason']))
                    <p class="mt-1 text-warning-600 dark:text-warning-400">{{ $bulkSummary['stopped_reason'] }}</p>
                @endif
            </div>
        @endif

        <div wire:loading.class="opacity-60 pointer-events-none" wire:target="pushCustomUrl,pushGoogle,removeGoogle,claudeReview,runBulkIndexing,bulkPushSelected">
            {{ $this->table }}
        </div>

        <section class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-4 py-3 bg-gray-50 dark:bg-gray-800/80 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Recent indexing attempts</h3>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse ($this->recentLogs as $log)
                    <div class="px-4 py-3 text-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div class="min-w-0">
                            <p class="font-mono text-gray-700 dark:text-gray-300 truncate">{{ $log->url }}</p>
                            <p class="text-gray-500 dark:text-gray-400 mt-0.5">{{ $log->action }} · {{ $log->created_at?->format('M j, Y g:i A') }}</p>
                        </div>
                        <span @class([
                            'inline-flex shrink-0 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide',
                            'bg-success-100 text-success-700' => $log->isSuccess(),
                            'bg-danger-100 text-danger-700' => ! $log->isSuccess(),
                        ])>
                            {{ $log->isSuccess() ? 'Success' : 'Failed' }}
                        </span>
                    </div>
                @empty
                    <p class="px-4 py-6 text-sm text-gray-500">No indexing requests yet.</p>
                @endforelse
            </div>
        </section>
    </div>
</x-filament-panels::page>
