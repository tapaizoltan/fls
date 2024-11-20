<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use App\Models\Sale;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\Customer;
use Filament\Forms\Form;
use App\Enums\EventTypes;
use App\Enums\CauseOfLoss;
use App\Enums\SalesStatus;
use Filament\Tables\Table;
use App\Enums\WhereDidAFindUs;
use Filament\Resources\Resource;
use Filament\Resources\Pages\Page;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Grid;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\SubNavigationPosition;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use DragonCode\Contracts\Cashier\Auth\Auth;
use Filament\Forms\Components\ToggleButtons;
use Illuminate\Support\Facades\Notification;
use App\Filament\Resources\SaleResource\Pages;
use Illuminate\Support\Facades\Auth as FormAuth;
use Symfony\Contracts\Service\Attribute\Required;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SaleResource\RelationManagers;
use Filament\Notifications\Notification as ActionsNotification;

class SaleResource extends Resource
{
    protected static ?string $model = Sale::class;

    protected static ?string $navigationGroup = 'Értékesítés';
    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?string $modelLabel = 'értékesítési folyamat';
    protected static ?string $pluralModelLabel = 'értékesítési folyamatok';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(4)
                    ->schema([
                        Select::make('user_id')
                            ->label('Felelős személy')
                            ->helperText('Ha nem Ön a felelős személy akkor válassza ki a folyamatért felelős személyt.')
                            ->options(User::all()->pluck('name', 'id'))
                            ->default(FormAuth::id())
                            ->native(false)
                            ->searchable()
                            ->columnSpan(2),
                        Select::make('customer_id')
                            ->label('Ügyfél')
                            ->helperText('Válassza ki azt az ügyfelet, amelyikhez rögzíteni kívánja az új eseményt.')
                            ->options(Customer::all()->pluck('name', 'id'))
                            ->native(false)
                            ->searchable()
                            ->required()
                            ->columnSpan(2),
                        Hidden::make('sale_event_id')
                            ->default(fn() => Sale::generateSaleEventId())
                            ->required(),
                        Hidden::make('sale_event_key')
                            ->default(fn() => Sale::generateSaleEventKey())
                            ->required(),
                        Fieldset::make('Igényfelmérés')
                            ->schema([
                                Select::make('where_did_a_find_us')
                                    ->label('Hol talált ránk?')
                                    ->helperText('Válassza ki a megkeresés formályát.')
                                    ->options(WhereDidAFindUs::class)
                                    ->native(false)
                                    ->prefixIcon('tabler-ear-scan')
                                    ->searchable()
                                    ->required()
                                    ->columnSpan(2),

                                Textarea::make('what_are_you_interested_in')
                                    ->label('Mi iránt érdeklődik?')
                                    ->helperText('Írja le néhány sorban, hogy mi iránt érdeklődött az ügyfél.')
                                    ->rows(5)
                                    ->cols(20)
                                    ->required()
                                    ->columnSpan(2),
                            ]),

                        Hidden::make('status')
                            ->default('1')
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->heading('Értékesítési folyamatok, értékesítéssel kapcsolatos események.')
            ->description('Ebben a modulban rögzítheti és kezelheti az értékesítési folyamatokat, az értékesítéshez kapcsolódó eseményeket.')
            ->emptyStateHeading('Nincs megjeleníthető értékesítési folyamat vagy esemény.')
            ->emptyStateDescription('Az "Új értékesítés" gombra kattintva rögzíthet új értékesítési folyamatot, értékesítéssel kapcsolatos eseményt a rendszerhez.')
            ->emptyStateIcon('tabler-database-search')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Dátum')
                    ->formatStateUsing(function ($state) {
                        return Carbon::parse($state)->translatedFormat('Y F d. l');
                    })
                    ->searchable(),
                TextColumn::make('sale_event_id')
                    ->label('Értékesítési azonosító')
                    ->description(function ($record): HtmlString {
                        return new HtmlString('<span class="text-gray-500 dark:text-gray-400" style="font-size:9pt;"><b>Kulcs: </b>' . $record->sale_event_key . '</span>');
                    })
                    ->searchable(['sale_event_id', 'sale_event_key']),
                TextColumn::make('customer.name')
                    ->label('Név/Cégnév')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Státusz')
                    ->badge()
                    ->size('md')
                    ->searchable(),
                // TextColumn::make('where_did_a_find_us')
                //     ->label('Hol talált ránk?')
                //     ->badge()
                //     ->size('md')
                //     ->searchable(),
                TextColumn::make('user.name')
                    ->label('Felelős személy')
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make()
            ])
            ->actions([
                ActionGroup::make([
                    // Action::make('changeStatus')
                    //     ->label('Státusz módosítása')
                    //     ->icon('tabler-timeline-event-plus')
                    //     ->modalHeading('Státusz módosítása')
                    //     ->mountUsing(fn($form, $record) => $form->fill([
                    //         'status' => $record->status,
                    //     ]))
                    //     ->action(function ($data, $record) {
                    //         $record->update([
                    //             'status' => $data['status'],
                    //         ]);
                    //     })
                    //     ->form([
                    //         ToggleButtons::make('status')
                    //             ->label('Státusz')
                    //             ->inline()
                    //             // .. // ->options(function($state) {
                    //             // .. //     return
                    //             // .. //     collect(SalesStatus::cases())
                    //             // .. //     ->filter(fn($status) =>$status->value >= $state->value)
                    //             // .. //         ->mapWithKeys(fn ($status) => [$status->value => $status->getLabel()]);
                    //             // .. // })
                    //             // .. // ->colors(function($state) {
                    //             // .. //     return
                    //             // .. //     collect(SalesStatus::cases())
                    //             // .. //     ->filter(fn($status) =>$status->value >= $state->value)
                    //             // .. //         ->mapWithKeys(fn ($status) => [$status->value => $status->getColor()]);
                    //             // .. // })
                    //             // .. // ->icons(function($state) {
                    //             // .. //     return
                    //             // .. //     collect(SalesStatus::cases())
                    //             // .. //     ->filter(fn($status) =>$status->value >= $state->value)
                    //             // .. //         ->mapWithKeys(fn ($status) => [$status->value => $status->geticon()]);
                    //             // .. // })
                    //             ->options(collect(SalesStatus::cases())
                    //                 ->mapWithKeys(fn($status) => [$status->value => $status->getLabel()])
                    //                 ->toArray())
                    //             ->colors(collect(SalesStatus::cases())
                    //                 ->mapWithKeys(fn($status) => [$status->value => $status->getColor()])
                    //                 ->toArray())
                    //             ->icons(collect(SalesStatus::cases())
                    //                 ->mapWithKeys(fn($status) => [$status->value => $status->getIcon()])
                    //                 ->toArray())
                    //             ->disableOptionWhen(function (string $value, $state): bool {
                    //                 return $value < $state->value;
                    //             })
                    //             ->required(),

                    //     ]),

                    // Action::make('changeStatus')
                    //     ->label('Státusz módosítása')
                    //     ->icon('tabler-timeline-event-plus')
                    //     ->modalHeading('Státusz módosítása')
                    //     ->mountUsing(fn($form, $record) => $form->fill([
                    //         'status' => $record->status,
                    //     ]))
                    //     ->form([
                    //         ToggleButtons::make('status')
                    //             ->label('Státusz')
                    //             ->options(SalesStatus::cases()) // Automatikusan betölti az enum értékeket
                    //             ->required()
                    //             ->reactive(), // A státusz változását figyeli

                    //         Fieldset::make('Igényfelmérés')
                    //             ->visible(fn($get) => $get('status') === SalesStatus::DemandAssessment->value)
                    //             ->schema([
                    //                 TextInput::make('assessment_note')
                    //                     ->label('Igényfelmérési megjegyzés')
                    //                     ->required(),
                    //             ]),

                    //         Fieldset::make('Árajánlat adás')
                    //             ->visible(fn($get) => $get('status') === SalesStatus::PriceOffer->value)
                    //             ->schema([
                    //                 TextInput::make('offer_details')
                    //                     ->label('Árajánlat részletei')
                    //                     ->required(),
                    //             ]),

                    //         Fieldset::make('Szerződéskötés számlázás')
                    //             ->visible(fn($get) => $get('status') === SalesStatus::ConclusionOfContract->value)
                    //             ->schema([
                    //                 TextInput::make('contract_number')
                    //                     ->label('Szerződésszám')
                    //                     ->required(),
                    //                 DatePicker::make('contract_date')
                    //                     ->label('Szerződés dátuma')
                    //                     ->required(),
                    //             ]),

                    //         Fieldset::make('Sikeresen lezárt')
                    //             ->visible(fn($get) => $get('status') === SalesStatus::SuccessfullyClosed->value)
                    //             ->schema([
                    //                 TextInput::make('success_note')
                    //                     ->label('Sikeres zárás megjegyzés'),
                    //             ]),

                    //         Fieldset::make('Sikertelen lezárt')
                    //             ->visible(fn($get) => $get('status') === SalesStatus::UnsuccessfullyClosed->value)
                    //             ->schema([
                    //                 Textarea::make('failure_reason')
                    //                     ->label('Sikertelen zárás oka')
                    //                     ->required(),
                    //             ]),

                    //         Fieldset::make('Kiszállítás alatt')
                    //             ->visible(fn($get) => $get('status') === SalesStatus::UnderDelivery->value)
                    //             ->schema([
                    //                 TextInput::make('delivery_tracking')
                    //                     ->label('Követési szám'),
                    //             ]),

                    //         Fieldset::make('Átvéve')
                    //             ->visible(fn($get) => $get('status') === SalesStatus::Delivered->value)
                    //             ->schema([
                    //                 TextInput::make('receiver_name')
                    //                     ->label('Átvevő neve')
                    //                     ->required(),
                    //                 DatePicker::make('delivery_date')
                    //                     ->label('Átvétel dátuma')
                    //                     ->required(),
                    //             ]),
                    //     ])
                    //     ->action(function ($data, $record) {
                    //         $record->update([
                    //             'status' => $data['status'],
                    //         ]);
                    //         ActionsNotification::make()
                    //             ->title('A státusz módosítása sikerült!')
                    //             ->success()
                    //             ->send();
                    //     }),

                    Action::make('Státusz módosítása')
                        ->modalHeading('Új értékesítési státusz')
                        ->icon('tabler-timeline-event-plus')
                        ->form([
                            Fieldset::make('Esemény')
                                ->schema([
                                    ToggleButtons::make('status')
                                        ->label('Státusz')
                                        ->helperText('Válassza ki az esemény státuszát.')
                                        ->inline()
                                        ->required()
                                        ->options([
                                            '1' => 'Igényfelmérés',
                                            '2' => 'Árajánlat adás',
                                            '3' => 'Szerződéskötés számlázás',
                                            '4' => 'Sikeresen lezárt',
                                            '5' => 'Sikertelen lezárt',
                                            '6' => 'Kiszállítás alatt',
                                            '7' => 'Kiszállítva',
                                        ])
                                        ->colors([
                                            '1' => 'info',
                                            '2' => 'warning',
                                            '3' => 'warning',
                                            '4' => 'success',
                                            '5' => 'danger',
                                            '6' => 'warning',
                                            '7' => 'success',
                                        ])
                                        ->icons([
                                            '1' => 'tabler-message-question',
                                            '2' => 'tabler-keyboard',
                                            '3' => 'tabler-writing-sign',
                                            '4' => 'tabler-thumb-up',
                                            '5' => 'tabler-thumb-down',
                                            '6' => 'tabler-truck-delivery',
                                            '7' => 'tabler-truck-loading',
                                        ])
                                        ->default(function ($record) {
                                            return $record->status->value;
                                        })
                                        ->live()
                                        ->columnSpanFull(),

                                    Fieldset::make('Árajánlat adás')
                                        ->hidden(fn(Get $get): bool => ($get('status') != '2'))
                                        ->schema([
                                            DatePicker::make('date_of_offer')
                                                ->helperText('Adja meg azt a dátumot amikor kiadta az árajánlatot')
                                                ->label('Ajánlatadás dátuma')
                                                ->prefixIcon('tabler-calendar')
                                                ->weekStartsOnMonday()
                                                ->placeholder(now())
                                                ->displayFormat('Y-m-d')
                                                ->required()
                                                ->native(false)
                                                ->hidden(fn(Get $get): bool => ($get('status') != '2')),

                                            DatePicker::make('expected_closing_date')
                                                ->helperText('Adja meg azt a dátumot amikor várhatóan lezárásra kerül ez az esemény vagy ügylet.')
                                                ->label('Várható lezárás dátuma')
                                                ->prefixIcon('tabler-calendar')
                                                ->weekStartsOnMonday()
                                                ->placeholder(now())
                                                ->displayFormat('Y-m-d')
                                                ->required()
                                                ->native(false)
                                                ->hidden(fn(Get $get): bool => ($get('status') != '2')),

                                            TextInput::make('expected_sales_revenue')
                                                ->label('Várható árbevétel')
                                                ->helperText('Adja meg a prognosztizált árbevétel számszerű értékét.')
                                                ->prefixIcon('tabler-abacus')
                                                ->numeric()
                                                ->default(0)
                                                ->required()
                                                ->minLength(1)
                                                ->maxLength(10)
                                                ->suffix('Ft.')
                                                ->hidden(fn(Get $get): bool => ($get('status') != '2')),
                                        ]),

                                    Fieldset::make('Sikertelen ügylet visszamérése')
                                        ->hidden(fn(Get $get): bool => ($get('status') != '5'))
                                        ->schema([
                                            ToggleButtons::make('cause_of_loss')
                                                ->helperText('Válassza ki az elvesztés okát.')
                                                ->label('Státusz')
                                                ->inline()
                                                ->options(CauseOfLoss::class)
                                                ->hidden(fn(Get $get): bool => ($get('status') != '5'))
                                                ->required(),
                                            Textarea::make('cause_of_loss_description')
                                                ->label('Elvesztés okának leírása')
                                                ->helperText('Írja le néhány sorban, mi okozta, hogy meghiúsult az ügylet.')
                                                ->rows(5)
                                                ->cols(20)
                                                ->columnSpan(1)
                                                ->hidden(fn(Get $get): bool => ($get('status') != '5'))
                                                ->required(),
                                        ]),
                                ]),
                        ])

                        ->action(function ($data, $record) {
                            $record->update([
                                'status' => $data['status'],
                            ]);

                            ActionsNotification::make()
                                ->title('A státusz módosítása sikerült!')
                                ->success()
                                ->send();
                        }),
                    EditAction::make()->icon('tabler-pencil'),
                    DeleteAction::make()->icon('tabler-trash'),
                    // Tables\Actions\DissociateAction::make(),

                    Tables\Actions\ForceDeleteAction::make(),
                    Tables\Actions\RestoreAction::make(),
                ]),

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

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            // Pages\ViewSale::class,
            Pages\EditSale::class,
            Pages\ManageSalePriceoffers::class,
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
            'index' => Pages\ListSales::route('/'),
            // 'create' => Pages\CreateSale::route('/create'),
            'edit' => Pages\EditSale::route('/{record}/edit'),
            'priceoffers' => Pages\ManageSalePriceoffers::route('/{record}/priceoffers'),
        ];
    }

    public static function getNavigationBadge(): ?string //ez kiírja a menü mellé, hogy mennyi ügyfél van már rögzítve
    {
        /** @var class-string<Model> $modelClass */
        $modelClass = static::$model;

        return (string) $modelClass::all()->count();
    }
}
