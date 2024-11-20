<?php

namespace App\Filament\Resources\IndustrytypeResource\Pages;

use App\Filament\Resources\IndustrytypeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateIndustrytype extends CreateRecord
{
    protected static string $resource = IndustrytypeResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected static bool $canCreateAnother = false;
}
