<?php

namespace App\Filament\Resources\SocialMarketingChannelResource\Pages;

use App\Filament\Resources\SocialMarketingChannelResource;
use App\Support\Social\SocialChannelCatalog;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSocialMarketingChannels extends ListRecords
{
    protected static string $resource = SocialMarketingChannelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function afterReorder(): void
    {
        SocialChannelCatalog::flushCache();
    }
}
