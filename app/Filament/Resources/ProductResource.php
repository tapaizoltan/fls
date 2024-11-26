<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Brand;
use App\Models\Product;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\Productsubcategory;
use Illuminate\Support\HtmlString;
use App\Models\Productmaincategory;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Pages\SubNavigationPosition;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ProductResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ProductResource\RelationManagers;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationGroup = 'Termékek';
    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?string $modelLabel = 'termék';
    protected static ?string $pluralModelLabel = 'termékek';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(4)
                    ->schema([
                        Select::make('brand_id')
                            ->label('Márka')
                            ->helperText('Válassza ki, vagy a "+" gombra kattintva, hozzon létre egy új márkanevet, amit később bármelyik termékhez használhat.')
                            //->options(Brand::all()->pluck('name', 'id'))
                            ->prefixIcon('tabler-layout-list')
                            ->preload()
                            ->relationship(name: 'brand', titleAttribute: 'name')
                            ->native(false)
                            ->searchable()
                            ->columnSpan(2)
                            ->createOptionModalHeading('Új márka, márkanév')
                            ->createOptionForm([
                                Grid::make(2)
                                ->schema([
                                    TextInput::make('name')
                                    ->label('Márkanév')
                                    ->helperText('Adja meg az új márka nevét.')
                                    ->required()
                                    ->columnSpan(1)
                                    ->unique(),
                                ])
                            ]),

                        Select::make('productmaincategory_id')
                            ->label('Főkategória')
                            ->helperText('Válassza ki, vagy a "+" gombra kattintva, hozzon létre egy új főkategóriát, amit később bármelyik termékhez használhat.')
                            //->options(Brand::all()->pluck('name', 'id'))
                            ->prefixIcon('tabler-layout-list')
                            ->preload()
                            ->relationship(name: 'productmaincategory', titleAttribute: 'name')
                            ->native(false)
                            ->searchable()
                            ->columnSpan(2)
                            ->createOptionModalHeading('Új főkategória')
                            ->createOptionForm([
                                Grid::make(2)
                                ->schema([
                                    TextInput::make('name')
                                    ->label('Főkategória neve')
                                    ->helperText('Adja meg az új főkategória nevét. Célszerű olyat választani ami a későbbiekben segítségére lehet a könnyebb azonosítás tekintetében.')
                                    ->required()
                                    ->columnSpan(1)
                                    ->unique(),
                                ])
                            ]),

                        Select::make('productsubcategory_id')
                            ->label('Alkategória')
                            ->helperText('Válassza ki, vagy a "+" gombra kattintva, hozzon létre egy új alkategóriát, amit később bármelyik termékhez használhat.')
                            //->options(Brand::all()->pluck('name', 'id'))
                            ->prefixIcon('tabler-layout-list')
                            ->preload()
                            ->relationship(name: 'productsubcategory', titleAttribute: 'name')
                            ->native(false)
                            ->searchable()
                            ->columnSpan(2)
                            ->createOptionModalHeading('Új alkategória')
                            ->createOptionForm([
                                Grid::make(2)
                                    ->schema([
                                        Select::make('productmaincategory_id')
                                            ->label('Főkategória')
                                            ->helperText('Válassza ki melyik főkategória alá kíván létrehozni alkategóriát.')
                                            ->options(Productmaincategory::all()->pluck('name', 'id'))
                                            ->prefixIcon('tabler-layout-list')
                                            ->preload()
                                            ->native(false)
                                            ->searchable()
                                            ->columnSpan(1)
                                            ->required()
                                            ->unique(),
                                        TextInput::make('name')
                                            ->label('Alkategória neve')
                                            ->helperText('Adja meg az új alkategória nevét. Célszerű olyat választani ami a későbbiekben segítségére lehet a könnyebb azonosítás tekintetében.')
                                            ->required()
                                            ->columnSpan(1)
                                            ->unique(),
                                    ])
                            ]),

                        Fieldset::make('Termék')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Neve')
                                    ->helperText('Adja meg az új termék nevét.')
                                    ->required()
                                    ->columnSpan(1),

                                Textarea::make('description')
                                    ->label('Leírása')
                                    ->helperText('Itt adhat néhány sor rövid ismertetőt a termékről.')
                                    ->rows(5)
                                    ->cols(20)
                                    ->columnSpan(1),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->heading('Termékek.')
            ->description('Ebben a modulban rögzítheti és kezelheti az értékesítési folyamatokban résztvevő termékeket.')
            ->emptyStateHeading('Nincs megjeleníthető termék.')
            ->emptyStateDescription('Az "Új termék" gombra kattintva rögzíthet új terméket a rendszerhez.')
            ->emptyStateIcon('tabler-database-search')
            ->columns([
                TextColumn::make('brand_id')
                    ->label('Márka')
                    ->formatStateUsing(function ($record) {
                        return $record->brand?->name;
                    }),
                TextColumn::make('name')
                    ->label('Megnevezés')
                    ->formatStateUsing(function ($record) {
                        if (!empty($record->productsubcategory?->name)) {
                            $productcategoriesbadges =  '<p>' . $record->name . '</p>
                            <div style="display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 7px;"><span class="fi-badge flex items-center justify-center gap-x-1 rounded-md text-xs font-medium ring-1 ring-inset px-2 min-w-[theme(spacing.6)] py-1 fi-color-custom bg-custom-50 text-custom-600 ring-custom-600/10 dark:bg-custom-400/10 dark:text-custom-400 dark:ring-custom-400/30 fi-color-primary" style="--c-50:var(--primary-50);--c-400:var(--primary-400);--c-600:var(--primary-600);">' . $record->productmaincategory?->name . '</span><span class="fi-badge flex items-center justify-center gap-x-1 rounded-md text-xs font-medium ring-1 ring-inset px-2 min-w-[theme(spacing.6)] py-1 fi-color-custom bg-custom-50 text-custom-600 ring-custom-600/10 dark:bg-custom-400/10 dark:text-custom-400 dark:ring-custom-400/30 fi-color-primary" style="--c-50:var(--primary-50);--c-400:var(--primary-400);--c-600:var(--primary-600);">' . $record->productsubcategory?->name . '</span></div>';
                        }
                        if (empty($record->productsubcategory?->name)) {
                            $productcategoriesbadges = '<p>' . $record->name . '</p>
                        <div style="display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 7px;"><span class="fi-badge flex items-center justify-center gap-x-1 rounded-md text-xs font-medium ring-1 ring-inset px-2 min-w-[theme(spacing.6)] py-1 fi-color-custom bg-custom-50 text-custom-600 ring-custom-600/10 dark:bg-custom-400/10 dark:text-custom-400 dark:ring-custom-400/30 fi-color-primary" style="--c-50:var(--primary-50);--c-400:var(--primary-400);--c-600:var(--primary-600);">' . $record->productmaincategory?->name . '</span></div>';
                        }
                        return $productcategoriesbadges;
                    })
                    ->html()
                    // ->description(function ($record): HtmlString {
                    //     if ($record->description != null) {
                    //         $text = $record->description;
                    //         $wrapText = '...';
                    //         $count = 40;
                    //         if (strlen($record->description) > $count) {
                    //             preg_match('/^.{0,' . $count . '}(?:.*?)\b/siu', $record->description, $matches);
                    //             $text = $matches[0];
                    //         } else {
                    //             $wrapText = '';
                    //         }
                    //         return new HtmlString('<span class="text-gray-500 dark:text-gray-400" style="font-size:9pt;">' . $text . $wrapText . '</span>');
                    //     } else {
                    //         return new HtmlString('');
                    //     }
                    // })
                    ->searchable(['name', 'description']),
                TextColumn::make('properties')
                    ->label('Tulajdonságok')
                    ,
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    // EditAction::make()->icon('tabler-pencil'),
                    DeleteAction::make()->icon('tabler-trash'),
                ]),
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
            'index' => Pages\ListProducts::route('/'),
            // 'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string //ez kiírja a menü mellé, hogy mennyi ügyfél van már rögzítve
    {
        /** @var class-string<Model> $modelClass */
        $modelClass = static::$model;

        return (string) $modelClass::all()->count();
    }
}
