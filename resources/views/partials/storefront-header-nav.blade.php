@php
    $nav = $storefrontNav ?? ['collectionsColumns' => [], 'collectionsFeatured' => [], 'mainNavItems' => []];
    $collectionsColumns = $nav['collectionsColumns'] ?? [];
    $collectionsFeatured = $nav['collectionsFeatured'] ?? [];
    $mainNavItems = $nav['mainNavItems'] ?? [];
    $columnCount = count($collectionsColumns);
@endphp
<div id="storefront-nav-backdrop" aria-hidden="true"></div>
<div id="storefront-category-strip" class="border-t border-slate-200 bg-white overflow-x-auto lg:overflow-visible no-scrollbar w-full max-w-full relative z-30">
    <nav class="w-full max-w-full px-4 sm:px-12 xl:px-20 flex items-center justify-between gap-2 sm:gap-3 py-2.5 text-xs font-semibold whitespace-nowrap relative">

        <div class="flex items-center justify-start xl:justify-center gap-1 sm:gap-3 min-w-0 flex-1 overflow-x-auto lg:overflow-visible no-scrollbar">

        <div class="relative shrink-0 group max-lg:flex-1" data-nav-dropdown="collections">
            <button
                type="button"
                data-nav-dropdown-toggle
                aria-expanded="false"
                aria-controls="collections-mega-panel"
                aria-haspopup="true"
                class="flex items-center justify-center gap-2 w-full lg:w-auto bg-emerald-600 text-white hover:bg-emerald-700 text-xs font-bold uppercase tracking-wider px-3.5 py-2.5 rounded-lg transition shadow-sm min-h-[44px]"
            >
                <i data-lucide="layout-grid" class="w-4 h-4 text-emerald-100"></i>
                <span>Shop</span>
                <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-emerald-100 transition-transform collections-chevron"></i>
            </button>

            @if ($columnCount > 0)
            <div
                id="collections-mega-panel"
                class="collections-mega-panel mega-menu-content hidden lg:block lg:absolute lg:left-0 lg:top-full lg:pt-2 lg:w-[min(920px,calc(100vw-2rem))] z-[60]"
                role="region"
                aria-label="Collections menu"
            >
                <div class="bg-white border border-slate-200 rounded-2xl shadow-2xl overflow-hidden text-slate-700">
                    <div class="px-5 py-4 bg-gradient-to-r from-emerald-50 via-white to-teal-50 border-b border-slate-200 flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <p class="text-[10px] uppercase tracking-widest text-emerald-600 font-extrabold">Browse departments</p>
                            <p class="text-sm font-bold text-emerald-800 mt-0.5">Shop by collection</p>
                        </div>
                        <a href="{{ route('collections') }}" class="inline-flex items-center gap-1.5 text-[11px] font-bold text-emerald-800 hover:text-emerald-700 transition px-3 py-2 rounded-lg bg-white border border-slate-200 shadow-sm">
                            View full catalog
                            <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                        </a>
                    </div>

                    <div class="flex flex-col lg:flex-row">
                        <div @class([
                            'flex-1 p-5 grid gap-4',
                            'grid-cols-1 sm:grid-cols-2' => $columnCount >= 2,
                            'grid-cols-1' => $columnCount < 2,
                        ])>
                            @foreach ($collectionsColumns as $column)
                            <div class="rounded-2xl border border-slate-200 bg-gradient-to-br from-white to-emerald-50/60 p-4 hover:border-emerald-200 hover:shadow-md transition group/card">
                                <a href="{{ $column['url'] }}" class="flex items-start gap-3 mb-3">
                                    <span class="w-11 h-11 shrink-0 rounded-xl bg-emerald-50 flex items-center justify-center group-hover/card:bg-emerald-100 transition">
                                        <i data-lucide="{{ $column['icon'] }}" class="w-5 h-5 {{ $column['icon_color'] }}"></i>
                                    </span>
                                    <span class="min-w-0">
                                        <span class="block font-bold text-emerald-800 text-xs uppercase tracking-wide">{{ $column['title'] }}</span>
                                        @if (! empty($column['description']))
                                        <span class="block text-[11px] text-slate-500 font-normal mt-1 line-clamp-2 whitespace-normal">{{ $column['description'] }}</span>
                                        @endif
                                    </span>
                                </a>
                                <ul class="space-y-1.5 text-[11px] text-slate-600 border-t border-slate-200/80 pt-3">
                                    @foreach ($column['links'] as $link)
                                    <li>
                                        <a href="{{ $link['url'] }}" class="flex items-center gap-2 py-1 hover:text-emerald-700 transition whitespace-normal">
                                            <i data-lucide="chevron-right" class="w-3 h-3 text-emerald-300 shrink-0"></i>
                                            {{ $link['label'] }}
                                        </a>
                                    </li>
                                    @endforeach
                                </ul>
                                <a href="{{ $column['url'] }}" class="inline-flex items-center gap-1 mt-3 text-[10px] font-extrabold uppercase tracking-wider text-emerald-600 hover:text-emerald-800 transition">
                                    Shop all
                                    <i data-lucide="arrow-up-right" class="w-3 h-3"></i>
                                </a>
                            </div>
                            @endforeach
                        </div>

                        <aside class="lg:w-[min(100%,280px)] xl:w-72 shrink-0 border-t lg:border-t-0 lg:border-l border-slate-200 bg-gradient-to-br from-slate-900 via-emerald-900 to-slate-950 text-white p-5 flex flex-col justify-between relative overflow-hidden">
                            <div class="absolute -right-16 -top-16 w-48 h-48 bg-emerald-600/20 rounded-full blur-3xl pointer-events-none"></div>
                            <div class="relative z-10">
                                <span class="inline-block px-2.5 py-1 rounded-full text-[9px] uppercase font-extrabold tracking-widest bg-emerald-500/20 text-emerald-100 border border-emerald-400/30">
                                    {{ $collectionsFeatured['badge'] ?? 'Featured' }}
                                </span>
                                <h3 class="text-lg font-serif font-bold mt-3 leading-snug">{{ $collectionsFeatured['title'] ?? 'Explore Beautage' }}</h3>
                                <p class="text-[11px] text-emerald-100/90 mt-2 leading-relaxed whitespace-normal">{{ $collectionsFeatured['text'] ?? 'Discover curated flagship collections.' }}</p>
                            </div>
                            @if (! empty($collectionsFeatured['button_url']))
                            <a href="{{ $collectionsFeatured['button_url'] }}" class="relative z-10 mt-5 w-full text-center text-xs font-extrabold uppercase tracking-wider bg-emerald-600 hover:bg-emerald-700 text-white py-3 px-4 rounded-xl transition shadow-lg shadow-emerald-900/30">
                                {{ $collectionsFeatured['button_label'] ?? 'Shop now' }}
                            </a>
                            @endif
                        </aside>
                    </div>
                </div>
            </div>
            @else
            <div class="collections-mega-panel mega-menu-content hidden lg:block lg:absolute lg:left-0 lg:top-full lg:pt-2 z-[60] lg:w-72">
                <div class="bg-white border border-slate-200 rounded-2xl shadow-xl p-5 text-sm">
                    <p class="text-slate-600 mb-3">Collections are being updated.</p>
                    <a href="{{ route('collections') }}" class="font-bold text-emerald-800 hover:text-emerald-700">Browse catalog →</a>
                </div>
            </div>
            @endif
        </div>

        @foreach ($mainNavItems as $item)
            @php
                $hasDropdown = ($item['has_dropdown'] ?? false) && ! empty($item['dropdown_links']);
                $linkClass = 'flex items-center gap-2 px-3.5 py-2 rounded-lg transition min-h-[44px] shrink-0 ';
                if ($item['highlight'] ?? false) {
                    $linkClass .= 'text-emerald-600 hover:bg-emerald-50 font-bold';
                } else {
                    $linkClass .= 'text-slate-700 hover:bg-emerald-50 hover:text-emerald-800';
                }
            @endphp

            @if (! $hasDropdown)
            <a href="{{ $item['url'] }}" class="{{ $linkClass }} hidden lg:flex">
                <i data-lucide="{{ $item['icon'] }}" class="w-4 h-4 {{ $item['icon_color'] ?? '' }} {{ ($item['highlight'] ?? false) ? 'fill-current' : '' }}"></i>
                <span>{{ $item['label'] }}</span>
            </a>
            @else
            <div class="relative shrink-0 group hidden lg:flex items-center rounded-lg hover:bg-emerald-50" data-nav-dropdown="cat-{{ md5($item['label'].$item['url']) }}">
                <a href="{{ $item['url'] }}" class="{{ $linkClass }} hover:bg-transparent pe-1">
                    <i data-lucide="{{ $item['icon'] }}" class="w-4 h-4 {{ $item['icon_color'] ?? '' }}"></i>
                    <span>{{ $item['label'] }}</span>
                </a>
                <button type="button" data-nav-dropdown-toggle aria-expanded="false" aria-label="Open {{ $item['label'] }} menu" class="flex items-center justify-center pe-2 py-2 text-slate-500 hover:text-emerald-800 min-h-[44px] min-w-[36px]">
                    <i data-lucide="chevron-down" class="w-3 h-3 opacity-70"></i>
                </button>
                <div class="nav-dropdown-panel mega-menu-content hidden lg:block lg:absolute lg:left-0 lg:top-full lg:pt-1 min-w-[220px] max-w-[280px] z-[60]">
                    <div class="bg-white border border-slate-200 rounded-xl shadow-xl py-2 text-slate-700">
                        <ul class="text-xs font-medium">
                            @foreach ($item['dropdown_links'] as $link)
                            <li>
                                <a href="{{ $link['url'] }}" class="block px-4 py-2.5 hover:bg-emerald-50 hover:text-emerald-700 transition whitespace-normal">{{ $link['label'] }}</a>
                            </li>
                            @endforeach
                            <li class="border-t border-slate-200 mt-1 pt-1">
                                <a href="{{ $item['url'] }}" class="block px-4 py-2.5 font-bold text-emerald-800 hover:bg-emerald-50 transition">
                                    View all {{ $item['label'] }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            @endif
        @endforeach

        </div>

        <div class="shrink-0 pl-1 hidden sm:block">
        </div>
    </nav>
</div>
