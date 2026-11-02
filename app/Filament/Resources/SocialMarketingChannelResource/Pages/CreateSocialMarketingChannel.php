<?php

namespace App\Filament\Resources\SocialMarketingChannelResource\Pages;

use App\Filament\Resources\SocialMarketingChannelResource;
use App\Support\Social\SocialChannelCatalog;
use Filament\Resources\Pages\CreateRecord;

class CreateSocialMarketingChannel extends CreateRecord
{
    protected static string $resource = SocialMarketingChannelResource::class;

    protected function afterCreate(): void
    {
        SocialChannelCatalog::flushCache();
    }
}
