<?php

namespace App\Filament\Widgets;

use Faker\Core\Color;
use App\Models\Saleevent;
use BladeUI\Icons\Components\Icon;
use Illuminate\Support\Facades\Auth;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class SaleClosed extends BaseWidget
{    
    protected function getStats(): array
    {
        return [
            Stat::make('Sikeres értékesítés',Saleevent::count())
            ->value(function(){
                $userId = Auth::id();
                $saleeventCount = Saleevent::where('user_id', $userId)->where('status', 5)->count();
                return $saleeventCount.' db';
            })
            ->icon('tabler-thumb-up')
            ->description('Sikeresen lezárt értékesítési folyamat.')
            ->descriptionColor('success'),
            Stat::make('Vesztett értékesítés',Saleevent::count())
            ->value(function(){
                $userId = Auth::id();
                $saleeventCount = Saleevent::where('user_id', $userId)->where('status', 6)->count();
                return $saleeventCount.' db';
            })
            ->icon('tabler-thumb-down')
            ->description('Sikertelenül lezárt értékesítési folyamat.')
            ->descriptionColor('danger')
        ];
    }
}
