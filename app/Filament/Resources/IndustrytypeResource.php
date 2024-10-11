<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Industrytype;
use Filament\Resources\Resource;
use App\Filament\Clusters\Settings;
use Filament\Tables\Columns\TextColumn;
use Filament\Pages\SubNavigationPosition;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\IndustrytypeResource\Pages;
use App\Filament\Resources\IndustrytypeResource\RelationManagers;

class IndustrytypeResource extends Resource
{
    protected static ?string $model = Industrytype::class;

    protected static ?string $cluster = Settings::class;
    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?string $navigationIcon = 'tabler-building-factory';
    protected static ?string $modelLabel = 'iparág';
    protected static ?string $pluralModelLabel = 'iparágak';

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
                TextColumn::make('name')
                ->label('Megnevezés')
                ->searchable()
            ])
            ->filters([
                //
            ])
            ->actions([
                //Tables\Actions\EditAction::make(),
                DeleteAction::make()->icon('tabler-trash')->label(false),
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
            'index' => Pages\ListIndustrytypes::route('/'),
            'create' => Pages\CreateIndustrytype::route('/create'),
            'edit' => Pages\EditIndustrytype::route('/{record}/edit'),
        ];
    }
}
