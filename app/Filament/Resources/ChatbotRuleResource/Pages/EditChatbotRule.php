<?php

namespace App\Filament\Resources\ChatbotRuleResource\Pages;

use App\Filament\Resources\ChatbotRuleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditChatbotRule extends EditRecord
{
    protected static string $resource = ChatbotRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
