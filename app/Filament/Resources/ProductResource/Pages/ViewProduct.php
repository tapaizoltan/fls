<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewProduct extends ViewRecord
{
    protected static string $resource = ProductResource::class;
    
    protected static ?string $navigationLabel = 'Termék adatlapja';
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
}
