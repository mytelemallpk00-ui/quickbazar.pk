<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class DashboardWelcome extends Widget
{
    protected static string $view = 'filament.widgets.dashboard-welcome';

    protected static ?int $sort = 0;

    protected int | string | array $columnSpan = 'full';
}
