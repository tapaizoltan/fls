<?php

namespace App\Filament\Resources\SaleResource\Pages;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use Filament\Actions;
use Nette\Utils\Html;
use App\Models\Product;
use Filament\Forms\Form;
use App\Models\Priceoffer;
use Filament\Tables\Table;
use App\Enums\PriceOffersStatus;
use Filament\Actions\DeleteAction;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Resources\SaleResource;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Placeholder;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Forms\Components\ToggleButtons;
use Filament\Resources\Pages\ManageRelatedRecords;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ManageSalePriceoffers extends ManageRelatedRecords
{
    protected static string $resource = SaleResource::class;

    protected static string $relationship = 'priceoffers';

    protected static ?string $navigationIcon = 'tabler-coffee';

    public static function getNavigationLabel(): string
    {
        return 'Árajánlat';
    }

    public function getTitle(): string | Htmlable
    {
        return "Árajánlat";
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('user_id')
                    ->default(auth()->id())
                    ->required(),
                Hidden::make('price_offer_id')
                    ->default(fn() => Priceoffer::generatePriceOfferId())
                    ->required(),
                Hidden::make('status')
                    ->default(1)
                    ->hiddenOn('edit')
                    ->required(),

                Grid::make(4)
                    ->schema([
                        Fieldset::make('Árajánlat státusz')
                            ->schema([
                                ToggleButtons::make('status')
                                    ->label('Státusz')
                                    ->helperText('Válassza ki az esemény státuszát.')
                                    ->hiddenOn('create')
                                    ->inline()
                                    ->required()
                                    ->options(PriceOffersStatus::class)
                                    ->columnSpanFull(),
                            ])
                            ->columnSpanFull()
                            ->hiddenOn('create'),
                        Fieldset::make('Értékesítési infó')
                            ->schema([
                                DatePicker::make('expected_closing_at')
                                    ->helperText('Adja meg azt a dátumot amikor várhatóan lezárásra kerül ez az ajánlat vagy ügylet.')
                                    ->label('Várható lezárás dátuma')
                                    ->prefixIcon('tabler-calendar')
                                    ->weekStartsOnMonday()
                                    ->placeholder(now())
                                    ->displayFormat('Y-m-d')
                                    ->required()
                                    ->native(false)
                                    ->columnSpanFull(),
                            ])
                            ->columnSpan([
                                'sm' => 4,
                                'md' => 4,
                                'lg' => 4,
                                'xl' => 2,
                                '2xl' => 2,
                            ]),
                        Section::make('Árajánlat végösszege')
                            ->schema([
                                Placeholder::make('offer_amount_calculate')
                                    ->label(false)
                                    ->content(function ($get, $set): HtmlString {
                                        $items = $get('priceofferitems') ?? [];
                                        $total = 0;

                                        foreach ($items as $item) {
                                            $netprice = $item['netprice'] ?? 0;
                                            $quantity = $item['quantity'] ?? 1;
                                            $discount = $item['discount'] ?? 0;

                                            // Nettó ár számítása
                                            $subtotal = ($netprice * $quantity) - ($netprice * $quantity * $discount / 100);
                                            $total += $subtotal;
                                        }
                                        $set('offer_amount', $total);
                                        return new HtmlString('<p style="font-size:30pt;">' . number_format($total, 0, ",", ".") . ' Forint</p>');
                                    })
                                    ->reactive(), // Fontos: A mező reagáljon a változásokra

                                Hidden::make('offer_amount'),
                            ])
                            ->columnSpan([
                                'sm' => 4,
                                'md' => 4,
                                'lg' => 4,
                                'xl' => 2,
                                '2xl' => 2,
                            ]),
                    ]),


                Repeater::make('priceofferitems')
                    ->label('Termékek')
                    ->addActionLabel('Termék hozzáadása')
                    ->relationship()
                    ->schema([
                        // Select::make('product_id')
                        //     ->label('Név')
                        //     ->options(Product::all()->pluck('width', 'id'))
                        //     ->searchable()
                        //     ->required()
                        //     ->reactive()
                        //     ->afterStateUpdated(function ($state, callable $set) {
                        //         $product = Product::find($state);
                        //         if ($product) {
                        //             $set('netprice', $product->netprice);
                        //         } else {
                        //             $set('netprice', null);
                        //         }
                        //     }),



                        Select::make('product_id')
                            ->label('Product')
                            ->options(Product::getGroupedProducts()) // Ez adja vissza a csoportosított termékeket
                            ->searchable() // Kereshetővé teszi a listát
                            ->placeholder('Select a product') // Alapértelmezett szöveg
                            ->required() // Kötelezővé teszi a mezőt
                            ->reactive(), // Frissíti a form mezőit, ha változás történik

                        // Placeholder a netprice megjelenítésére
                        Placeholder::make('netprice')
                            ->label('Nettó ár')
                            //->content(fn($get) => $get('netprice') ?? 'N/A'),
                            ->content(fn($get) => number_format($get('netprice'), 0, ",", ".") . ' Forint' ?? 'N/A'),
                        Hidden::make('netprice'),

                        // Quantity mező
                        TextInput::make('quantity')
                            ->label('Mennyiség')
                            ->numeric()
                            ->reactive()
                            ->suffix('darab')
                            ->default(1) // Alapértelmezett érték
                            ->required(),

                        // Discount mező
                        TextInput::make('discount')
                            ->label('Kedvezmény')
                            ->numeric()
                            ->reactive()
                            ->default(0)
                            ->suffix('%')
                            ->required(),

                        // Nettó összeg (net_total_price) megjelenítése
                        Placeholder::make('net_total_price_calculate')
                            ->label('Össz. nettó ár')
                            ->content(function ($get, $set) {
                                $netprice = $get('netprice') ?? 0;
                                $quantity = $get('quantity') ?? 1;
                                $discount = $get('discount') ?? 0;

                                // Számítás: (netprice * quantity) - (netprice * quantity * discount / 100)
                                $total = ($netprice * $quantity) - ($netprice * $quantity * $discount / 100);

                                $set('net_total_price', $total);
                                return number_format($total, 0, ",", ".") . ' Forint';
                            })
                            ->reactive(),

                        Hidden::make('net_total_price'),
                    ])
                    ->columns(3)
                    ->columnSpanFull()
                    ->reorderable()
                    ->reorderableWithButtons()
                    ->required()
                    ->reactive(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('Árajánlatok')
            ->heading('Értékesítési folyamathoz kapcsolt árajánlatok.')
            ->description('Ebben a modulban az értékesítési folyamathoz rögzítheti árajánlatait és kezelheti azok utánkövetését.')
            ->emptyStateHeading('Nincs megjeleníthető, az értékesítési folyamathoz köthető árajánlat.')
            ->emptyStateDescription('Az "Új árajánlat" gombra kattintva rögzíthet új, az értékesítési folyamathoz köthető árajánlatot.')
            ->emptyStateIcon('tabler-database-search')
            ->columns([

                TextColumn::make('created_at')
                    ->label('Dátum')
                    ->formatStateUsing(function ($record) {
                        return '<p><span class="text-gray-500 dark:text-gray-400" style="font-size:9pt;">Létrehozva: </span><span class="text-custom-600 dark:text-custom-400" style="font-size:11pt;">' . Carbon::parse($record->created_at)->translatedFormat('Y F d. l') . '</span></p>
                    <p><span class="text-gray-500 dark:text-gray-400" style="font-size:9pt;">Várható lezárás: </span><span class="text-custom-600 dark:text-custom-400" style="font-size:11pt;">' . Carbon::parse($record->expected_closing_at)->translatedFormat('Y F d. l') . '</span></p>';
                    })->html()
                    ->searchable(['created_at', 'expected_closing_at']),
                TextColumn::make('status')
                    ->label('Státusz')
                    ->badge()
                    ->size('md')
                    ->searchable(),
                TextColumn::make('offer_amount')
                    ->label('Ajánlat összege')
                    ->formatStateUsing(function ($state) {
                        return number_format($state, 0, ",", ".") . ' Forint';
                    })
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label('Létrehozó személy')
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make()
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Új árajánla')
                    ->icon('tabler-circle-plus')->slideOver(),
                //Tables\Actions\AssociateAction::make(),
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make()->icon('tabler-pencil'),
                    DeleteAction::make()->icon('tabler-trash'),

                    // Tables\Actions\DissociateAction::make(),

                    Tables\Actions\ForceDeleteAction::make(),
                    Tables\Actions\RestoreAction::make(),
                ])
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
