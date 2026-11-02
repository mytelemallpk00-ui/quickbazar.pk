<?php

namespace App\Support;

use App\Models\Order;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminStoreAnalytics
{
    /** @return array<string, string> */
    public static function rangeLabels(): array
    {
        return [
            '1d' => 'Today',
            '7d' => '7 days',
            '30d' => '30 days',
            '90d' => '3 months',
            '180d' => '6 months',
            '365d' => '1 year',
            '730d' => '2 years',
        ];
    }

    /** @return array{days: int, bucket: string} */
    public static function rangeConfig(string $range): array
    {
        return match ($range) {
            '1d' => ['days' => 1, 'bucket' => 'hour'],
            '7d' => ['days' => 7, 'bucket' => 'day'],
            '30d' => ['days' => 30, 'bucket' => 'day'],
            '90d' => ['days' => 90, 'bucket' => 'day'],
            '180d' => ['days' => 180, 'bucket' => 'week'],
            '365d' => ['days' => 365, 'bucket' => 'week'],
            '730d' => ['days' => 730, 'bucket' => 'month'],
            default => ['days' => 30, 'bucket' => 'day'],
        };
    }

    public static function normalizeRange(string $range): string
    {
        return array_key_exists($range, static::rangeLabels()) ? $range : '30d';
    }

    /** @return array<string, mixed> */
    public static function dashboard(string $range): array
    {
        $range = static::normalizeRange($range);
        $config = static::rangeConfig($range);
        $end = Carbon::now()->endOfMinute();
        $start = match ($range) {
            '1d' => Carbon::today()->startOfDay(),
            default => $end->copy()->subDays($config['days'] - 1)->startOfDay(),
        };

        $sales = static::salesSeries($start, $end, $config['bucket']);
        $traffic = static::trafficSeries($start, $end, $config['bucket']);

        return [
            'range' => $range,
            'ranges' => static::rangeLabels(),
            'currency' => static::currencySymbol(),
            'sales_total' => $sales['total'],
            'sales_total_formatted' => static::formatMoney($sales['total']),
            'orders_count' => $sales['orders_count'],
            'sessions_total' => $traffic['sessions_total'],
            'page_views_total' => $traffic['page_views_total'],
            'sales_chart' => $sales['chart'],
            'traffic_chart' => $traffic['chart'],
            'traffic_note' => static::trafficTrackingNote($start),
            'live_visitors' => StoreTraffic::liveVisitorCount(),
            'today_page_views' => static::todayPageViews(),
        ];
    }

    protected static function todayPageViews(): int
    {
        if (! Schema::hasTable('store_traffic_daily')) {
            return 0;
        }

        return (int) (DB::table('store_traffic_daily')
            ->where('date', now()->toDateString())
            ->value('page_views') ?? 0);
    }

    protected static function currencySymbol(): string
    {
        $currency = strtoupper((string) SiteSettings::get('analytics_gmc_currency', 'PKR'));

        return match ($currency) {
            'PKR' => 'Rs',
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            default => $currency.' ',
        };
    }

    protected static function formatMoney(float $amount): string
    {
        return static::currencySymbol().number_format($amount, 0, '.', ',');
    }

    /**
     * @return array{total: float, orders_count: int, chart: array{labels: list<string>, values: list<float>}}
     */
    protected static function salesSeries(Carbon $start, Carbon $end, string $bucket): array
    {
        $rows = Order::query()
            ->whereBetween('created_at', [$start, $end])
            ->whereNotIn('status', [Order::STATUS_CANCELLED])
            ->where('payment_status', '!=', Order::PAYMENT_REFUNDED)
            ->get(['total', 'created_at']);

        $ordersCount = $rows->count();
        $total = (float) $rows->sum('total');

        $buckets = static::buildBucketKeys($start, $end, $bucket);
        $totals = array_fill_keys(array_keys($buckets), 0.0);

        foreach ($rows as $order) {
            $key = static::bucketKey(Carbon::parse($order->created_at), $bucket);
            if (array_key_exists($key, $totals)) {
                $totals[$key] += (float) $order->total;
            }
        }

        return [
            'total' => $total,
            'orders_count' => $ordersCount,
            'chart' => [
                'labels' => array_values($buckets),
                'values' => array_map(fn (string $key): float => round($totals[$key], 2), array_keys($buckets)),
            ],
        ];
    }

    /**
     * @return array{sessions_total: int, page_views_total: int, chart: array{labels: list<string>, sessions: list<int>, page_views: list<int>}}
     */
    protected static function trafficSeries(Carbon $start, Carbon $end, string $bucket): array
    {
        $buckets = static::buildBucketKeys($start, $end, $bucket);
        $sessions = array_fill_keys(array_keys($buckets), 0);
        $pageViews = array_fill_keys(array_keys($buckets), 0);

        if (Schema::hasTable('store_traffic_daily')) {
            $dailyRows = DB::table('store_traffic_daily')
                ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
                ->get(['date', 'sessions', 'page_views']);

            foreach ($dailyRows as $row) {
                $key = static::bucketKey(Carbon::parse($row->date)->startOfDay(), $bucket);
                if (! array_key_exists($key, $sessions)) {
                    continue;
                }
                $sessions[$key] += (int) $row->sessions;
                $pageViews[$key] += (int) $row->page_views;
            }
        }

        return [
            'sessions_total' => (int) array_sum($sessions),
            'page_views_total' => (int) array_sum($pageViews),
            'chart' => [
                'labels' => array_values($buckets),
                'sessions' => array_map(fn (string $key): int => $sessions[$key], array_keys($buckets)),
                'page_views' => array_map(fn (string $key): int => $pageViews[$key], array_keys($buckets)),
            ],
        ];
    }

    protected static function trafficTrackingNote(Carbon $start): ?string
    {
        if (! Schema::hasTable('store_traffic_daily')) {
            return 'Store traffic tracking will appear after the latest site update is deployed.';
        }

        $first = DB::table('store_traffic_daily')->where('page_views', '>', 0)->min('date');
        if ($first === null) {
            return 'Traffic is counted from storefront visits. Data fills in as customers browse your shop.';
        }

        if (Carbon::parse($first)->gt($start)) {
            return 'Traffic tracking started '.$first.'. Earlier dates in this range may show zero.';
        }

        return null;
    }

    /** @return array<string, string> bucket key => label */
    protected static function buildBucketKeys(Carbon $start, Carbon $end, string $bucket): array
    {
        $keys = [];

        if ($bucket === 'hour') {
            $cursor = $start->copy()->startOfHour();
            while ($cursor <= $end) {
                $key = $cursor->format('Y-m-d H:00');
                $keys[$key] = $cursor->format('g A');
                $cursor->addHour();
            }

            return $keys;
        }

        if ($bucket === 'week') {
            $cursor = $start->copy()->startOfWeek();
            while ($cursor <= $end) {
                $key = $cursor->format('Y-\\WW');
                $keys[$key] = 'Wk '.$cursor->format('M j');
                $cursor->addWeek();
            }

            return $keys;
        }

        if ($bucket === 'month') {
            $cursor = $start->copy()->startOfMonth();
            while ($cursor <= $end) {
                $key = $cursor->format('Y-m');
                $keys[$key] = $cursor->format('M Y');
                $cursor->addMonth();
            }

            return $keys;
        }

        $period = CarbonPeriod::create($start->copy()->startOfDay(), '1 day', $end->copy()->startOfDay());
        foreach ($period as $day) {
            $key = $day->format('Y-m-d');
            $keys[$key] = $day->format('M j');
        }

        return $keys;
    }

    protected static function bucketKey(Carbon $moment, string $bucket): string
    {
        return match ($bucket) {
            'hour' => $moment->copy()->startOfHour()->format('Y-m-d H:00'),
            'week' => $moment->copy()->startOfWeek()->format('Y-\\WW'),
            'month' => $moment->copy()->startOfMonth()->format('Y-m'),
            default => $moment->copy()->startOfDay()->format('Y-m-d'),
        };
    }
}
