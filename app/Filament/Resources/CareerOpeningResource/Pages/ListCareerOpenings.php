<?php

namespace App\Filament\Resources\CareerOpeningResource\Pages;

use App\Filament\Resources\CareerOpeningResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCareerOpenings extends ListRecords
{
    protected static string $resource = CareerOpeningResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
