<?php

namespace App\Filament\Resources\SaleResource\Pages;

use App\Filament\Resources\SaleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSales extends ListRecords
{
    protected static string $resource = SaleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make()->label('Új ügyfél')->icon('tabler-circle-plus'),
            Actions\CreateAction::make()->label('Új értékesítés')->icon('tabler-circle-plus')->slideOver()->createAnother(false),
        ];
    }
}
