<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Brand;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use App\Filament\Clusters\Settings;
use Filament\Forms\Components\Grid;
use Filament\Tables\Grouping\Group;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SubNavigationPosition;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\BrandResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\BrandResource\RelationManagers;

class BrandResource extends Resource
{
    protected static ?string $model = Brand::class;

    protected static ?string $cluster = Settings::class;
    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?string $navigationIcon = 'tabler-lollipop';
    protected static ?string $modelLabel = 'márka';
    protected static ?string $pluralModelLabel = 'márkák';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(2)
                    ->schema([
                        Select::make('supplier_id')
                            ->label('Beszállító')
                            ->helperText('Válassza ki a beszállítót.')
                            ->required()
                            ->columns(1)
                            ->relationship(name: 'supplier', titleAttribute: 'name')
                            ->native(false),
                        TextInput::make('name')
                            ->label('Márkanév')
                            ->helperText('Módosítsa a márkanevet.')
                            ->required()
                            ->prefixIcon('tabler-writing')
                            ->columns(1),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('Márkák')
            ->heading('Márkák, márkanevek')
            ->description('Ebben a modulban rögzítheti a forgalmazott márkákat, márkaneveket, amiket később hozzá tud társítani az egyes termékekhez.')
            ->emptyStateHeading('Nincs megjeleníthető márka, márkanév.')
            ->emptyStateDescription('Új márka rögzítése az "Új termék" létrehozásakor, a márka lenyíló menüjében, a "+" gombra kattintva rögzíthet, amit később bármelyik másik terméknél is tud majd használni..')
            ->emptyStateIcon('tabler-database-search')
            ->groups([
                Group::make('supplier.name')
                    ->titlePrefixedWithLabel(false)
                    ->getDescriptionFromRecordUsing(function () {
                        return 'beszállító márkái';
                    })
                    ->collapsible(),
            ])
            ->defaultGroup('supplier.name')
            ->groupingSettingsHidden()
            ->columns([
                TextColumn::make('name')
                    ->label('Márkanév')
                    ->formatStateUsing(function ($record): HtmlString {
                        return new HtmlString('<div style="display: flex; flex-wrap: wrap; gap: 6px; margin-left:30px;margin-bottom: 7px;"><svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-corner-down-right"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 6v6a3 3 0 0 0 3 3h10l-4 -4m0 8l4 -4" /></svg><span>' . $record->name . '</span></div>');
                    })
                    ->searchable()
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make()
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label(false)->icon('tabler-pencil')->modalHeading('Márkanév szerkesztése')->modalWidth('xl'),
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
            'index' => Pages\ListBrands::route('/'),
            // 'create' => Pages\CreateBrand::route('/create'),
            // 'edit' => Pages\EditBrand::route('/{record}/edit'),
        ];
    }
}
