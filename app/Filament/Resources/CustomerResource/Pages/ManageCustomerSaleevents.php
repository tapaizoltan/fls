<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use Filament\Actions;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Support\Htmlable;
use App\Filament\Resources\CustomerResource;
use Filament\Resources\Pages\ManageRelatedRecords;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use PhpParser\Node\Stmt\Label;

class ManageCustomerSaleevents extends ManageRelatedRecords
{
    protected static string $resource = CustomerResource::class;

    protected static string $relationship = 'saleevents';

    protected static ?string $navigationIcon = 'tabler-history';

    public static function getNavigationLabel(): string
    {
        return 'Értékesítési esemény(ek)';
    }

    public function getTitle(): string | Htmlable
    {
        return 'Értékesírési esemény(ek) szerkesztése';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            //->recordTitleAttribute('Értékesítési események')
            ->heading('Értékesítéssel kapcsolatos események.')
            ->description('Ebben a modulban megtekinthetőek az adott vállalkozás értékesítési eseményei.')
            ->emptyStateHeading('Nincs megjeleníthető értékesítési esemény.')
            //->emptyStateDescription('Az "Új ügyfél" gombra kattintva rögzíthet új ügyfelet, társaságot, vállalkozást a rendszerhez.')
            ->emptyStateIcon('tabler-database-search')
            ->defaultSort('created_at', 'desc')
            // ->defaultGroup(
            //     Group::make('created_at')
            //     // ->getTitleFromRecordUsing(function($record){return 'Kibocsájtó: '.$record->source;})
            //     ->titlePrefixedWithLabel(false)
            //     ->collapsible(),
            // )
            // ->defaultGroup(
            //     Group::make('created_at')
            //     ->label(false)
            //     ->getTitleFromRecordUsing(function ($record) {
            //         return Carbon::parse($record->created_at)->translatedFormat('Y F d. l');
            //     })
            //     ->titlePrefixedWithLabel(false)
            //     ->collapsible(),
            // )
            ->columns([
                TextColumn::make('created_at')
                    ->label('Dátum')
                    // ->formatStateUsing(function ($state){
                    //     return Carbon::parse($state)->translatedFormat('G').' óra '.Carbon::parse($state)->translatedFormat('i').' perc';
                    // })
                    ->formatStateUsing(function ($state){
                        return Carbon::parse($state)->translatedFormat('Y F d. l');
                    })
                    ->description(function($state)
                    {
                        return Carbon::parse($state)->translatedFormat('G').' óra '.Carbon::parse($state)->translatedFormat('i').' perc';
                    })
                    ->searchable(),
                TextColumn::make('event_type')
                    ->label('Esemény típusa')
                    ->badge()
                    ->size('md')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Státusz')
                    ->badge()
                    ->size('md')
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label('Felelős személy')
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make()
            ])
            ->headerActions([
                //Tables\Actions\CreateAction::make(),
                //Tables\Actions\AssociateAction::make(),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DissociateAction::make(),
                // Tables\Actions\DeleteAction::make(),
                // Tables\Actions\ForceDeleteAction::make(),
                // Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DissociateBulkAction::make(),
                //     Tables\Actions\DeleteBulkAction::make(),
                //     Tables\Actions\RestoreBulkAction::make(),
                //     Tables\Actions\ForceDeleteBulkAction::make(),
                // ]),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]));
    }
}
