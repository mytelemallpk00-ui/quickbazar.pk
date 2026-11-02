@php
    use App\Models\User;
    use App\Support\AdminPermissions;
    use Filament\Facades\Filament;

    $user = Filament::auth()->user();
    $firstName = $user ? explode(' ', (string) $user->name)[0] : 'Admin';
    $today = $this->todayAnalytics;
    $period = $this->periodAnalytics;

    /** @var User|null $adminUser */
    $adminUser = auth()->user();
    $can = fn (string $permission): bool => $adminUser instanceof User && $adminUser->canAccessAdminModule($permission);
@endphp

<x-filament-widgets::widget class="beautage-dashboard-welcome-widget col-span-full">
    <div class="beautage-dash-shell">
        <div class="beautage-saas-hero">
            <div class="beautage-saas-hero-glow" aria-hidden="true"></div>
            <div class="beautage-saas-hero-grid">
                <div class="beautage-saas-hero-content">
                    <p class="beautage-saas-hero-eyebrow">
                        {{ now()->format('l, F j, Y') }}
                        <span class="beautage-saas-hero-sep">·</span>
                        <span class="beautage-live-pill beautage-live-pill--compact">
                            <span class="beautage-live-dot"></span>
                            {{ (int) ($today['live_visitors'] ?? 0) }} live now
                        </span>
                    </p>
                    <h2 class="beautage-saas-hero-title">Welcome back, {{ $firstName }}</h2>
                    <p class="beautage-saas-hero-subtitle">
                        Your Beautage command center — sales, traffic, and storefront health at a glance.
                    </p>
                </div>
                <div class="beautage-saas-hero-actions">
                    @if($can(AdminPermissions::CATALOG))
                    <a href="{{ route('filament.admin.resources.products.create') }}" class="beautage-saas-btn beautage-saas-btn--white">
                        <x-filament::icon icon="heroicon-m-plus" class="h-4 w-4 shrink-0" />
                        Add product
                    </a>
                    @endif
                    @if($can(AdminPermissions::ORDERS))
                    <a href="{{ route('filament.admin.resources.orders.index') }}" class="beautage-saas-btn beautage-saas-btn--outline">
                        <x-filament::icon icon="heroicon-m-shopping-bag" class="h-4 w-4 shrink-0" />
                        View orders
                    </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="beautage-dash-kpi-grid">
            <div class="beautage-dash-kpi">
                <div class="beautage-dash-kpi-icon beautage-dash-kpi-icon--sales">
                    <x-filament::icon icon="heroicon-o-banknotes" class="h-5 w-5" />
                </div>
                <div class="beautage-dash-kpi-body">
                    <span class="beautage-dash-kpi-label">Sales today</span>
                    <span class="beautage-dash-kpi-value">{{ $today['sales_total_formatted'] ?? '—' }}</span>
                    <span class="beautage-dash-kpi-meta">{{ number_format((int) ($today['orders_count'] ?? 0)) }} orders</span>
                </div>
            </div>
            <div class="beautage-dash-kpi">
                <div class="beautage-dash-kpi-icon beautage-dash-kpi-icon--traffic">
                    <x-filament::icon icon="heroicon-o-eye" class="h-5 w-5" />
                </div>
                <div class="beautage-dash-kpi-body">
                    <span class="beautage-dash-kpi-label">Page views today</span>
                    <span class="beautage-dash-kpi-value">{{ number_format((int) ($today['today_page_views'] ?? 0)) }}</span>
                    <span class="beautage-dash-kpi-meta">{{ number_format((int) ($today['sessions_total'] ?? 0)) }} sessions</span>
                </div>
            </div>
            <div class="beautage-dash-kpi">
                <div class="beautage-dash-kpi-icon beautage-dash-kpi-icon--period">
                    <x-filament::icon icon="heroicon-o-chart-bar" class="h-5 w-5" />
                </div>
                <div class="beautage-dash-kpi-body">
                    <span class="beautage-dash-kpi-label">Last 30 days sales</span>
                    <span class="beautage-dash-kpi-value">{{ $period['sales_total_formatted'] ?? '—' }}</span>
                    <span class="beautage-dash-kpi-meta">{{ number_format((int) ($period['orders_count'] ?? 0)) }} orders</span>
                </div>
            </div>
            <div class="beautage-dash-kpi">
                <div class="beautage-dash-kpi-icon beautage-dash-kpi-icon--live">
                    <x-filament::icon icon="heroicon-o-signal" class="h-5 w-5" />
                </div>
                <div class="beautage-dash-kpi-body">
                    <span class="beautage-dash-kpi-label">Store traffic (30d)</span>
                    <span class="beautage-dash-kpi-value">{{ number_format((int) ($period['sessions_total'] ?? 0)) }}</span>
                    <span class="beautage-dash-kpi-meta">{{ number_format((int) ($period['page_views_total'] ?? 0)) }} page views</span>
                </div>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
