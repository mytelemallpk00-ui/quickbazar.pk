<?php

namespace App\Filament\Widgets;

use App\Support\AdminStoreAnalytics;
use Filament\Widgets\Widget;

class StorePerformanceChartsWidget extends Widget
{
    protected static string $view = 'filament.widgets.store-performance-charts';

    protected static ?int $sort = -9;

    protected static ?string $pollingInterval = '30s';

    protected int | string | array $columnSpan = 'full';

    public string $range = '30d';

    /** @return array<string, mixed> */
    public function getAnalyticsProperty(): array
    {
        return AdminStoreAnalytics::dashboard($this->range);
    }
}
