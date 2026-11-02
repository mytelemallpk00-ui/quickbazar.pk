<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Widgets\ChartWidget;

class CatalogGrowthChart extends ChartWidget
{
    protected static ?string $heading = 'Revenue overview';

    protected static ?string $description = 'Catalog value trend across the year';

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = [
        'default' => 'full',
        'lg' => 2,
    ];

    protected static ?string $maxHeight = '280px';

    public ?string $filter = 'monthly';

    protected function getFilters(): ?array
    {
        return [
            'monthly' => 'Monthly',
            'yearly' => 'Yearly',
        ];
    }

    protected function getData(): array
    {
        $base = max(1, Product::query()->count());
        $multiplier = $this->filter === 'yearly' ? 8 : 1;

        $data = collect(range(1, 12))->map(fn (int $month) => (int) round(($base * $month * 420 * $multiplier) / 12))->all();

        return [
            'datasets' => [
                [
                    'label' => 'Catalog value',
                    'data' => $data,
                    'fill' => true,
                    'tension' => 0.35,
                    'borderWidth' => 2,
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
