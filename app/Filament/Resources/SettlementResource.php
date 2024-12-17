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
use Filament\Forms\Components\TextInput;
use Filament\Pages\SubNavigationPosition;
use Filament\Tables\Actions\CreateAction;
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
                TextInput::make('zip_code')
                    ->label('Postai irányítószám')
                    ->helperText('Módosítsa a település postai irányítószámát.')
                    ->numeric()
                    ->required()
                    ->prefixIcon('tabler-numbers')
                    ->columns(2),
                TextInput::make('settlement')
                    ->label('Település név')
                    ->helperText('Módosítsa a kívánt település nevét.')
                    ->required()
                    ->prefixIcon('tabler-writing')
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('Települések')
            ->heading('Települések és postai irányítószámok')
            ->description('Ebben a modulban rögzíthet Településeket a hozzá tartozó postai irányítószámmal.')
            ->emptyStateHeading('Nincs megjeleníthető település.')
            ->emptyStateDescription('Az "Új település" gombra kattintva rögzíthet település és posti irányítószámot.')
            ->emptyStateIcon('tabler-database-search')
            ->headerActions([
                CreateAction::make()
                    ->createAnother(false)
                    ->modalWidth('md')
                    ->icon('tabler-circle-plus')
                    ->slideOver()
                    ->form([
                        TextInput::make('zip_code')
                            ->label('Postai irányítószám')
                            ->helperText('Adja meg a rögzíteni kívánt új település postai irányítószámát.')
                            ->numeric()
                            ->required()
                            ->prefixIcon('tabler-numbers')
                            ->columnSpanFull(),
                        TextInput::make('settlement')
                            ->label('Település név')
                            ->helperText('Adja meg a rögzíteni kívánt új település nevét.')
                            ->required()
                            ->prefixIcon('tabler-writing')
                            ->columnSpanFull(),
                    ]),
            ])
            ->columns([
                TextColumn::make('zip_code')
                    ->label('Postai irányítószám')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('settlement')
                    ->label('Település neve')
                    ->sortable()
                    ->searchable(),
            ])
            ->openRecordUrlInNewTab()
            ->filters([
                Tables\Filters\TrashedFilter::make()
            ])
            // ->headerActions([
            //     Tables\Actions\CreateAction::make()->label('Új település')->icon('tabler-circle-plus')->modalHeading('Új település')->slideOver()->modalWidth('xl')->createAnother(false),
            // ])
            ->actions([
                Tables\Actions\EditAction::make()->label(false)->icon('tabler-pencil')->modalHeading('Település szerkesztése')->modalWidth('xl'),
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
            'index' => Pages\ListSettlements::route('/'),
            // 'create' => Pages\CreateSettlement::route('/create'),
            // 'edit' => Pages\EditSettlement::route('/{record}/edit'),
        ];
    }
}
