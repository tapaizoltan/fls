<?php
 
namespace App\Filament\Pages;
 
use Filament\Pages\Dashboard as BaseDashboard;
 
class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'tabler-armchair';
    protected static ?string $modelLabel = 'vezérlőpult';
    protected static ?string $pluralModelLabel = 'vezérlőpult';
}