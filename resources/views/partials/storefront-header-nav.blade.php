@php
    $nav = $storefrontNav ?? [];
    $columns = $nav['collectionsColumns'] ?? [];
    $barItems = $nav['mainNavItems'] ?? [];
    $buttonLabel = $nav['collectionsButton'] ?? 'Collections & categories';
    $featured = $nav['collectionsFeatured'] ?? [];
@endphp
<div id="storefront-nav-backdrop" aria-hidden="true"></div>
<div id="storefront-category-strip" class="qb-nav w-full">
    <nav class="flex w-full items-center gap-2 px-4 sm:px-6 lg:px-8" aria-label="Departments">
        <div class="group relative shrink-0" data-nav-dropdown="collections">
            <button type="button" class="qb-collections" data-nav-dropdown-toggle aria-expanded="false" aria-controls="mega-collections">
                <i data-lucide="layout-grid" class="h-4 w-4"></i>
                <span>{{ $buttonLabel }}</span>
                <i data-lucide="chevron-down" class="collections-chevron h-4 w-4"></i>
            </button>
            <div id="mega-collections" class="nav-dropdown-panel mega-menu-content qb-mega qb-collections-menu hidden lg:block" role="region" aria-label="{{ $buttonLabel }}">
                <div class="qb-mega-card qb-collections-card">
                    @if ($columns === [])
                        <p class="text-sm text-slate-500">Add categories under Admin → Header menu to fill this panel.</p>
                    @else
                        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach ($columns as $column)
                                <div>
                                    <a href="{{ $column['url'] }}" class="mb-3 flex items-center gap-2 text-[12px] font-extrabold uppercase tracking-wide text-slate-900">
                                        <i data-lucide="{{ $column['icon'] }}" class="h-4 w-4 {{ $column['icon_color'] }}"></i>
                                        {{ $column['title'] }}
                                    </a>
                                    <ul class="space-y-1">
                                        @foreach ($column['links'] as $link)
                                            <li>
                                                <a href="{{ $link['url'] }}" class="flex items-center gap-2 rounded-lg px-1 py-1.5 text-sm font-semibold text-slate-600 hover:bg-orange-50 hover:text-slate-900">
                                                    <i data-lucide="{{ $column['icon'] }}" class="h-3.5 w-3.5 {{ $column['icon_color'] }}"></i>
                                                    {{ $link['label'] }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    @if (! empty($nav['showFeatured']) && filled($featured['title'] ?? null))
                        <aside class="mt-5 rounded-xl bg-[#0b1220] p-4 text-white">
                            @if (filled($featured['badge'] ?? null))
                                <p class="text-[11px] font-extrabold uppercase tracking-wide text-orange-300">{{ $featured['badge'] }}</p>
                            @endif
                            <p class="mt-1 text-base font-extrabold">{{ $featured['title'] }}</p>
                            @if (filled($featured['text'] ?? null))
                                <p class="mt-1 text-sm text-slate-300">{{ $featured['text'] }}</p>
                            @endif
                            @if (filled($featured['button_label'] ?? null) && filled($featured['button_url'] ?? null))
                                <a href="{{ $featured['button_url'] }}" class="mt-3 inline-flex rounded-lg bg-orange-500 px-3 py-2 text-sm font-bold text-white hover:bg-orange-600">{{ $featured['button_label'] }}</a>
                            @endif
                        </aside>
                    @endif
                </div>
            </div>
        </div>

        <div class="flex min-w-0 flex-1 items-center gap-0.5 overflow-x-auto no-scrollbar">
            @foreach ($barItems as $item)
                <a href="{{ $item['url'] }}" class="{{ ! empty($item['highlight']) ? 'qb-nav-hot' : 'qb-nav-link' }}">
                    @if (filled($item['icon'] ?? null))
                        <i data-lucide="{{ $item['icon'] }}" class="h-4 w-4 {{ $item['icon_color'] ?? '' }}"></i>
                    @endif
                    {{ $item['label'] }}
                </a>
            @endforeach
        </div>
    </nav>
</div>
