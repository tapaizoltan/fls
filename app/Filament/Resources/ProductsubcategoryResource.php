<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\Productsubcategory;
use Illuminate\Support\HtmlString;
use App\Filament\Clusters\Settings;
use App\Models\Productmaincategory;
use Filament\Forms\Components\Grid;
use Filament\Tables\Grouping\Group;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SubNavigationPosition;
use Filament\Tables\Actions\CreateAction;
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
                Grid::make(2)
                    ->schema([
                        Select::make('productmaincategory_id')
                            ->label('Főkategória')
                            ->helperText('Válassza ki, melyik főkategóriához kíván alkategóriát létrehozni.')
                            //->options(Brand::all()->pluck('name', 'id'))
                            ->prefixIcon('tabler-layout-list')
                            ->preload()
                            ->relationship(name: 'productmaincategory', titleAttribute: 'name')
                            ->native(false)
                            ->searchable()
                            ->required()
                            ->columns(1),
                        TextInput::make('name')
                            ->label('Alkategória neve')
                            ->required()
                            ->helperText('Adja mega az új termék alkategória elnevezését.')
                            ->maxLength(255)
                            ->columns(1),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('Termék alkategóriák')
            ->heading('Termék alkategóriák')
            ->description('Ebben a modulban rögzítheti azokat az alkatgóriákat, amelyekbe később rendezhetőek lesznek a termékek.')
            ->emptyStateHeading('Nincs megjeleníthető termék alkategória.')
            ->emptyStateDescription('Az "Új termék alkategória" gombra kattintva rögzíthet új alkatógiát.')
            ->emptyStateIcon('tabler-database-search')
            ->groups([
                Group::make('productmaincategory.name')
                    ->titlePrefixedWithLabel(false)
                    ->getDescriptionFromRecordUsing(function () {
                        return 'főkategória elemei';
                    })
                    ->collapsible(),
            ])
            ->defaultGroup('productmaincategory.name')
            ->groupingSettingsHidden()
            ->headerActions([
                CreateAction::make()
                    ->createAnother(false)
                    ->modalWidth('md')
                    ->icon('tabler-circle-plus')
                    ->slideOver()
                    ->form([
                        Grid::make(2)
                            ->schema([
                                Select::make('productmaincategory_id')
                                    ->label('Főkategória')
                                    ->helperText('Válassza ki, melyik főkategóriához kíván alkategóriát létrehozni.')
                                    //->options(Brand::all()->pluck('name', 'id'))
                                    ->prefixIcon('tabler-layout-list')
                                    ->preload()
                                    ->relationship(name: 'productmaincategory', titleAttribute: 'name')
                                    ->native(false)
                                    ->searchable()
                                    ->required()
                                    ->columnSpanFull(),
                                TextInput::make('name')
                                    ->label('Alkategória neve')
                                    ->required()
                                    ->helperText('Adja mega az új termék alkategória elnevezését.')
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ])
            ->columns([
                TextColumn::make('name')
                    ->label('Alkategória neve')
                    ->formatStateUsing(function ($record): HtmlString {
                        // return new HtmlString('<div style="display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 7px;"><span class="fi-badge flex items-center justify-center gap-x-1 rounded-md text-xs font-medium ring-1 ring-inset px-2 min-w-[theme(spacing.6)] py-1 fi-color-custom bg-custom-50 text-custom-600 ring-custom-600/10 dark:bg-custom-400/10 dark:text-custom-400 dark:ring-custom-400/30 fi-color-gray" style="--c-50:var(--gray-50);--c-400:var(--gray-400);--c-600:var(--gray-600);">' . Productmaincategory::find($record->productmaincategory_id)->name . '</span></div><div style="display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 7px;"><svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-corner-down-right"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 6v6a3 3 0 0 0 3 3h10l-4 -4m0 8l4 -4" /></svg><span>' . $record->name . '</span></div>');
                        return new HtmlString('<div style="display: flex; flex-wrap: wrap; gap: 6px; margin-left:30px;margin-bottom: 7px;"><svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-corner-down-right"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 6v6a3 3 0 0 0 3 3h10l-4 -4m0 8l4 -4" /></svg><span>' . $record->name . '</span></div>');
                    })

                    // ->formatStateUsing(function ($record) : HtmlString {
                    //     return new HtmlString('<span class="text-gray-500 dark:text-gray-400" style="font-size:9pt;">Főkategória: ' . Productmaincategory::find($record->productmaincategory_id)->name . '</span><span class="text-gray-500 dark:text-gray-400" style="font-size:9pt;">Főkategória: ' . Productmaincategory::find($record->productmaincategory_id)->name . '</span>');
                    // })
                    // ->description(function ($record): HtmlString {
                    //         return new HtmlString('<span class="text-gray-500 dark:text-gray-400" style="font-size:9pt;">Főkategória: ' . Productmaincategory::find($record->productmaincategory_id)->name . '</span>');
                    // } ,position: 'above')

                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query
                            ->where('name', 'like', "%{$search}%")
                            ->orWhereHas('productmaincategory', function ($query) use ($search) {
                                $query->where('name', 'like', "%{$search}%");
                            });
                    }),
                //->searchable(['name', 'productmaincategory.name']),
                // ->searchable(),
                // TextColumn::make('productmaincategory.name')
                //     ->label('Főkategória')
                //     ->searchable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make()
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label(false)->icon('tabler-pencil')->modalHeading('Alkategória szerkesztése')->modalWidth('xl'),
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
            'index' => Pages\ListProductsubcategories::route('/'),
            // 'create' => Pages\CreateProductsubcategory::route('/create'),
            // 'edit' => Pages\EditProductsubcategory::route('/{record}/edit'),
        ];
    }
}
