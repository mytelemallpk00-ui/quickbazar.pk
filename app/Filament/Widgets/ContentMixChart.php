<?php

namespace App\Filament\Widgets;

use App\Models\BlogPost;
use App\Models\CareerOpening;
use App\Models\Category;
use App\Models\CmsPage;
use App\Models\Product;
use Filament\Widgets\ChartWidget;

class ContentMixChart extends ChartWidget
{
    protected static ?string $heading = 'Traffic sources';

    protected static ?string $description = 'Share of catalog vs content';

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = [
        'default' => 'full',
        'lg' => 1,
    ];

    protected static ?string $maxHeight = '280px';

    protected function getData(): array
    {
        $products = Product::query()->count();
        $categories = Category::query()->count();
        $cms = CmsPage::query()->count();
        $blog = BlogPost::query()->count();
        $careers = CareerOpening::query()->count();

        $organic = max(1, $products + $categories);
        $direct = max(1, $cms);
        $referral = max(1, $blog + $careers);

        return [
            'datasets' => [
                [
                    'label' => 'Mix',
                    'data' => [$organic, $direct, $referral],
                    'backgroundColor' => ['#5E1F70', '#10B981', '#F59E0B'],
                    'borderWidth' => 0,
                ],
            ],
            'labels' => ['Catalog', 'CMS pages', 'Blog & careers'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
