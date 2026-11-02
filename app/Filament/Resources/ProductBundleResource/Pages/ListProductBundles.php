<?php

namespace App\Filament\Resources\ProductBundleResource\Pages;

use App\Filament\Resources\ProductBundleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProductBundles extends ListRecords
{
    protected static string $resource = ProductBundleResource::class;

    public function getSubheading(): ?string
    {
        return 'Each deal appears only on its main product\'s detail page (/products/slug) when active — not on home, collections, or checkout.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
