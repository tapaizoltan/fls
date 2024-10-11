<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;
use Filament\Pages\SubNavigationPosition;

class Settings extends Cluster
{
    protected static ?string $navigationIcon = 'tabler-adjustments-cog';
    protected static ?string $navigationLabel = 'Beállítások';
}
