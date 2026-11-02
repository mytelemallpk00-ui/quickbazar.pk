<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\AuthorizesAdminPage;
use App\Filament\Widgets\DashboardWelcomeWidget;
use App\Filament\Widgets\ManageContentLinksWidget;
use App\Filament\Widgets\StorePerformanceChartsWidget;
use App\Support\AdminPermissions;
use Filament\Pages\Dashboard;

class AdminDashboard extends Dashboard
{
    use AuthorizesAdminPage;

    protected static function adminPermissionModule(): string
    {
        return AdminPermissions::OVERVIEW;
    }

    /** @return array<class-string> */
    public function getWidgets(): array
    {
        return [
            DashboardWelcomeWidget::class,
            StorePerformanceChartsWidget::class,
            ManageContentLinksWidget::class,
        ];
    }

    protected static ?string $navigationGroup = 'Overview';

    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?string $navigationLabel = 'Dashboard';

    protected static ?int $navigationSort = -10;

    public function getColumns(): int | string | array
    {
        return 1;
    }

    public function getTitle(): string
    {
        return 'Dashboard';
    }
}
