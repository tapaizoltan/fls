<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use Filament\Forms;
use Filament\Tables;
use Filament\Actions;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Support\Htmlable;
use App\Filament\Resources\CustomerResource;
use Filament\Resources\Pages\ManageRelatedRecords;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ManageCustomerAddresses extends ManageRelatedRecords
{
    protected static string $resource = CustomerResource::class;

    protected static string $relationship = 'addresses';

    protected static ?string $navigationIcon = 'tabler-map-pin-2';

    public static function getNavigationLabel(): string
    {
        return 'Cím';
    }

    public function getTitle(): string | Htmlable
    {
        return 'Cím(ek) szerkesztése';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('cimek')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('cimek')
            ->columns([
                Tables\Columns\TextColumn::make('cimek'),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make()
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
                Tables\Actions\AssociateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DissociateAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DissociateBulkAction::make(),
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]));
    }
}