<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Settlement;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Filament\Clusters\Settings;
use Filament\Tables\Columns\TextColumn;
use Filament\Pages\SubNavigationPosition;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SettlementResource\Pages;
use App\Filament\Resources\SettlementResource\RelationManagers;

class SettlementResource extends Resource
{
    protected static ?string $model = Settlement::class;

    protected static ?string $cluster = Settings::class;
    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?string $navigationIcon = 'tabler-map-question';
    protected static ?string $modelLabel = 'település';
    protected static ?string $pluralModelLabel = 'települések';
    
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
                TextColumn::make('zip_code')
                ->label('Postai irányítószám')
                ->searchable(),
                TextColumn::make('settlement')
                ->label('Település neve')
                ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                //Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()->label(false),
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
            'index' => Pages\ListSettlements::route('/'),
            'create' => Pages\CreateSettlement::route('/create'),
            'edit' => Pages\EditSettlement::route('/{record}/edit'),
        ];
    }
}
