<?php

namespace App\Providers;

use Filament\Support\Assets\Css;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Filament\Support\Facades\FilamentAsset;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::unguard();

        FilamentAsset::register([
            Css::make('custom-stylesheet', __DIR__ . '/../../resources/css/app.css'),
        ]);
    }


}
