<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Filament\Clusters\Settings;
use App\Models\Productmaincategory;
use Filament\Pages\SubNavigationPosition;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ProductmaincategoryResource\Pages;
use App\Filament\Resources\ProductmaincategoryResource\RelationManagers;

class ProductmaincategoryResource extends Resource
{
    protected static ?string $model = Productmaincategory::class;

    protected static ?string $cluster = Settings::class;
    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?string $navigationIcon = 'tabler-list-details';
    protected static ?string $modelLabel = 'termék főkategória';
    protected static ?string $pluralModelLabel = 'termék főkategóriák';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListProductmaincategories::route('/'),
            'create' => Pages\CreateProductmaincategory::route('/create'),
            'edit' => Pages\EditProductmaincategory::route('/{record}/edit'),
        ];
    }
}
