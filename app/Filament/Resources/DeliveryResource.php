<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Delivery;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\ToggleButtons;
use App\Filament\Resources\DeliveryResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\DeliveryResource\RelationManagers;
use Coolsam\SignaturePad\Forms\Components\Fields\SignaturePad;

class DeliveryResource extends Resource
{
    protected static ?string $model = Delivery::class;

    protected static ?string $navigationGroup = 'Kiszállítás';

    protected static ?string $modelLabel = 'kiszállítás';
    protected static ?string $pluralModelLabel = 'kiszállítások';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            SignaturePad::make('signature')
            ->backgroundColor('black') // Set the background color in case you want to download to jpeg
            ->penColor('blue') // Set the pen color
            ->strokeMinDistance(2.0) // set the minimum stroke distance (the default works fine)
            ->strokeMaxWidth(2.5) // set the max width of the pen stroke
            ->strokeMinWidth(1.0) // set the minimum width of the pen stroke
            ->strokeDotSize(2.0) // set the stroke dot size.
            ->hideDownloadButtons() // In case you don't want to show the download buttons on the pad, you can hide them by setting this option.
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('price_offer_id')->label('Azonosító'),
                TextColumn::make('sale.customer.name')
                ->label('Cég neve')
                ->sortable()
                ->searchable(),
                TextColumn::make('created_at')->label('Létrehozva')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                /*
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
                */
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDeliveries::route('/'),
            'create' => Pages\CreateDelivery::route('/create'),
            'edit' => Pages\EditDelivery::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string //ez kiírja a menü mellé, hogy mennyi ügyfél van már rögzítve
    {
        /** @var class-string<Model> $modelClass */
        $modelClass = static::$model;

        return (string) $modelClass::all()->count();
    }
}
