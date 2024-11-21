<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use Filament\Forms;
use Filament\Tables;
use Filament\Actions;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\AreaType;
use Filament\Forms\Form;
use App\Models\Settlement;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Support\Htmlable;
use App\Filament\Resources\CustomerResource;
use Filament\Forms\Components\ToggleButtons;
use Filament\Resources\Pages\ManageRelatedRecords;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ManageCustomerAddresses extends ManageRelatedRecords
{
    protected static string $resource = CustomerResource::class;

    protected static string $relationship = 'addresses';

    protected static ?string $navigationIcon = 'tabler-map-pin-2';

    public static function getNavigationLabel(): string
    {
        return 'Cím(ek)';
    }

    public function getTitle(): string | Htmlable
    {
        return 'Cím(ek) szerkesztése';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('addresstype_id')
                    ->label('Cím típusa, jellege')
                    ->helperText('Válassza ki, vagy a "+" gombra kattintva, hozzon létre egy új jelleget, típust az adott címhez.')
                    ->prefixIcon('tabler-layout-list')
                    ->preload()
                    //->options(Region::all()->pluck('name', 'id'))
                    ->relationship(name: 'addresstype', titleAttribute: 'name')
                    ->native(false)
                    ->required()
                    ->searchable()
                    ->createOptionForm([
                        TextInput::make('name')->label('Típus, jelleg neve')->helperText('Adja meg az új jelleg, típus nevét. Célszerű olyat választani ami a későbbiekben segítségére lehet a könnyebb azonosítás tekintetében.')
                            ->required()->unique(),
                    ]),
                /*
                TextInput::make('name')
                ->hidden(fn (Get $get): bool => ($get('combination_type')!='5'))
                ->helperText('Adjon egy nevet a címnek. (pl.: halőrház, stb.)')
                ->label('Cím neve')
                ->prefixIcon('tabler-writing-sign')
                ->required()
                ->minLength(3)
                ->maxLength(255)
                ->columns('2'),
                */
                /*
                ToggleButtons::make('country_type')
                ->helperText('Válassza ki a rögzíteni kívánt kapcsolat típusát.')
                ->label('Ország')
                ->inline()
                ->live()
                ->required()
                ->options([
                    '0' => 'Magyar cím',
                    '1' => 'Külföldi cím',
                ])
                ->icons([
                    '0' => 'tabler-pepper',
                    '1' => 'heroicon-o-globe-europe-africa',
                ])
                ->colors([
                    '0' => 'info',
                    '1' => 'info',
                ])
                ->dehydrated(false)
                ->default(0),
                */

                // magyar címhez ezek kellenek
                Hidden::make('country_code')
                    //->disabled(fn (Get $get): bool => ($get('country_type')!='0'))
                    ->default('HUN'),

                Select::make('settlement')
                    //->hidden(fn (Get $get): bool => ($get('country_type')!='0'))
                    //->required(fn (Get $get): bool => ($get('country_type')!='1'))
                    ->required()
                    ->helperText('Válassza ki vagy keresse ki a kívánt települést.')
                    ->label('Település')
                    ->prefixIcon('tabler-writing-sign')
                    ->preload()
                    ->options(Settlement::selectraw('settlement, concat ( zip_code , " - " , settlement ) as codeWithSettlement')->pluck('codeWithSettlement', 'settlement'))
                    //->options(ZipCode::selectraw('settlement, concat ( "(" , zip_code , ") " , settlement ) as codeWithSettlement')->pluck('codeWithSettlement', 'settlement'))
                    //->options(ZipCode::all()->pluck('settlement', 'settlement'))
                    //->relationship(name: 'zipcode', titleAttribute: 'settlement')
                    ->searchable()
                    ->native(false)
                    ->createOptionForm([
                        TextInput::make('zip_code')->label('Postai irányítószám')->helperText('Adja meg a rögzíteni kívánt új település, postai irányítószámát.')
                            ->required()->unique(),
                        TextInput::make('settlement')->label('Település neve')->helperText('Adja meg a rögzíteni kívánt új település nevét.')
                            ->required()->unique(),
                    ])
                    ->afterStateUpdated(function (Set $set, $state) {
                        $set('zip_code', Settlement::where('settlement', $state)->first()->zip_code);
                    })
                    ->live(),

                Hidden::make('zip_code')
                    //->disabled(fn (Get $get): bool => ($get('country_type')!='0'))
                    ->default(function (Get $get) {
                        return $get('settlement');
                    }),

                /*
                TextInput::make('zip_code')
                ->disabled(fn (Get $get): bool => ($get('country_type')!='1'))
                ->default(function(Get $get){
                    return $get('settlement');
                })
                ->helperText('Település irányítószáma')
                ->label('Irányítószám')
                ->prefixIcon('tabler-writing-sign')
                ->required()
                ->minLength(3)
                ->maxLength(255),
                */

                // külföldi címhez ezek kellenek
                /*
                Select::make('country_code')
                ->hidden(fn (Get $get): bool => ($get('country_type')!='1'))
                ->required(fn (Get $get): bool => ($get('country_type')!='1'))
                ->label('Ország')
                ->prefixIcon('heroicon-o-globe-europe-africa')
                ->helperText('Válassza ki a címhez tartozó országot.')
                ->preload()
                ->options(Country::all()->pluck('name', 'iso_code'))
                ->searchable()
                ->native(false),
                
                TextInput::make('zip_code')
                ->hidden(fn (Get $get): bool => ($get('country_type')!='1'))
                ->required(fn (Get $get): bool => ($get('country_type')!='0'))
                ->helperText('Adja meg az irányítószámot.')
                ->label('Irányítószám')
                ->prefixIcon('tabler-writing-sign')
                ->required()
                ->minLength(3)
                ->maxLength(255)
                ->columns('2'),

                TextInput::make('settlement')
                ->hidden(fn (Get $get): bool => ($get('country_type')!='1'))
                ->required(fn (Get $get): bool => ($get('country_type')!='0'))
                ->helperText('Adja meg a település nevét.')
                ->label('Település')
                ->prefixIcon('tabler-writing-sign')
                ->minLength(3)
                ->maxLength(255),
                */

                // minden címhez ezek kellenek
                Section::make('Cím adatok')
                    ->description('Lehetőségéhez mérten rögzítse a pontos címadatokat vagy válasszon egyéb, a lejjebb megtalálható lehetőségek közül.')
                    ->schema([
                        TextInput::make('address')
                            ->helperText('Adja meg a közterület nevét.')
                            ->label('Közterület neve')
                            ->prefixIcon('tabler-writing-sign')
                            ->minLength(2)
                            ->maxLength(255),

                        Select::make('area_type_id')
                            ->helperText('Válassza ki a közterület jellegét.')
                            ->label('Közterület jellege')
                            ->prefixIcon('tabler-layout-list')
                            ->options(AreaType::all()->pluck('name', 'id'))
                            ->searchable()
                            ->native(false),

                        TextInput::make('address_number')
                            ->helperText('Adja meg az épület számát és az esetleges kiegészító adatokat (pl.: emelet, ajtó, stb...).')
                            ->label('Épület, emelet, ajtó')
                            ->prefixIcon('tabler-writing-sign')
                            ->maxLength(100),

                        TextInput::make('po_box')
                            ->helperText('Amennyiben rendelkezik postafiókkal, annak számát is megadhatja.')
                            ->label('Postafiók')
                            ->prefixIcon('tabler-writing-sign')
                            ->minLength(1)
                            ->maxLength(20),
                    ])->columns(2),

                Section::make('Helyrajzi szám')
                    ->description('Amennyiben csak helyrajzi számmal rendelkezik, akkor azt is megadhatja.')
                    ->schema([
                        ToggleButtons::make('parcel_type')
                            ->helperText('Válassza ki a helyrajzi szám típusát.')
                            ->label('Helyrajzi szám típusa')
                            ->inline()
                            ->options([
                                '1' => 'Belterület',
                                '2' => 'Külterület',
                            ])
                            ->icons([
                                '1' => 'tabler-pin-end',
                                '2' => 'tabler-pin-invoke',
                            ])
                            ->colors([
                                '1' => 'info',
                                '2' => 'info',
                            ])
                            //->dehydrated(false)
                            ->default(1),

                        TextInput::make('parcel_number')
                            ->helperText('Adja meg a pontos helyrajzi számot.')
                            ->label('Helyrajzi szám')
                            ->prefixIcon('tabler-writing-sign')
                            ->minLength(3)
                            ->maxLength(255),
                    ])->columns(2),

                Section::make('Koordináták')
                    ->description('Amennyiben ismeri a cím pontos koordinátáit, lehetősége van azokat is megadni.')
                    ->schema([
                        TextInput::make('latitude')
                            ->helperText('Adja meg a pontos földrajzi szélesség koordinátáit.')
                            ->label('Földrajzi szélesség')
                            ->prefixIcon('tabler-world-latitude')
                            ->minLength(3)
                            ->maxLength(255),

                        TextInput::make('longitude')
                            ->helperText('Adja meg a pontos földrajzi hosszúság koordinátáit.')
                            ->label('Földrajzi hosszúság')
                            ->prefixIcon('tabler-world-longitude')
                            ->minLength(3)
                            ->maxLength(255),

                        Forms\Components\Actions::make([
                            Forms\Components\Actions\Action::make('Megttekintés térképen')
                                ->icon('tabler-arrow-loop-right')
                                ->visible(function ($record) {
                                    if (!empty($record->latitude) && !empty($record->longitude)) {
                                        return true;
                                    }
                                })
                                ->url(function ($record) {
                                    //return 'https://www.google.com/maps/@'.$record->latitude.','.$record->longitude.',19z?entry=ttu';
                                    return 'http://maps.google.com/maps?q=' . $record->latitude . ',' . $record->longitude;
                                })
                                ->openUrlInNewTab()
                                ->hidden(fn(GET $get, $operation): bool => ($operation == 'create'))
                                ->action(function (Forms\Get $get, Forms\Set $set) {
                                    $set('excerpt', str($get('content'))->words(45, end: ''));
                                })
                        ]),

                    ])->columns(2),

                Section::make('Megjegyzés')
                    ->description('Lehetősége van a címhez megjegyzést írni, hogy ezzel is megkönnyitse annak azonosítását.')
                    ->schema([
                        Textarea::make('description')
                            ->hiddenLabel()
                            ->rows(3)
                            ->cols(20)
                            ->autosize(false),
                    ])->columns(1),

                /*
                TextInput::make('settlement')
                ->disabled(fn (Get $get): bool => ($get('country_type')!='1'))
                ->default(function(Get $get){
                    return $get('zip_code');
                })
                ->helperText('Település')
                ->label('Település')
                ->prefixIcon('tabler-writing-sign')
                ->required()
                ->minLength(3)
                ->maxLength(255),

                /*
                // magyar címhez legördülő irányítószám mező
                Select::make('zip_code')
                ->hidden(fn (Get $get): bool => ($get('country_type')!='0'))
                ->required(fn (Get $get): bool => ($get('country_type')!='1'))
                ->helperText('Válassza ki az irányítószámot vagy keresse ki a kívánt települést.')
                ->label('Irányítószám')
                ->prefixIcon('tabler-writing-sign')
                ->preload()
                ->options(ZipCode::selectraw('zip_code, concat (zip_code, " (", settlement ,")" ) as codeWithSettlement')->pluck('codeWithSettlement', 'zip_code'))
                ->searchable()
                ->native(false)
                ->afterStateUpdated(function (Set $set, $state) {
                    $set ('settlement', ZipCode::where('zip_code', $state)->first()->settlement);
                })
                ->live(),

                /*
                //külföldi címhez kitöltős irányítószám mező
                TextInput::make('zip_code')
                ->hidden(fn (Get $get): bool => ($get('country_type')!='1'))
                ->required(fn (Get $get): bool => ($get('country_type')!='0'))
                ->helperText('Adja meg az irányítószámot')
                ->label('Külföldi irányítószám')
                ->prefixIcon('tabler-writing-sign')
                ->required()
                ->minLength(3)
                ->maxLength(255)
                ->columns('2'),
                */

                /*
                TextInput::make('settlement')
                ->disabled(fn (Get $get): bool => ($get('country_type')!='1'))
                ->default(function(Get $get){
                    return $get('zip_code');
                })
                ->helperText('Település')
                ->label('Település')
                ->prefixIcon('tabler-writing-sign')
                ->required()
                ->minLength(3)
                ->maxLength(255),
                */
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('Cím')
            ->heading('Címek, elérhetőségek')
            ->description('Ebben a modulban rögzíthet címeket, elérhetőségeket az adott ügyfélhez.')
            ->emptyStateHeading('Nincs megjeleníthető cím.')
            ->emptyStateDescription('Az "Új cím" gombra kattintva rögzíthet címeket, elérhetőségeket az adott ügyfélhez.')
            ->emptyStateIcon('tabler-database-search')
            ->columns([
                TextColumn::make('addresstype.name')
                    ->label('Típus, jelleg')
                    ->searchable(),
                TextColumn::make('zip_code')
                    ->label('Cím')
                    ->formatStateUsing(function ($record) {
                        $areatype_name = AreaType::find($record->area_type_id);
                        if ($record->address == null) {
                            $address = '';
                            if ($record->parcel_type != null && $record->parcel_number != null) {
                                if ($record->parcel_type == 1) {
                                    $parcel_type = 'Belterület';
                                }
                                if ($record->parcel_type == 2) {
                                    $parcel_type = 'Külterület';
                                }
                                $address = ', ' . $parcel_type . ' ' . $record->parcel_number . '.';
                            }
                        }
                        if ($record->address != null) {
                            $address = ', ' . $record->address . ' ' . $areatype_name->name . ' ' . $record->address_number . '.';
                        }
                        return $record->zip_code . ' ' . $record->settlement . $address;
                    })
                    ->searchable(['zip_code', 'settlement', 'address', 'parcel_number'])
                    ->description(function ($record,) {
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
                            return $text . $wrapText;
                        }
                    }),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make()
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('Új cím')->icon('tabler-circle-plus')->modalHeading('Új cím')->createAnother(false),
            ])
            ->actions([
                Action::make('latitude')
                    ->icon('tabler-arrow-loop-right')
                    ->hiddenLabel()
                    ->tooltip('Ide kattintva megtekintheti egy új ablakban a helyszínt a térképen.')
                    ->url(function ($record) {
                        //return 'https://www.google.com/maps/@'.$record->latitude.','.$record->longitude.',19z?entry=ttu';
                        return 'http://maps.google.com/maps?q=' . $record->latitude . ',' . $record->longitude;
                    })
                    ->openUrlInNewTab()
                    ->visible(function ($record) {
                        if (!empty($record->latitude) && !empty($record->longitude)) {
                            return true;
                        }
                    }),
                Tables\Actions\EditAction::make()->label(false)->icon('tabler-pencil'),
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
}
