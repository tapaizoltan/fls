<?php

namespace App\Filament\Resources\ProductmaincategoryResource\Pages;

use App\Filament\Resources\ProductmaincategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProductmaincategories extends ListRecords
{
    protected static string $resource = ProductmaincategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
