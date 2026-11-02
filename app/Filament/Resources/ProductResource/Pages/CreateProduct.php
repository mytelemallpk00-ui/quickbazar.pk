<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Concerns\GeneratesProductSeoWithAi;
use App\Filament\Resources\ProductResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    use GeneratesProductSeoWithAi;

    protected static string $resource = ProductResource::class;

}
