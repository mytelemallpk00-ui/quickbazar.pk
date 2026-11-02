<?php

namespace App\Filament\Resources\ProgrammaticSeoLandingResource\Pages;

use App\Filament\Resources\ProgrammaticSeoLandingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProgrammaticSeoLanding extends EditRecord
{
    protected static string $resource = ProgrammaticSeoLandingResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
