<?php

namespace App\Filament\Resources\ProductmaincategoryResource\Pages;

use App\Filament\Resources\ProductmaincategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProductmaincategory extends EditRecord
{
    protected static string $resource = ProductmaincategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
