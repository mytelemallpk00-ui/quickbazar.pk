<?php

namespace App\Filament\Resources\SocialMarketingChannelResource\Pages;

use App\Filament\Resources\SocialMarketingChannelResource;
use App\Support\Social\SocialChannelCatalog;
use Filament\Resources\Pages\EditRecord;

class EditSocialMarketingChannel extends EditRecord
{
    protected static string $resource = SocialMarketingChannelResource::class;

    protected function afterSave(): void
    {
        SocialChannelCatalog::flushCache();
    }

    protected function afterDelete(): void
    {
        SocialChannelCatalog::flushCache();
    }
}
