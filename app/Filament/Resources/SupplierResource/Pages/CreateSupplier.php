<?php

namespace App\Filament\Resources\SupplierResource\Pages;

use Filament\Actions;
use Filament\Tables\Actions\CreateAction;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\SupplierResource;

class CreateSupplier extends CreateRecord
{
    protected static string $resource = SupplierResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected static bool $canCreateAnother = false;
}
