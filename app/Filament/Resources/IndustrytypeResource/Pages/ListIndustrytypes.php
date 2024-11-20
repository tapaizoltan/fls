<?php

namespace App\Filament\Resources\IndustrytypeResource\Pages;

use App\Filament\Resources\IndustrytypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListIndustrytypes extends ListRecords
{
    protected static string $resource = IndustrytypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
