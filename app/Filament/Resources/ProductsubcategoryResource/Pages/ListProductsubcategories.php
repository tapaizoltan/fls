<?php

namespace App\Filament\Resources\ProductsubcategoryResource\Pages;

use App\Filament\Resources\ProductsubcategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProductsubcategories extends ListRecords
{
    protected static string $resource = ProductsubcategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
