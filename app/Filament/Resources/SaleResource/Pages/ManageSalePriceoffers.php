<?php

namespace App\Filament\Resources\SaleResource\Pages;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use App\Models\Brand;
use Filament\Actions;
use Nette\Utils\Html;
use App\Models\Contact;
use App\Models\Product;
use Filament\Forms\Form;
use App\Models\Priceoffer;
use Filament\Tables\Table;
use App\Mail\OfferSentMail;
use App\Jobs\SendOfferEmail;
use App\Mail\PriceOfferMail;
use App\Enums\PriceOffersStatus;
use App\Models\Productsubcategory;
use Filament\Actions\DeleteAction;
use Illuminate\Support\HtmlString;
use App\Models\Productmaincategory;
use Filament\Forms\Components\Grid;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Mail;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Resources\SaleResource;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\MultiSelect;
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
                                            //$netprice = 111;
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

                        // Főkategória kiválasztása
                        Select::make('productmaincategory_id')
                            ->label('Főkategória')
                            ->options(ProductMainCategory::pluck('name', 'id'))
                            ->reactive()
                            ->live()
                            ->placeholder('Válassz főkategóriát')
                            ->required(), // Kötelezővé tesszük

                        // Alkategória kiválasztása
                        Select::make('productsubcategory_id')
                            ->label('Alkategória')
                            ->options(function (callable $get) {
                                $mainCategoryId = $get('productmaincategory_id');
                                return $mainCategoryId
                                    ? ProductSubCategory::where('productmaincategory_id', $mainCategoryId)->pluck('name', 'id')
                                    : [];
                            })
                            ->reactive()
                            ->live()
                            ->hidden(fn(callable $get) => !$get('productmaincategory_id'))
                            ->placeholder('Válassz alkategóriát')
                            ->required(), // Kötelezővé tesszük

                        // Márka kiválasztása
                        Select::make('brand_id')
                            ->label('Márka')
                            ->options(function (callable $get) {
                                $mainCategoryId = $get('productmaincategory_id');
                                $subCategoryId = $get('productsubcategory_id');
                                return ($mainCategoryId && $subCategoryId)
                                    ? Brand::whereHas('products', function ($query) use ($mainCategoryId, $subCategoryId) {
                                        $query->where('productmaincategory_id', $mainCategoryId)
                                            ->where('productsubcategory_id', $subCategoryId);
                                    })->pluck('name', 'id')
                                    : [];
                            })
                            ->reactive()
                            ->live()
                            ->hidden(fn(callable $get) => !$get('productmaincategory_id') || !$get('productsubcategory_id'))
                            ->placeholder('Válassz márkát')
                            ->required(), // Kötelezővé tesszük

                        // Termék kiválasztása
                        Select::make('product_id')
                            ->label('Termék')
                            ->options(function (callable $get) {
                                $mainCategoryId = $get('productmaincategory_id');
                                $subCategoryId = $get('productsubcategory_id');
                                $brandId = $get('brand_id');
                                return ($mainCategoryId && $subCategoryId && $brandId)
                                    ? Product::where('productmaincategory_id', $mainCategoryId)
                                    ->where('productsubcategory_id', $subCategoryId)
                                    ->where('brand_id', $brandId)
                                    ->get()
                                    ->mapWithKeys(function ($product) {
                                        return [
                                            $product->id => sprintf(
                                                '%d/%d%s%d',
                                                $product->width,
                                                $product->height,
                                                strtoupper($product->structure),
                                                $product->rim_diameter
                                            ),
                                        ];
                                    })
                                    : [];
                            })
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $latestPrice = Product::find($state)?->productprices()
                                    ->latest('created_at')
                                    ->first();

                                if ($latestPrice) {
                                    $set('netprice', $latestPrice->net_list_price_huf);
                                } else {
                                    $set('netprice', null);
                                }
                            })
                            ->live()
                            ->hidden(fn(callable $get) => !$get('productmaincategory_id') || !$get('productsubcategory_id') || !$get('brand_id'))
                            ->placeholder('Válassz terméket')

                            ->required(), // Kötelezővé tesszük

                        // Placeholder a netprice megjelenítésére
                        Placeholder::make('netprice')
                            ->label('Nettó ár')
                            //->content(fn($get) => $get('netprice') ?? 'N/A'),
                            ->content(fn($get) => $get('netprice') ? number_format($get('netprice'), 0, ',', '.') . ' Ft' : 'N/A'),
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
                ]),
                
                Action::make('sendOffer')
                    ->label('Ajánlat küldése')
                    ->icon('tabler-mail-forward')

                    // 1) Modal‑űrlap a kapcsolattartók listájával
                    ->form(function (Priceoffer $record) {
                        $customer = $record->sale->customer;

                        $options = $customer->contacts()
                            ->where('get_offer', true)
                            ->select('id', 'firstname', 'second_firstname', 'lastname')
                            ->get()
                            ->mapWithKeys(function ($contact) {
                                $fullName = trim("{$contact->lastname} {$contact->firstname}" .
                                    ($contact->second_firstname ? ' ' . $contact->second_firstname : ''));
                                return [$contact->id => $fullName];
                            });

                        return [
                            Select::make('contact_ids')
                                ->multiple()
                                ->label('Kapcsolattartók kiválasztása')
                                ->options($options)
                                ->searchable()
                                ->required()
                                ->placeholder('Válassz egy vagy több kapcsolattartót'),
                        ];
                    })

                    // 2) Letiltjuk a gombot, ha nincs címzett
                    ->disabled(
                        fn(Priceoffer $record) =>
                        $record->sale->customer
                            ->contacts()
                            ->where('get_offer', true)
                            ->doesntExist()
                    )

                    // 3) Submit logika
                    ->action(function (array $data, Priceoffer $record) {
                        $contacts = Contact::whereIn('id', $data['contact_ids'])->get();

                        if ($contacts->isEmpty()) {
                            Notification::make()
                                ->title('Nem található egyik kiválasztott kapcsolattartó sem.')
                                ->danger()
                                ->send();
                            return;
                        }

                        foreach ($contacts as $contact) {
                            $fullName = trim("{$contact->lastname} {$contact->firstname}" .
                                ($contact->second_firstname ? ' ' . $contact->second_firstname : ''));

                            dispatch(new SendOfferEmail(
                                $record,
                                $contact->email,
                                $fullName,
                            ));
                        }

                        Notification::make()
                            ->title('Ajánlat(ok) elküldve a kiválasztott kapcsolattartók részére!')
                            ->success()
                            ->send();
                    }),
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
