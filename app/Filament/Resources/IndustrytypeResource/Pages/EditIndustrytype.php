<?php

namespace App\Filament\Resources\IndustrytypeResource\Pages;

use App\Filament\Resources\IndustrytypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditIndustrytype extends EditRecord
{
    protected static string $resource = IndustrytypeResource::class;

    // protected function getHeaderActions(): array
    // {
    //     return [
    //         Actions\DeleteAction::make(),
    //     ];
    // }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
