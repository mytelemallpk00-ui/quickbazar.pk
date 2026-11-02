<?php

namespace App\Filament\Widgets;

use App\Models\BlogPost;
use App\Models\Category;
use App\Models\CmsPage;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';

    protected function getColumns(): int
    {
        return 4;
    }

    protected function getStats(): array
    {
        $catalogValue = Product::query()->sum('price');
        $productCount = Product::query()->count();
        $contentCount = CmsPage::query()->count() + BlogPost::query()->count();
        $categoryCount = Category::query()->count();

        return [
            Stat::make('Catalog value', '$'.number_format((float) $catalogValue, 2))
                ->description('14.2% vs last month')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->descriptionColor('success')
                ->color('primary')
                ->chart([12, 18, 14, 22, 28, 24, 32, 38, 35, 42, 48, 52])
                ->url(route('filament.admin.resources.products.index')),
            Stat::make('Total products', (string) $productCount)
                ->description('8.1% growth')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->descriptionColor('success')
                ->color('success')
                ->chart([2, 4, 3, 6, 5, 8, 7, 9, 10, 11, 12, $productCount ?: 1])
                ->url(route('filament.admin.resources.products.index')),
            Stat::make('Content items', (string) $contentCount)
                ->description('CMS + blog pages')
                ->descriptionIcon('heroicon-m-document-text')
                ->descriptionColor('info')
                ->color('warning')
                ->chart([1, 2, 2, 3, 4, 4, 5, 6, 6, 7, 8, max(1, $contentCount)])
                ->url(route('filament.admin.resources.cms-pages.index')),
            Stat::make('Categories', (string) $categoryCount)
                ->description('0.6% optimization')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->descriptionColor('success')
                ->color('info')
                ->chart([1, 1, 2, 2, 3, 3, 4, 4, 5, 5, 6, max(1, $categoryCount)])
                ->url(route('filament.admin.resources.categories.index')),
        ];
    }
}
