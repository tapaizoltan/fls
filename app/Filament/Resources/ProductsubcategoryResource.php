<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\Productsubcategory;
use App\Filament\Clusters\Settings;
use Filament\Pages\SubNavigationPosition;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ProductsubcategoryResource\Pages;
use App\Filament\Resources\ProductsubcategoryResource\RelationManagers;

class ProductsubcategoryResource extends Resource
{
    protected static ?string $model = Productsubcategory::class;

    protected static ?string $cluster = Settings::class;
    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?string $navigationIcon = 'tabler-list-tree';
    protected static ?string $modelLabel = 'termék alkategória';
    protected static ?string $pluralModelLabel = 'termék alkategóriák';

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
            'index' => Pages\ListProductsubcategories::route('/'),
            'create' => Pages\CreateProductsubcategory::route('/create'),
            'edit' => Pages\EditProductsubcategory::route('/{record}/edit'),
        ];
    }
}
