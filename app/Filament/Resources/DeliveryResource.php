<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;

use Filament\Tables;
use App\Models\Delivery;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\Contact;
use App\Models\Product;
use App\Models\Productsubcategory;
use Illuminate\Support\HtmlString;
use App\Models\Productmaincategory;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
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
    //protected static ?string $model = Delivery::class;

    protected static ?string $navigationGroup = 'Kiszállítás';

    protected static ?string $modelLabel = 'kiszállítás';
    protected static ?string $pluralModelLabel = 'kiszállítások';
    protected static ?int $navigationSort = 1;
    protected static ?string $model = \App\Models\Priceoffer::class;
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('status', 5);
    }
    /*
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
        */

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Repeater::make('priceofferitems')
                ->relationship('priceofferitems')
                ->schema([
                    Placeholder::make('product_name')
                    ->label('Termék')
                    /*->content(fn ($record) => $record->product?->description ?? '-'),*/
                    ->content(fn ($record) => $record->product?->width . '/' . $record->product?->height . $record->product?->structure . $record->product?->rim_diameter ?? '-'),
                    /*->formatStateUsing(function ($record) {
                        return '<p><span class="text-custom-600 dark:text-custom-400" style="font-size:11pt; text-transform: uppercase; ">' . $record->width . '/' . $record->height . $record->structure . $record->rim_diameter . '</span></p>';
                        //return $record->width . '/' . $record->height . $record->structure . $record->rim_diameter;
                    }),*/

                    TextInput::make('quantity')
                        ->label('Mennyiség')
                        ->disabled()
                        ->dehydrated(false),
                ])
                ->columns(2)
                ->disableItemCreation()
                ->disableItemDeletion()
                ->disableItemMovement(),

                SignaturePad::make('signature')
                ->backgroundColor('black') // Set the background color in case you want to download to jpeg
                ->penColor('blue') // Set the pen color
                ->strokeMinDistance(2.0) // set the minimum stroke distance (the default works fine)
                ->strokeMaxWidth(2.5) // set the max width of the pen stroke
                ->strokeMinWidth(1.0) // set the minimum width of the pen stroke
                ->strokeDotSize(2.0) // set the stroke dot size.
                ->hideDownloadButtons() // In case you don't want to show the download buttons on the pad, you can hide them by setting this option.
                ->required(),
        ]);
    }

    public static function mutateFormDataBeforeSave(array $data): array
    {
        $data['status'] = 6;
        return $data;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->heading('Kiszállítási folyamatok.')
            ->description('Ebben a modulban kezelheti a kiszállításait.')
            ->emptyStateHeading('Nincs megjeleníthető kiszállítási folyamat vagy esemény.')
            ->emptyStateIcon('tabler-database-search')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Dátum')
                    ->formatStateUsing(function ($state) {
                        return Carbon::parse($state)->translatedFormat('Y F d. l');
                    })
                    ->sortable()
                    ->searchable(),
                TextColumn::make('price_offer_id')
                ->label('Azonosító'),
                TextColumn::make('sale.customer.name')
                ->label('Cég neve')
                ->sortable()
                ->searchable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
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
