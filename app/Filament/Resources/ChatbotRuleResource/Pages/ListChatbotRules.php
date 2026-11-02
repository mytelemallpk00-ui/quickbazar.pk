<?php

namespace App\Filament\Resources\ChatbotRuleResource\Pages;

use App\Filament\Resources\ChatbotRuleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListChatbotRules extends ListRecords
{
    protected static string $resource = ChatbotRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
