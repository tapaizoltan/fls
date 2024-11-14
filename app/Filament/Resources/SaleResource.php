<?php

namespace App\Filament\Resources;

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
                TextColumn::make('status')
                    ->label('Státusz')
                    ->badge()
                    ->size('md')
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
                TextColumn::make('where_did_a_find_us')
                    ->label('Hol talált ránk?')
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
                    Action::make('changeStatus')
                        ->label('Státusz módosítása')
                        ->icon('tabler-timeline-event-plus')
                        ->modalHeading('Státusz módosítása')
                        ->mountUsing(fn ($form, $record) => $form->fill([
                            'status' => $record->status,
                        ]))
                        ->action(function ($data, $record) {
                            $record->update([
                                'status' => $data['status'],
                            ]);
                        })
                        ->form([
                            ToggleButtons::make('status')
                                ->label('Státusz')
                                ->inline()
                                // ->options(function($state) {
                                //     return
                                //     collect(SalesStatus::cases())
                                //     ->filter(fn($status) =>$status->value >= $state->value)
                                //         ->mapWithKeys(fn ($status) => [$status->value => $status->getLabel()]);
                                // })
                                // ->colors(function($state) {
                                //     return
                                //     collect(SalesStatus::cases())
                                //     ->filter(fn($status) =>$status->value >= $state->value)
                                //         ->mapWithKeys(fn ($status) => [$status->value => $status->getColor()]);
                                // })
                                // ->icons(function($state) {
                                //     return
                                //     collect(SalesStatus::cases())
                                //     ->filter(fn($status) =>$status->value >= $state->value)
                                //         ->mapWithKeys(fn ($status) => [$status->value => $status->geticon()]);
                                // })
                                ->options(collect(SalesStatus::cases())
                                    ->mapWithKeys(fn ($status) => [$status->value => $status->getLabel()])
                                    ->toArray())
                                ->colors(collect(SalesStatus::cases())
                                    ->mapWithKeys(fn ($status) => [$status->value => $status->getColor()])
                                    ->toArray())
                                ->icons(collect(SalesStatus::cases())
                                    ->mapWithKeys(fn ($status) => [$status->value => $status->getIcon()])
                                    ->toArray())
                                ->disableOptionWhen(function (string $value, $state): bool {
                                        return $value < $state->value;
                                    })
                                ->required(),
                        ]),
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
