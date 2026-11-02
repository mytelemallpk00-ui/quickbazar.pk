<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class ManageContentLinks extends Widget
{
    protected static string $view = 'filament.widgets.manage-content-links';

    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 'full';
}
