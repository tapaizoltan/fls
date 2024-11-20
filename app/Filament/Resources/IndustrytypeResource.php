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
use Filament\Forms\Components\TextInput;
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
                TextInput::make('name')
                    ->label('Iparág neve')
                    ->helperText('Módosítsa az iparág nevét.')
                    ->required()
                    ->prefixIcon('tabler-writing')
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('Iparágak')
            ->heading('Iparágak, tevékenységi besorolások')
            ->description('Ebben a modulban rögzíthet iparágakat, amiket később hozzá tud társítani az ügyfeleihez.')
            ->emptyStateHeading('Nincs megjeleníthető iparág.')
            ->emptyStateDescription('Új iparág rögzítése az "Új ügyfél" létrehozásakor, az iparágak lenyíló menüjében, a "+" gombra kattintva rögzíthet, amit később bármelyik másik ügyfélnél is tud majd használni..')
            ->emptyStateIcon('tabler-database-search')
            ->columns([
                TextColumn::make('name')
                    ->label('Iparág neve')
                    ->searchable()
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make()
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label(false)->icon('tabler-pencil')->modalHeading('Iparág szerkesztése'),
                //Tables\Actions\DissociateAction::make(),
                Tables\Actions\DeleteAction::make()->label(false)->icon('tabler-trash'),
                Tables\Actions\ForceDeleteAction::make()->label(false),
                Tables\Actions\RestoreAction::make()->label(false),
            ])

            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DissociateBulkAction::make(),
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn(Builder $query) => $query->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]));
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
            // 'create' => Pages\CreateIndustrytype::route('/create'),
            // 'edit' => Pages\EditIndustrytype::route('/{record}/edit'),
        ];
    }
}
