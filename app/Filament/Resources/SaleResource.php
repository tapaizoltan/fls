<?php

namespace App\Filament\Resources;

use App\Enums\CauseOfLoss;
use Filament\Forms;
use App\Models\Sale;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\Customer;
use Filament\Forms\Form;
use App\Enums\EventTypes;
use App\Enums\SalesStatus;
use Filament\Tables\Table;
use App\Enums\WhereDidAFindUs;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Grid;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
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

    protected static ?string $modelLabel = 'értékesítés';
    protected static ?string $pluralModelLabel = 'értékesítések';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(4)
                    ->schema([
                        Select::make('user_id')
                            ->label('Felelős személy')
                            ->helperText('Ha nem Ön a felelős zemély akkor válassza ki a folyamatért felelős személyt.')
                            ->options(User::all()->pluck('name', 'id'))
                            ->default(FormAuth::id())
                            ->native(false)
                            ->searchable()
                            ->columnSpan(2),
                        Select::make('customer_id')
                            ->label('Ügyfél')
                            ->helperText('Válassza ki azt az ügyfelet, emlyikhez rögzíteni kívánja az új eseményt.')
                            ->options(Customer::all()->pluck('name', 'id'))
                            ->native(false)
                            ->searchable()
                            ->required()
                            ->columnSpan(2),

                        Fieldset::make('Esemény')
                            ->schema([
                                ToggleButtons::make('event_type')
                                    ->helperText('Válassza ki az esemény típusát.')
                                    ->label('Típus')
                                    ->inline()
                                    ->required()
                                    ->live()
                                    //->options(EventTypes::class)
                                    ->options([
                                        '1' => 'Feltérképezés',
                                        '2' => 'Árajánlat kiadás',
                                        '3' => 'Értékesítés folyamatban',
                                        '4' => 'Lezárt nyert',
                                        '5' => 'Lezárt vesztett',
                                    ])
                                    ->icons([
                                        '1' => 'tabler-radar',
                                        '2' => 'tabler-replace',
                                        '3' => 'tabler-cash',
                                        '4' => 'tabler-thumb-up',
                                        '5' => 'tabler-thumb-down',
                                    ])
                                    ->colors([
                                        '1' => 'info',
                                        '2' => 'warning',
                                        '3' => 'warning',
                                        '4' => 'success',
                                        '5' => 'danger',
                                    ])
                                    ->default(1),
                                ToggleButtons::make('status')
                                    ->helperText('Válassza ki az esemény státuszát.')
                                    ->label('Státusz')
                                    ->inline()
                                    // ->options(SalesStatus::class)
                                    ->required()
                                    ->options(function (Get $get) {
                                        if ($get('event_type') == 1) {
                                            return ['1' => 'Igényfelmérés',];
                                        }
                                        if ($get('event_type') == 2) {
                                            return ['2' => 'Árajánlat adás', '3' => 'Árajánlat utánkövetés',];
                                        }
                                        if ($get('event_type') == 3) {
                                            return ['4' => 'Szerződéskötés, számlázás',];
                                        }
                                        if ($get('event_type') == 4) {
                                            return ['4' => 'Sikeres lezárt',];
                                        }
                                        if ($get('event_type') == 5) {
                                            return ['4' => 'Sikertelen lezárt',];
                                        }
                                    })
                                    ->icons([
                                        '1' => 'tabler-brand-flightradar24',
                                        '2' => 'tabler-file-symlink',
                                        '3' => 'tabler-reorder',
                                        '4' => 'tabler-heart-handshake',
                                        '5' => 'tabler-thumb-up',
                                        '6' => 'tabler-thumb-down',
                                    ])
                                    ->colors([
                                        '1' => 'info',
                                        '2' => 'info',
                                        '3' => 'info',
                                        '4' => 'info',
                                        '5' => 'info',
                                        '6' => 'info',
                                    ]),

                                // == ezt kell átnézni majd live() miatt

                                // ToggleButtons::make('event_type')
                                //     ->helperText('Válassza ki az esemény típusát.')
                                //     ->label('Esemény típusa')
                                //     ->inline()
                                //     ->live()
                                //     //->afterStateUpdated(fn (Set $set) => $set ('eventtype', NULL))
                                //     ->required()
                                //     ->options([
                                //         '1' => 'Feltérképezés',
                                //         '2' => 'Árajánlat kiadás',
                                //         '3' => 'Értékesítés folyamatban',
                                //         '4' => 'Lezárt nyert',
                                //         '5' => 'Lezárt vesztett',
                                //     ])
                                //     ->icons([
                                //         '1' => 'tabler-radar',
                                //         '2' => 'tabler-replace',
                                //         '3' => 'tabler-cash',
                                //         '4' => 'tabler-thumb-up',
                                //         '5' => 'tabler-thumb-down',
                                //     ])
                                //     ->colors([
                                //         '1' => 'info',
                                //         '2' => 'warning',
                                //         '3' => 'warning',
                                //         '4' => 'success',
                                //         '5' => 'danger',
                                //     ])
                                //     ->default(1)
                                //     ->columnSpan('full'),

                                // ToggleButtons::make('status')
                                //     ->helperText('Válassza ki az esemény státuszát.')
                                //     ->label('Esemény státusz')
                                //     ->inline()
                                //     ->required()
                                //     ->options(function (Get $get) {
                                //         if ($get('event_type') == 1) {
                                //             return ['1' => 'Igényfelmérés',];
                                //         }
                                //         if ($get('event_type') == 2) {
                                //             return ['2' => 'Árajánlat adás', '3' => 'Árajánlat utánkövetés',];
                                //         }
                                //         if ($get('event_type') == 3) {
                                //             return ['4' => 'Szerződéskötés, számlázás',];
                                //         }
                                //         if ($get('event_type') == 4) {
                                //             return ['4' => 'Sikeres lezárt',];
                                //         }
                                //         if ($get('event_type') == 5) {
                                //             return ['4' => 'Sikertelen lezárt',];
                                //         }
                                //     })
                                //     ->icons([
                                //         '1' => 'tabler-brand-flightradar24',
                                //         '2' => 'tabler-file-symlink',
                                //         '3' => 'tabler-reorder',
                                //         '4' => 'tabler-heart-handshake',
                                //         '5' => 'tabler-thumb-up',
                                //         '6' => 'tabler-thumb-down',
                                //     ])
                                //     ->colors([
                                //         '1' => 'info',
                                //         '2' => 'warning',
                                //         '3' => 'warning',
                                //         '4' => 'warning',
                                //         '5' => 'success',
                                //         '6' => 'danger',
                                //     ])
                                // ->default(function (Get $get) {
                                //     if ($get('event_type') == 1) {
                                //         return 1;
                                //     }
                                //     if ($get('event_type') == 2) {
                                //         return 2;
                                //     }
                                // })
                                // ->columnSpan('full'),
                            ]),

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

                        Fieldset::make('Értékesítési infók')
                            ->schema([
                                DatePicker::make('date_of_offer')
                                    //->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Adjon egy fantázianevet a légijárműnek. Érdemes olyan nevet választani, amivel könnyedén azonosítható lesz az adott légijármű.')
                                    ->helperText('Adja meg azt a dátumot amikor kiadta az árajánlatot')
                                    ->label('Ajánlatadás dátuma')
                                    ->prefixIcon('tabler-calendar')
                                    ->weekStartsOnMonday()
                                    ->placeholder(now())
                                    ->displayFormat('Y-m-d')
                                    ->required()
                                    ->native(false),

                                DatePicker::make('expected_closing_date')
                                    //->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Adjon egy fantázianevet a légijárműnek. Érdemes olyan nevet választani, amivel könnyedén azonosítható lesz az adott légijármű.')
                                    ->helperText('Adja meg azt a dátumot amikor várhatóan lezárásra kerül ez az esemény vagy ügylet.')
                                    ->label('Várható lezárás dátuma')
                                    ->prefixIcon('tabler-calendar')
                                    ->weekStartsOnMonday()
                                    ->placeholder(now())
                                    ->displayFormat('Y-m-d')
                                    ->required()
                                    ->native(false),

                                TextInput::make('expected_sales_revenue')
                                    ->label('Várható árbevétel')
                                    ->helperText('Adja meg a prognosztizált árbevétel számszerű értékét.')
                                    ->prefixIcon('tabler-abacus')
                                    ->numeric()
                                    ->default(0)
                                    ->required()
                                    ->minLength(1)
                                    ->maxLength(10)
                                    // ->disabled(!auth()->user()->hasRole(['super_admin']))
                                    //->suffix(fn(Get $get) => ($get('fee_type') == 1 ? '%' : 'Ft.'))
                                    ->suffix('Ft.'),

                                Textarea::make('sales_info')
                                    ->label('Értékesítési infó')
                                    ->helperText('Írja le néhány sorban, értékesítéshez fontos információit.')
                                    ->rows(5)
                                    ->cols(20)
                                    ->columnSpan(1),
                            ]),
                        Fieldset::make('Árajánlat(ok)')
                            ->schema([]),
                        Fieldset::make('Sikertelen ügylet visszamérése')
                            ->schema([
                                ToggleButtons::make('cause_of_loss')
                                    ->helperText('Válassza ki az elvesztés okát.')
                                    ->label('Státusz')
                                    ->inline()
                                    ->options(CauseOfLoss::class)
                                    ->required(),
                                Textarea::make('cause_of_loss_description')
                                    ->label('Elvesztés okának leírása')
                                    ->helperText('Írja le néhány sorban, mi okozta, hogy meghiúsult az ügylet.')
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
            ->heading('Értékesítés, értékesítési folyamatok, értékesítéssel kapcsolatos események.')
            ->description('Ebben a modulban rögzítheti és kezelheti az értékesítési folyamatokat, az értékesítéshez kapcsolódó eseményeket.')
            ->emptyStateHeading('Nincs megjeleníthető értékesítési folyamat vagy esemény.')
            ->emptyStateDescription('Az "Új értékesítés" gombra kattintva rögzíthet új értékesítési folyamatot, értékesítéssel kapcsolatos eseményt a rendszerhez.')
            ->emptyStateIcon('tabler-database-search')
            ->columns([
                TextColumn::make('customer.name')
                    ->label('Név/Cégnév')
                    ->searchable(),
                TextColumn::make('where_did_a_find_us')
                    ->label('Hol talált ránk?')
                    ->badge()
                    ->size('md')
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
                //
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('Új esemény')
                        ->icon('tabler-timeline-event-plus')
                        ->modalHeading('Új esemény rögzítése')
                        ->form([
                            ToggleButtons::make('event_type')
                                ->helperText('Válassza ki az esemény típusát.')
                                ->label('Típus')
                                ->inline()
                                ->required()
                                ->options(EventTypes::class)
                                ->default(1),
                            ToggleButtons::make('status')
                                ->helperText('Válassza ki az esemény státuszát.')
                                ->label('Státusz')
                                ->inline()
                                ->options(SalesStatus::class)
                                ->required(),
                        ])
                        ->action(function (array $data, Sale $record): void {
                            $record->event_type = $data['event_type'];
                            $record->status = $data['status'];
                            $record->save();

                            ActionsNotification::make()
                                ->title('Az Új esemény rögzítése sikerült!')
                                ->success()
                                ->send();
                        }),
                    EditAction::make()->icon('tabler-pencil'),
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
            'index' => Pages\ListSales::route('/'),
            // 'create' => Pages\CreateSale::route('/create'),
            'edit' => Pages\EditSale::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string //ez kiírja a menü mellé, hogy mennyi ügyfél van már rögzítve
    {
        /** @var class-string<Model> $modelClass */
        $modelClass = static::$model;

        return (string) $modelClass::all()->count();
    }
}
