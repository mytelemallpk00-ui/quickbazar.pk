@php
    $analytics = $this->analytics;
    $chartId = 'beautage-store-charts-'.$this->getId();
    $live = (int) ($analytics['live_visitors'] ?? 0);
    $todayViews = (int) ($analytics['today_page_views'] ?? 0);
@endphp

<x-filament-widgets::widget class="beautage-store-analytics-widget col-span-full">
    <div class="beautage-analytics-shell beautage-analytics-shell--compact" wire:poll.30s id="{{ $chartId }}-root">
        <div class="beautage-analytics-inner">
            <div class="beautage-analytics-toolbar">
                <div class="beautage-analytics-toolbar__left">
                    <h3 class="beautage-dash-section-title">Store performance</h3>
                    <span class="beautage-live-pill beautage-live-pill--compact">
                        <span class="beautage-live-dot"></span>
                        {{ $live }} live
                    </span>
                    <span class="beautage-analytics-toolbar__hint">{{ number_format($todayViews) }} views today</span>
                </div>
                <div class="beautage-range-pills shrink-0">
                    @foreach ($analytics['ranges'] as $key => $label)
                        <button
                            type="button"
                            wire:click="$set('range', '{{ $key }}')"
                            wire:loading.attr="disabled"
                            @class([
                                'bg-white text-primary-600 shadow-sm ring-1 ring-primary-500/20 dark:bg-gray-900 dark:text-primary-400' => $analytics['range'] === $key,
                                'text-gray-600 hover:bg-white/80 dark:text-gray-400 dark:hover:bg-white/5' => $analytics['range'] !== $key,
                            ])
                        >
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>

            @if ($analytics['traffic_note'])
                <p class="beautage-analytics-note">{{ $analytics['traffic_note'] }}</p>
            @endif

            <div class="beautage-metrics-row">
                <div class="beautage-metric-card beautage-metric-card--sales beautage-metric-card--split">
                    <div class="beautage-metric-card__stat">
                        <div class="flex items-center justify-between gap-2">
                            <p class="beautage-metric-label">Total sales</p>
                            <x-filament::icon icon="heroicon-o-banknotes" class="h-5 w-5 text-primary-500/50 shrink-0" />
                        </div>
                        <p class="beautage-metric-kpi beautage-metric-kpi--compact">{{ $analytics['sales_total_formatted'] }}</p>
                        <p class="beautage-metric-meta">{{ number_format($analytics['orders_count']) }} orders</p>
                    </div>
                    <div wire:ignore class="beautage-chart-wrap beautage-chart-wrap--compact" data-chart-wrap="sales">
                        <div class="beautage-chart-loading" data-chart-loading="{{ $chartId }}">Loading…</div>
                        <canvas id="{{ $chartId }}-sales" role="img" aria-label="Sales over time"></canvas>
                    </div>
                </div>

                <div class="beautage-metric-card beautage-metric-card--traffic beautage-metric-card--split">
                    <div class="beautage-metric-card__stat">
                        <div class="flex items-center justify-between gap-2">
                            <p class="beautage-metric-label">Store traffic</p>
                            <x-filament::icon icon="heroicon-o-globe-alt" class="h-5 w-5 text-indigo-500/50 shrink-0" />
                        </div>
                        <p class="beautage-metric-kpi beautage-metric-kpi--compact">{{ number_format($analytics['sessions_total']) }}</p>
                        <p class="beautage-metric-meta">{{ number_format($analytics['page_views_total']) }} page views</p>
                    </div>
                    <div wire:ignore class="beautage-chart-wrap beautage-chart-wrap--compact" data-chart-wrap="traffic">
                        <div class="beautage-chart-loading" data-chart-loading="{{ $chartId }}">Loading…</div>
                        <canvas id="{{ $chartId }}-traffic" role="img" aria-label="Traffic over time"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>

@script
<script>
(() => {
    const chartId = @js($chartId);
    const root = document.getElementById(chartId + '-root');
    let salesChart = null;
    let trafficChart = null;

    const hideLoading = () => {
        root?.querySelectorAll('[data-chart-loading="' + chartId + '"]').forEach((el) => el.remove());
    };

    const showLoadError = () => {
        root?.querySelectorAll('[data-chart-loading="' + chartId + '"]').forEach((el) => {
            el.textContent = 'Chart unavailable — refresh page';
        });
    };

    const gradientFill = (ctx, colorStart, colorEnd) => {
        const g = ctx.createLinearGradient(0, 0, 0, 120);
        g.addColorStop(0, colorStart);
        g.addColorStop(1, colorEnd);
        return g;
    };

    const baseOptions = {
        responsive: true,
        maintainAspectRatio: false,
        animation: { duration: 500, easing: 'easeOutQuart' },
        interaction: { mode: 'index', intersect: false },
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: 'rgba(24, 24, 27, 0.92)',
                padding: 8,
                cornerRadius: 8,
                titleFont: { size: 11 },
                bodyFont: { size: 11 },
            },
        },
        scales: {
            x: {
                grid: { display: false },
                ticks: { maxTicksLimit: 5, color: '#a1a1aa', font: { size: 9 } },
                border: { display: false },
            },
            y: {
                beginAtZero: true,
                grid: { color: 'rgba(161, 161, 170, 0.12)' },
                ticks: { color: '#a1a1aa', font: { size: 9 }, maxTicksLimit: 4 },
                border: { display: false },
            },
        },
    };

    const destroyCharts = () => {
        if (salesChart) { salesChart.destroy(); salesChart = null; }
        if (trafficChart) { trafficChart.destroy(); trafficChart = null; }
    };

    const renderCharts = () => {
        const analytics = $wire.analytics;
        const salesEl = document.getElementById(chartId + '-sales');
        const trafficEl = document.getElementById(chartId + '-traffic');
        if (!salesEl || !trafficEl || typeof Chart === 'undefined') {
            return false;
        }

        destroyCharts();

        const salesCtx = salesEl.getContext('2d');
        salesChart = new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: analytics.sales_chart.labels,
                datasets: [{
                    label: 'Sales',
                    data: analytics.sales_chart.values,
                    borderColor: '#5E1F70',
                    borderWidth: 2,
                    backgroundColor: gradientFill(salesCtx, 'rgba(94, 31, 112, 0.25)', 'rgba(94, 31, 112, 0.02)'),
                    fill: true,
                    tension: 0.35,
                    pointRadius: 0,
                    pointHoverRadius: 4,
                }],
            },
            options: baseOptions,
        });

        const trafficCtx = trafficEl.getContext('2d');
        trafficChart = new Chart(trafficCtx, {
            type: 'line',
            data: {
                labels: analytics.traffic_chart.labels,
                datasets: [
                    {
                        label: 'Sessions',
                        data: analytics.traffic_chart.sessions,
                        borderColor: '#6366F1',
                        borderWidth: 2,
                        backgroundColor: gradientFill(trafficCtx, 'rgba(99, 102, 241, 0.2)', 'rgba(99, 102, 241, 0.02)'),
                        fill: true,
                        tension: 0.35,
                        pointRadius: 0,
                    },
                    {
                        label: 'Views',
                        data: analytics.traffic_chart.page_views,
                        borderColor: '#10B981',
                        borderWidth: 1.5,
                        borderDash: [3, 2],
                        fill: false,
                        tension: 0.35,
                        pointRadius: 0,
                    },
                ],
            },
            options: {
                ...baseOptions,
                plugins: {
                    ...baseOptions.plugins,
                    legend: {
                        display: true,
                        position: 'top',
                        align: 'end',
                        labels: { boxWidth: 8, usePointStyle: true, font: { size: 9 }, padding: 6 },
                    },
                },
            },
        });

        hideLoading();
        return true;
    };

    const loadChartJs = () => new Promise((resolve) => {
        if (typeof Chart !== 'undefined') {
            resolve(true);
            return;
        }
        const filamentScript = document.querySelector('script[src*="chart.js"]');
        if (filamentScript && !filamentScript.dataset.beautageChartBound) {
            filamentScript.dataset.beautageChartBound = '1';
            filamentScript.addEventListener('load', () => resolve(typeof Chart !== 'undefined'), { once: true });
            setTimeout(() => resolve(typeof Chart !== 'undefined'), 1500);
            return;
        }
        const existing = document.querySelector('script[data-beautage-chartjs]');
        if (existing) {
            existing.addEventListener('load', () => resolve(typeof Chart !== 'undefined'), { once: true });
            setTimeout(() => resolve(typeof Chart !== 'undefined'), 1500);
            return;
        }
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js';
        script.async = true;
        script.dataset.beautageChartjs = '1';
        script.onload = () => resolve(typeof Chart !== 'undefined');
        script.onerror = () => resolve(false);
        document.head.appendChild(script);
    });

    const bootCharts = (attempt = 0) => {
        loadChartJs().then((ok) => {
            if (!ok) {
                if (attempt < 3) {
                    setTimeout(() => bootCharts(attempt + 1), 400);
                    return;
                }
                showLoadError();
                return;
            }
            if (!renderCharts() && attempt < 5) {
                setTimeout(() => bootCharts(attempt + 1), 200);
            }
        });
    };

    bootCharts();

    $wire.watch('range', () => {
        queueMicrotask(() => bootCharts());
    });

    Livewire.hook('commit', ({ component, succeed }) => {
        succeed(() => {
            if (component.el?.querySelector?.('#' + chartId + '-sales')) {
                queueMicrotask(() => renderCharts());
            }
        });
    });
})();
</script>
@endscript
