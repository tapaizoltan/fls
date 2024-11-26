<?php

namespace App\Filament\Resources\ProductsubcategoryResource\Pages;

use App\Filament\Resources\ProductsubcategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProductsubcategory extends EditRecord
{
    protected static string $resource = ProductsubcategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
