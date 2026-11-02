<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class ManageContentLinksWidget extends Widget
{
    protected static string $view = 'filament.widgets.manage-content-links';

    protected static ?int $sort = 10;

    protected int | string | array $columnSpan = 'full';
}
