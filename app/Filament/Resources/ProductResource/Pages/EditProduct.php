<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Concerns\GeneratesProductSeoWithAi;
use App\Filament\Concerns\InteractsWithGoogleIndex;
use App\Filament\Resources\ProductResource;
use App\Models\GoogleIndexLog;
use App\Services\GoogleIndex\ClaudeSeoPreflightService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    use GeneratesProductSeoWithAi;
    use InteractsWithGoogleIndex;

    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('claudeSeoPreflight')
                ->label('AI SEO check')
                ->icon('heroicon-o-sparkles')
                ->color('info')
                ->visible(fn (): bool => app(ClaudeSeoPreflightService::class)->isConfigured())
                ->action(fn () => $this->analyzeProductSeoWithClaude($this->getRecord())),
            Actions\Action::make('pushGoogleIndex')
                ->label('Push to Google Index')
                ->icon('heroicon-o-rocket-launch')
                ->color('success')
                ->requiresConfirmation()
                ->action(fn () => $this->pushProductToGoogle($this->getRecord())),
            Actions\Action::make('googleIndexDeleted')
                ->label('Notify URL deleted')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->action(fn () => $this->pushProductToGoogle($this->getRecord(), GoogleIndexLog::ACTION_DELETED)),
            Actions\DeleteAction::make(),
        ];
    }
}
