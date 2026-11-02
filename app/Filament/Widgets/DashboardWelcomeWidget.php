<?php

namespace App\Filament\Widgets;

use App\Support\AdminStoreAnalytics;
use Filament\Widgets\Widget;

class DashboardWelcomeWidget extends Widget
{
    protected static string $view = 'filament.widgets.dashboard-welcome';

    protected static ?int $sort = -10;

    protected int | string | array $columnSpan = 'full';

    /** @return array<string, mixed> */
    public function getTodayAnalyticsProperty(): array
    {
        return AdminStoreAnalytics::dashboard('1d');
    }

    /** @return array<string, mixed> */
    public function getPeriodAnalyticsProperty(): array
    {
        return AdminStoreAnalytics::dashboard('30d');
    }
}
