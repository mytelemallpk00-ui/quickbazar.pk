@php
    use App\Support\MoneyFormat;

    $departments = \App\Models\Category::query()
        ->where('is_active', true)
        ->orderBy('sort_order')
        ->orderBy('name')
        ->get();
@endphp
<div id="storefront-nav-backdrop" aria-hidden="true"></div>
<div id="storefront-category-strip" class="qb-nav w-full">
    <nav class="flex w-full items-center gap-1 px-4 sm:px-6 lg:px-8" aria-label="Departments">
        <a href="{{ route('home') }}" class="qb-nav-link {{ request()->routeIs('home') ? 'is-current' : '' }}">
            <i data-lucide="home" class="h-4 w-4 text-orange-400"></i>
            Home
        </a>

        <div class="flex min-w-0 flex-1 items-center gap-0.5 overflow-x-auto no-scrollbar">
            @foreach ($departments as $department)
                @php
                    $links = $department->resolvedNavLinks();
                    $picks = $department->products()->orderByDesc('rating')->limit(4)->get();
                    $dropdownId = 'dept-'.$department->id;
                @endphp
                <div class="group relative flex shrink-0 items-center" data-nav-dropdown="{{ $dropdownId }}">
                    <a href="{{ $department->collectionsUrl() }}" class="qb-nav-link pr-1">
                        <i data-lucide="{{ $department->iconName() }}" class="h-4 w-4 text-orange-300"></i>
                        {{ $department->displayNavLabel() }}
                    </a>
                    <button type="button" data-nav-dropdown-toggle class="qb-nav-link px-1.5" aria-expanded="false" aria-controls="mega-{{ $department->id }}" aria-label="Open {{ $department->displayNavLabel() }} menu">
                        <i data-lucide="chevron-down" class="collections-chevron h-3.5 w-3.5 opacity-70"></i>
                    </button>
                    <div id="mega-{{ $department->id }}" class="nav-dropdown-panel mega-menu-content qb-mega hidden lg:block" role="region" aria-label="{{ $department->displayNavLabel() }}">
                        <div class="qb-mega-card grid w-full gap-6 px-4 py-6 sm:px-6 lg:grid-cols-12 lg:px-8">
                            <div class="lg:col-span-3">
                                <p class="text-[11px] font-extrabold uppercase tracking-wide text-orange-600">Department</p>
                                <h3 class="mt-1 text-lg font-extrabold text-slate-900">{{ $department->displayNavLabel() }}</h3>
                                @if (filled($department->description))
                                    <p class="mt-2 text-sm leading-relaxed text-slate-500">{{ \Illuminate\Support\Str::limit(strip_tags($department->description), 140) }}</p>
                                @endif
                                <ul class="mt-4 space-y-1">
                                    @foreach ($links as $link)
                                        <li>
                                            <a href="{{ $link['url'] }}" class="block rounded-lg px-2 py-2 text-sm font-semibold text-slate-700 hover:bg-orange-50 hover:text-orange-700">{{ $link['label'] }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                                <a href="{{ $department->collectionsUrl() }}" class="mt-3 inline-flex items-center gap-1 text-sm font-bold text-orange-600 hover:text-orange-700">
                                    Shop all {{ $department->displayNavLabel() }}
                                    <i data-lucide="arrow-right" class="h-4 w-4"></i>
                                </a>
                            </div>
                            <div class="lg:col-span-6">
                                <p class="text-[11px] font-extrabold uppercase tracking-wide text-slate-400">Popular in this department</p>
                                <div class="mt-3 grid grid-cols-2 gap-3">
                                    @forelse ($picks as $pick)
                                        <a href="{{ route('product.show', $pick) }}" class="flex items-center gap-3 rounded-xl border border-slate-100 p-2 hover:border-orange-200 hover:bg-orange-50/40">
                                            <img src="{{ $pick->image }}" alt="" width="56" height="56" class="h-14 w-14 shrink-0 rounded-lg object-cover" loading="lazy">
                                            <span class="min-w-0">
                                                <span class="block truncate text-sm font-bold text-slate-900">{{ $pick->name }}</span>
                                                <span class="mt-0.5 block text-sm font-extrabold text-orange-600">{{ MoneyFormat::pkr($pick->price) }}</span>
                                            </span>
                                        </a>
                                    @empty
                                        <p class="col-span-2 text-sm text-slate-500">Products in this department will show here.</p>
                                    @endforelse
                                </div>
                            </div>
                            <aside class="flex flex-col justify-between rounded-2xl bg-[#0b1220] p-5 text-white lg:col-span-3">
                                <div>
                                    <p class="text-[11px] font-extrabold uppercase tracking-wide text-orange-300">Express delivery</p>
                                    <p class="mt-2 text-lg font-extrabold leading-snug">Cash on delivery across Pakistan</p>
                                    <p class="mt-2 text-sm text-slate-300">Lahore, Karachi, Islamabad and 50+ cities.</p>
                                </div>
                                <a href="{{ $department->collectionsUrl() }}" class="mt-5 inline-flex items-center justify-center rounded-lg bg-orange-500 px-4 py-2.5 text-sm font-bold text-white hover:bg-orange-600">Shop {{ $department->displayNavLabel() }}</a>
                            </aside>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <a href="{{ route('exclusive-offers') }}" class="qb-flash shrink-0">
            <i data-lucide="zap" class="h-3.5 w-3.5"></i>
            Flash deals
        </a>
    </nav>
</div>
