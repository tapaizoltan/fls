<?php

namespace App\Filament\Resources\ProductResource\Pages;

use Filament\Forms;
use Filament\Tables;
use Filament\Actions;
use App\Models\Supplier;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Support\Htmlable;
use App\Filament\Resources\ProductResource;
use Filament\Resources\Pages\ManageRelatedRecords;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ManageProductProductprices extends ManageRelatedRecords
{
    protected static string $resource = ProductResource::class;

    protected static string $relationship = 'productprices';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationLabel(): string
    {
        return 'Árak';
    }

    public function getTitle(): string | Htmlable
    {
        return "Árak";
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('supplier_id')
                    ->label('Beszállító')
                    ->helperText('Válassza ki, vagy a "+" gombra kattintva, hozzon létre egy új beszállítót az adott termékhez.')
                    ->prefixIcon('tabler-layout-list')
                    ->preload()
                    //->options(Region::all()->pluck('name', 'id'))
                    ->relationship(name: 'supplier', titleAttribute: 'name')
                    ->native(false)
                    ->required()
                    ->searchable()
                    ->createOptionForm([
                        TextInput::make('name')->label('Beszállító neve')->helperText('Adja meg az új beszállító nevét. Később a beszállítókat kezelheti a Beszállítás/Beszállítók menü alatt.')
                            ->required()->unique(),
                    ]),
                Section::make('Beszerzési árak')
                    ->description('Rögzítse az adott termék beszerzési árait.')
                    ->schema([
                        TextInput::make('net_purchase_price_eur')
                            ->helperText('Adja meg a netto beszerzési árat Euro-ban.')
                            ->label('Netto beszerzési ár (Euro)')
                            ->prefixIcon('tabler-transfer-in')
                            ->postfix('€')
                            ->numeric()
                            ->minLength(2)
                            ->maxLength(255),

                        TextInput::make('net_purchase_price_huf')
                            ->helperText('Adja meg a netto beszerzési árat Forint-ban.')
                            ->label('Netto beszerzési ár (Forint)')
                            ->prefixIcon('tabler-transfer-in')
                            ->postfix('Ft')
                            ->numeric()
                            ->minLength(2)
                            ->maxLength(255),
                    ])->columns(2),

                Section::make('Lista árak')
                    ->description('Rögzítse az adott termék lista árait.')
                    ->schema([
                        TextInput::make('net_list_price_eur')
                            ->helperText('Adja meg a netto lista árat Euro-ban.')
                            ->label('Netto lista ár (Euro)')
                            ->prefixIcon('tabler-transfer-out')
                            ->postfix('€')
                            ->numeric()
                            ->minLength(2)
                            ->maxLength(255),

                        TextInput::make('net_list_price_huf')
                            ->helperText('Adja meg a netto lista árat Forint-ban.')
                            ->label('Netto lista ár (Forint)')
                            ->prefixIcon('tabler-transfer-out')
                            ->postfix('Ft')
                            ->numeric()
                            ->minLength(2)
                            ->maxLength(255),
                    ])->columns(2),
            ])
            ->columns([
                'sm' => 1,
                'md' => 2,
                'lg' => 2,
                'xl' => 2,
                '2xl' => 2,
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Árak.')
            ->description('Ebben a modulban rögzítheti és kezelheti termék árait.')
            ->emptyStateHeading('Nincs megjeleníthető ár a termékhez.')
            ->emptyStateDescription('Az "Új ár" gombra kattintva rögzíthet új árat a termékhez.')
            ->emptyStateIcon('tabler-database-search')
            //->recordTitleAttribute('árak')
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('supplier_id')
                    ->label('Beszállító')
                    ->formatStateUsing(function ($record) {
                        return $record->supplier->name;
                    }),
                Tables\Columns\TextColumn::make('net_purchase_price_eur')
                    ->label('Netto beszerzési ár')
                    ->formatStateUsing(function ($record) {
                        return '<p><span class="text-custom-600 dark:text-custom-400" style="font-size:11pt;">' . number_format($record->net_purchase_price_huf, "0", ".", ".") . ' Ft</span></p>
                    <p><span class="text-custom-600 dark:text-custom-400" style="font-size:11pt;">' . number_format($record->net_purchase_price_eur, "0", ".", ".") . ' €</span></p>';
                    })->html()
                    ->searchable(['net_purchase_price_huf', 'net_purchase_price_eur']),
                Tables\Columns\TextColumn::make('net_list_price_eur')
                    ->label('Netto lista ár')
                    ->formatStateUsing(function ($record) {
                        return '<p><span class="text-custom-600 dark:text-custom-400" style="font-size:11pt;">' . number_format($record->net_list_price_huf, "0", ".", ".") . ' Ft</span></p>
                    <p><span class="text-custom-600 dark:text-custom-400" style="font-size:11pt;">' . number_format($record->net_list_price_eur, "0", ".", ".") . ' €</span></p>';
                    })->html()
                    ->searchable(['net_list_price_huf', 'net_list_price_eur']),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make()
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
                // Tables\Actions\AssociateAction::make(),
                Tables\Actions\CreateAction::make()->label('Új ár')->icon('tabler-circle-plus')->modalHeading('Új ár')->createAnother(false),
            ])
            ->actions([
                //Tables\Actions\EditAction::make()->label(false)->icon('tabler-pencil'),
                //Tables\Actions\DissociateAction::make(),
                //Tables\Actions\DeleteAction::make()->label(false)->icon('tabler-trash'),
                //Tables\Actions\ForceDeleteAction::make()->label(false),
                //Tables\Actions\RestoreAction::make()->label(false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DissociateBulkAction::make(),
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn(Builder $query) => $query->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]));
    }
}
