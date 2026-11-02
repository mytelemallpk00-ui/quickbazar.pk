<?php

namespace App\Filament\Resources\ProductBundleResource\Pages;

use App\Filament\Resources\ProductBundleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProductBundle extends EditRecord
{
    protected static string $resource = ProductBundleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('viewOnStore')
                ->label('View on storefront')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->url(fn (): ?string => $this->record->loadMissing('hostProduct')->storefrontUrl())
                ->openUrlInNewTab()
                ->visible(fn (): bool => filled($this->record->loadMissing('hostProduct')->hostProduct?->slug)),
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $hostId = (int) $this->record->product_id;
        $this->record->bundleItems()->where('product_id', $hostId)->delete();
    }
}
