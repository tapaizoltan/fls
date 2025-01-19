<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Supplier;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\Productsubcategory;
use Filament\Resources\Pages\Page;
use Illuminate\Support\HtmlString;
use App\Models\Productmaincategory;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Filament\Pages\SubNavigationPosition;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\ToggleButtons;
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

    // public static function getBrandOptions()
    // {
    //     return Brand::all()
    //         ->groupBy(function ($brand) {
    //             return $brand->created_at; // Csoportosítás dátum szerint
    //         })
    //         ->map(function ($group) {
    //             return $group->pluck('name', 'id'); // A name és id értékek visszaadása
    //         });
    // }

    public static function getGroupedBrandOptions(): array
    {
        $brands = Brand::with('supplier')->get();
        $grouped = $brands->groupBy(function ($brand) {
            return $brand->supplier?->name ?? 'Nincs beszállító'; // Supplier név vagy alapértelmezett szöveg
        });
        $options = [];
        foreach ($grouped as $supplierName => $brands) {
            foreach ($brands as $brand) {
                $options[$supplierName][$brand->id] = $brand->name; // Csoport => [brand_id => brand_name]
            }
        }
        return $options;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(12)
                    ->schema([
                        Section::make()
                            ->schema([
                                // Select::make('brand_id')
                                //     ->label('Márka')
                                //     ->helperText('Válassza ki, vagy a "+" gombra kattintva, hozzon létre egy új márkanevet, amit később bármelyik termékhez használhat.')
                                //     //->options(Brand::all()->pluck('name', 'id'))
                                //     ->prefixIcon('tabler-layout-list')
                                //     ->preload()
                                //     ->relationship(name: 'brand', titleAttribute: 'name')
                                //     ->native(false)
                                //     ->searchable()
                                //     ->columnSpan(1)
                                //     ->createOptionModalHeading('Új márka, márkanév')
                                //     ->createOptionAction(fn($action) => $action->modalWidth('sm'))
                                //     ->createOptionForm([

                                //         TextInput::make('name')
                                //             ->label('Márkanév')
                                //             ->helperText('Adja meg az új márka nevét.')
                                //             ->required()
                                //             ->columnSpanFull()
                                //             ->unique(),
                                //     ]),
                                Select::make('brand_id')
                                    ->label('Márka')
                                    ->helperText('Válassza ki, vagy a "+" gombra kattintva, hozzon létre egy új márkanevet, amit később bármelyik termékhez használhat.')
                                    ->options(self::getGroupedBrandOptions())
                                    ->prefixIcon('tabler-layout-list')
                                    ->preload()
                                    ->searchable()
                                    // ->relationship(name: 'brand', titleAttribute: 'name')
                                    ->native(false)
                                    ->columnSpan(1)
                                    ->createOptionModalHeading('Új márka, márkanév')
                                    ->createOptionAction(fn($action) => $action->modalWidth('xl'))
                                    ->createOptionForm([
                                        Grid::make(2)
                                            ->schema([
                                                Select::make('supplier_id')
                                                    ->label('Beszállító')
                                                    ->helperText('Válassza ki a beszállítót.')
                                                    ->options(Supplier::all()->pluck('name', 'id'))
                                                    //->relationship(name: 'supplier', titleAttribute: 'name')
                                                    ->prefixIcon('tabler-layout-list')
                                                    ->preload()
                                                    ->required()
                                                    ->searchable()
                                                    ->columns(1)
                                                    ->native(false),
                                                TextInput::make('name')
                                                    ->label('Márkanév')
                                                    ->helperText('Adja meg az új márka nevét.')
                                                    ->required()
                                                    ->columns(1)
                                                    ->unique(),
                                            ]),
                                    ])
                                    ->createOptionUsing(function (array $data): int {
                                        $brand = Brand::create([
                                            'supplier_id' => $data['supplier_id'],
                                            'name' => $data['name'],
                                        ]);

                                        Notification::make()
                                            ->title('Az új márkanév rögzítése sikerült!')
                                            ->success()
                                            ->send();

                                        return $brand->getKey();
                                    })
                                    ->required(),
                            ])->columnSpan([
                                'sm' => 6,
                                'md' => 6,
                                'lg' => 6,
                                'xl' => 6,
                                '2xl' => 6,
                            ]),

                        Section::make()
                            ->schema([
                                Select::make('productmaincategory_id')
                                    ->label('Főkategória')
                                    ->helperText('Válassza ki, vagy a "+" gombra kattintva, hozzon létre egy új főkategóriát, amit később bármelyik termékhez használhat.')
                                    ->prefixIcon('tabler-layout-list')
                                    ->preload()
                                    ->options(function () {
                                        return Productmaincategory::pluck('name', 'id');
                                    })
                                    //->relationship(name: 'productmaincategory', titleAttribute: 'name')
                                    ->native(false)
                                    ->searchable()
                                    ->columnSpan(1)
                                    ->createOptionAction(fn($action) => $action->modalWidth('md'))
                                    ->createOptionModalHeading('Új főkategória')
                                    ->createOptionForm([
                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('name')
                                                    ->label('Főkategória neve')
                                                    ->helperText('Adja meg az új főkategória nevét. Célszerű olyat választani ami a későbbiekben segítségére lehet a könnyebb azonosítás tekintetében.')
                                                    ->required()
                                                    ->columnSpanFull()
                                                    ->unique(),
                                            ])
                                    ])
                                    ->createOptionUsing(function (array $data): int {

                                        $category = ProductMainCategory::create([
                                            'name' => $data['name'],
                                        ]);

                                        // Értesítés küldése
                                        Notification::make()
                                            ->title('Az új főkategória rögzítése sikerült!')
                                            ->success()
                                            ->send();

                                        return $category->getKey();
                                    }),
                            ])->columnSpan([
                                'sm' => 6,
                                'md' => 6,
                                'lg' => 6,
                                'xl' => 6,
                                '2xl' => 6,
                            ]),

                        Section::make()
                            ->schema([
                                Select::make('productsubcategory_id')
                                    ->label('Alkategória')
                                    ->helperText('Válassza ki, vagy a "+" gombra kattintva, hozzon létre egy új alkategóriát, amit később bármelyik termékhez használhat.')
                                    ->prefixIcon('tabler-layout-list')
                                    ->preload()
                                    ->options(function () {
                                        return Productsubcategory::pluck('name', 'id');
                                    })
                                    //->relationship(name: 'productsubcategory', titleAttribute: 'name')
                                    ->native(false)
                                    ->searchable()
                                    ->columnSpan(1)
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
                                                    ->required(),
                                                //->dehydrated(false),
                                                TextInput::make('name')
                                                    ->label('Alkategória neve')
                                                    ->helperText('Adja meg az új alkategória nevét. Célszerű olyat választani ami a későbbiekben segítségére lehet a könnyebb azonosítás tekintetében.')
                                                    ->required()
                                                    ->columnSpan(1)
                                                    ->unique(),
                                            ])

                                    ])
                                    ->createOptionUsing(function (array $data): int {
                                        $subcategory = Productsubcategory::create([
                                            'productmaincategory_id' => $data['productmaincategory_id'],
                                            'name' => $data['name'],
                                        ]);

                                        Notification::make()
                                            ->title('Az új alkategória rögzítése sikerült!')
                                            ->success()
                                            ->send();

                                        return $subcategory->getKey();
                                    }),
                            ])->columnSpan([
                                'sm' => 6,
                                'md' => 6,
                                'lg' => 6,
                                'xl' => 6,
                                '2xl' => 6,
                            ]),

                        Section::make()
                            ->schema([
                                FileUpload::make('image_path')
                                    ->label('Kép feltöltése')
                                    ->helperText('Feltölthet fényképet a termékről, hogy az könnyebben beazonosítható legyen.')
                                    ->directory('form-attachments')
                                    ->image()
                                    ->maxSize(10000),
                            ])->columnSpan([
                                'sm' => 6,
                                'md' => 6,
                                'lg' => 6,
                                'xl' => 6,
                                '2xl' => 6,
                            ]),
                    ]),

                Grid::make(12)
                    ->schema([
                        Fieldset::make('Termék alap paraméterek')
                            ->schema([
                                ToggleButtons::make('season')
                                    ->label('Évszak')
                                    ->helperText('Válassza ki az adott termék évszaknak megfelelő besorolását.')
                                    ->inline()
                                    // ->grouped()
                                    ->options([
                                        '1' => 'Nyári',
                                        '2' => 'Téli',
                                        '3' => 'Négy évszakos',
                                        '4' => 'Egyéb',
                                    ])
                                    ->colors([
                                        '1' => 'info',
                                        '2' => 'info',
                                        '3' => 'info',
                                        '4' => 'warning',
                                    ])
                                    //->disabled(!auth()->user()->hasRole(['super_admin']))
                                    //->required()
                                    ->default(1),
                                    
                                ToggleButtons::make('structure')
                                    ->label('Szerkezet')
                                    ->helperText('Válassza ki az adott termék szerkezetét.')
                                    ->inline()
                                    // ->grouped()
                                    ->options([
                                        'R' => 'Radiál',
                                        'D' => 'Diagonál',
                                        'B' => 'Bias',
                                    ])
                                    ->colors([
                                        'R' => 'info',
                                        'D' => 'info',
                                        'B' => 'info',
                                    ])
                                    //->disabled(!auth()->user()->hasRole(['super_admin']))
                                    //->default('R')
                                    //->required()
                                    ,
                                TextInput::make('width')
                                    ->label('Szélesség')
                                    ->helperText('Adja meg a termék szélességét.')
                                    ->numeric()
                                    ->required()
                                    ->prefixIcon('tabler-ruler-3')
                                    ->suffix('col'),
                                TextInput::make('height')
                                    ->label('Magasság')
                                    ->helperText('Adja meg a termék magasságát.')
                                    ->numeric()
                                    ->required()
                                    ->prefixIcon('tabler-ruler')
                                    ->suffix('col'),
                                TextInput::make('rim_diameter')
                                    ->label('Felni átmérő')
                                    ->helperText('Adja meg a termék felni átmérőjét.')
                                    ->numeric()
                                    ->required()
                                    ->prefixIcon('tabler-restore')
                                    ->suffix('col'),
                            ])
                            ->columnSpan([
                                'sm' => 12,
                                'md' => 12,
                                'lg' => 12,
                                'xl' => 12,
                                '2xl' => 12,
                            ]),

                        Fieldset::make('Termék bővített paraméterek')
                            ->schema([
                                TextInput::make('outer_diameter')
                                    ->label('Külső átmérő')
                                    ->helperText('Adja meg a termék külső átmérőjét.')
                                    ->numeric()
                                    ->prefixIcon('tabler-ruler-3')
                                    ->suffix('cm'),
                                TextInput::make('load_capacity')
                                    ->label('Névleges teherbírás')
                                    ->helperText('Adja meg a termék névleges teherbírását.')
                                    ->numeric()
                                    ->prefixIcon('tabler-weight')
                                    ->suffix('col'),
                                ToggleButtons::make('internal_structure')
                                    ->label('Belső kialakítás')
                                    ->helperText('Válassza ki az adott termék belső kialakítását.')
                                    ->inline()
                                    // ->grouped()
                                    ->options([
                                        '1' => 'Tömör',
                                        '2' => 'Fujt',
                                        '3' => 'Tömlővel és védőszalaggal',
                                        '4' => 'Töltött',
                                    ])
                                    ->colors([
                                        '1' => 'info',
                                        '2' => 'info',
                                        '3' => 'info',
                                        '4' => 'info',
                                    ])
                                    //->disabled(!auth()->user()->hasRole(['super_admin']))
                                    //->default(1),
                                    ,
                                ToggleButtons::make('color')
                                    ->label('Szín')
                                    ->helperText('Válassza ki az adott termék színét.')
                                    ->inline()
                                    // ->grouped()
                                    ->options([
                                        '1' => 'Normál',
                                        '2' => 'Nyomot nem hagyó',
                                    ])
                                    ->colors([
                                        '1' => 'info',
                                        '2' => 'info',
                                    ])
                                    //->disabled(!auth()->user()->hasRole(['super_admin']))
                                    //->default(1),
                                    ,
                                TextInput::make('pattern_code')
                                    ->label('Mintázat kódja')
                                    ->helperText('Adja meg a termék mintázatának kódját.')
                                    ->prefixIcon('tabler-grid-4x4'),
                                TextInput::make('pattern_depth')
                                    ->label('Mintázat mélysége')
                                    ->helperText('Adja meg a termék mintázatának mélységét.')
                                    ->numeric()
                                    ->prefixIcon('tabler-ruler-2')
                                    ->suffix('mm'),
                                Textarea::make('description')
                                    ->label('Megjegyzés')
                                    ->helperText('Itt rögzíthet néhány fontosnak ítélt információt a termékkel kapcsolatban.')
                                    ->rows(10)
                                    ->cols(20)
                                    ->columnSpanFull(),
                            ])
                            ->columnSpan([
                                'sm' => 12,
                                'md' => 12,
                                'lg' => 12,
                                'xl' => 12,
                                '2xl' => 12,
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
                TextColumn::make('width')
                    ->label('Méret')
                    ->formatStateUsing(function ($record) {
                        return '<p><span class="text-custom-600 dark:text-custom-400" style="font-size:11pt; text-transform: uppercase; ">' . $record->width . '/' . $record->height . $record->structure . $record->rim_diameter . '</span></p>';
                        //return $record->width . '/' . $record->height . $record->structure . $record->rim_diameter;
                    })->html()
                    ->searchable(['width', 'height', 'structure', 'rim_diameter'])
                    ->description(function ($record): HtmlString {
                        if ($record->description != null) {
                            $text = $record->description;
                            $wrapText = '...';
                            $count = 40;
                            if (strlen($record->description) > $count) {
                                preg_match('/^.{0,' . $count . '}(?:.*?)\b/siu', $record->description, $matches);
                                $text = $matches[0];
                            } else {
                                $wrapText = '';
                            }
                            return new HtmlString('<span class="text-gray-500 dark:text-gray-400" style="font-size:9pt;">' . $text . $wrapText . '</span>');
                        } else {
                            return new HtmlString('');
                        }
                    }),
                ImageColumn::make('image_path')
                    ->label('Termékfotó')
                    //->square()
                    ->circular(),

                TextColumn::make('brand_id')
                    ->label('Márka')
                    ->sortable()
                    ->formatStateUsing(function ($record) {
                        return $record->brand?->name;
                    })
                    ->searchable(query: function ($query, $search) {
                        return $query->whereHas('brand', function ($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%");
                        });
                    }),

                TextColumn::make('productMainCategory.name')
                    ->label('Főkategória')
                    ->formatStateUsing(function ($record) {
                        return new HtmlString('<div style="display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 7px;"><span class="fi-badge flex items-center justify-center gap-x-1 rounded-md text-xs font-medium ring-1 ring-inset px-2 min-w-[theme(spacing.6)] py-1 fi-color-custom bg-custom-50 text-custom-600 ring-custom-600/10 dark:bg-custom-400/10 dark:text-custom-400 dark:ring-custom-400/30 fi-color-primary" style="--c-50:var(--primary-50);--c-400:var(--primary-400);--c-600:var(--primary-600);">' . $record->productMainCategory?->name . '</div></span>');
                    })
                    ->sortable()
                    ->searchable(query: function ($query, $search) {
                        return $query->whereHas('productMainCategory', function ($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%");
                        });
                    }),

                TextColumn::make('productSubCategory.name')
                    ->label('Alkategória')
                    ->formatStateUsing(function ($record) {
                        return new HtmlString('<div style="display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 7px;"><span class="fi-badge flex items-center justify-center gap-x-1 rounded-md text-xs font-medium ring-1 ring-inset px-2 min-w-[theme(spacing.6)] py-1 fi-color-custom bg-custom-50 text-custom-600 ring-custom-600/10 dark:bg-custom-400/10 dark:text-custom-400 dark:ring-custom-400/30 fi-color-primary" style="--c-50:var(--primary-50);--c-400:var(--primary-400);--c-600:var(--primary-600);">' . $record->productSubCategory?->name . '</div></span>');
                    })
                    ->sortable()
                    ->searchable(query: function ($query, $search) {
                        return $query->whereHas('productSubCategory', function ($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%");
                        });
                    }),

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

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewProduct::class,
            Pages\EditProduct::class,
            Pages\ManageProductProductprices::class,
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
            'view' => Pages\ViewProduct::route('/{record}'),
            'productprices' => Pages\ManageProductProductprices::route('/{record}/productprices'),
        ];
    }

    public static function getNavigationBadge(): ?string //ez kiírja a menü mellé, hogy mennyi ügyfél van már rögzítve
    {
        /** @var class-string<Model> $modelClass */
        $modelClass = static::$model;

        return (string) $modelClass::all()->count();
    }
}
