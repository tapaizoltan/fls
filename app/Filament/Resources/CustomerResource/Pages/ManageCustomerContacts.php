<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use Filament\Forms;
use Filament\Tables;
use Filament\Actions;
use App\Models\Contact;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Support\Htmlable;
use App\Filament\Resources\CustomerResource;
use Filament\Forms\Components\ToggleButtons;
use Filament\Resources\Pages\ManageRelatedRecords;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ManageCustomerContacts extends ManageRelatedRecords
{
    protected static string $resource = CustomerResource::class;

    protected static string $relationship = 'contacts';

    protected static ?string $navigationIcon = 'tabler-address-book';

    public static function getNavigationLabel(): string
    {
        return 'Kapcsolat(ok)';
    }

    public function getTitle(): string | Htmlable
    {
        return 'Kapcsolat(ok) szerkesztése';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                ToggleButtons::make('type')
                    ->helperText('Válassza ki a rögzíteni kívánt kapcsolat típusát.')
                    ->label('Kapcsolat típusa')
                    ->inline()
                    /*->grouped()*/
                    ->live()
                    ->required()
                    ->options([
                        '0' => 'Személyes',
                        '1' => 'Részleg, osztály, stb...',
                    ])
                    ->icons([
                        '0' => 'tabler-user-plus',
                        '1' => 'tabler-directions',
                    ])
                    ->colors([
                        '0' => 'info',
                        '1' => 'info',
                    ])
                    ->default(0)
                    ->columnSpanFull(),
                Toggle::make('financial_relationship')
                    ->inline(false)
                    ->label('Pénzügyi kapcsolattartó')
                    ->helperText('Ezt bekapcsolja akkor az adott kapcsolat hivatott financiális kérdésekkel kapcsolatban.')
                    ->onIcon('tabler-check')
                    ->offIcon('tabler-x')
                    ->default(0),
                Toggle::make('get_offer')
                    ->inline(false)
                    ->label('Kaphat ajánlatot')
                    ->helperText('Ezt bekapcsolja akkor az adott kapcsolat kaphat ajánlatokat a rendszertől.')
                    ->onIcon('tabler-check')
                    ->offIcon('tabler-x')
                    ->default(0),
                TextInput::make('department_name')
                    ->hidden(fn(Get $get): bool => ($get('type') != '1'))
                    ->helperText('Adjon egy nevet a kapcsolatnak. (pl.: irodai vagy pusztaszentborzasztói porta, stb.)')
                    ->label('Neve')
                    ->prefixIcon('tabler-writing-sign')
                    ->required()
                    ->minLength(3)
                    ->maxLength(255),
                Select::make('title')
                    ->hidden(fn(Get $get): bool => ($get('type') != '0'))
                    ->label('Előtag')
                    ->helperText('Válassza ki a kapcsolattartó titulusát amennyiben van neki.')
                    ->options([
                        'id.' => 'id.',
                        'ifj.' => 'ifj.',
                        'özv.' => 'özv.',
                        'dr.' => 'dr.',
                        'Dr.' => 'Dr.',
                        'PhD.' => 'PhD.',
                        'Prof.' => 'Prof.',
                    ])
                    ->searchable()
                    ->native(false),
                TextInput::make('lastname')
                    ->hidden(fn(Get $get): bool => ($get('type') != '0'))
                    ->helperText('Adja meg a kapcsolattartó vezetéknevét.')
                    ->label('Vezetéknév')
                    ->prefixIcon('tabler-writing-sign')
                    ->required()
                    ->minLength(3)
                    ->maxLength(255),
                TextInput::make('firstname')
                    ->hidden(fn(Get $get): bool => ($get('type') != '0'))
                    ->helperText('Adja meg a kapcsolattartó keresztnevét.')
                    ->label('Keresztnév')
                    ->prefixIcon('tabler-writing-sign')
                    ->required()
                    ->minLength(3)
                    ->maxLength(255),
                TextInput::make('second_firstname')
                    ->hidden(fn(Get $get): bool => ($get('type') != '0'))
                    ->helperText('Adja meg a kapcsolattartó második keresztnevét, amennyiben van neki.')
                    ->label('Második keresztnév')
                    ->prefixIcon('tabler-writing-sign')
                    ->minLength(3)
                    ->maxLength(255),
                TextInput::make('email')
                    ->helperText('Adja meg a kapcsolathoz tartozó elektronikus levelezési címet.')
                    ->label('E-mail cím')
                    ->prefixIcon('tabler-writing-sign')
                    ->minLength(3)
                    ->maxLength(255),
                TextInput::make('phone')
                    ->helperText('Adja meg a kapcsolathoz tartozó telefonszámot.')
                    ->label('Telefonszám')
                    ->prefixIcon('tabler-writing-sign')
                    ->minLength(3)
                    ->maxLength(255),
            ])
            ->columns([
                'sm' => 1,
                'md' => 2,
                'lg' => 2,
                'xl' => 2,
                '2xl' => 2,
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('Kapcsolat(ok)')
            ->heading('Kapcsolatok, kapcsolattartók')
            ->description('Ebben a modulban rögzíthet kapcsolatokat az adott ügyfélhez.')
            ->emptyStateHeading('Nincs megjeleníthető kapcsolat.')
            ->emptyStateDescription('Az "Új kapcsolat" gombra kattintva rögzíthet kapcsolatokat az adott ügyfélhez.')
            ->emptyStateIcon('tabler-database-search')
            ->columns([
                TextColumn::make('type')
                    ->label('Név')
                    ->searchable(['title', 'lastname', 'firstname', 'second_firstname', 'department_name'])
                    ->formatStateUsing(function (Contact $contact, $state) {
                        if ($state == 0) {
                            if ($contact->title == null) {
                                $contact_title = '';
                            }
                            if ($contact->title != null) {
                                $contact_title = $contact->title . ' ';
                            }
                            if ($contact->second_firstname == null) {
                                $contact_second_firstname = '';
                            }
                            if ($contact->second_firstname != null) {
                                $contact_second_firstname = ' ' . $contact->second_firstname;
                            }
                            return $contact_title . $contact->lastname . ' ' . $contact->firstname . $contact_second_firstname;
                        }
                        if ($state == 1) {
                            return $contact->department_name;
                        }
                    }),
                TextColumn::make('phone')
                    ->label('Telefonszám')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('E-mail cím')
                    ->searchable(),
                IconColumn::make('financial_relationship')
                    ->label('Pénzügyi kapcsolattartó')
                    ->icon(fn(string $state): string => match ($state) {
                        '0' => '',
                        '1' => 'tabler-circle-check',
                    })
                    ->color(fn(string $state): string => match ($state) {
                        '0' => '',
                        '1' => 'success',
                    })
                    ->size(IconColumn\IconColumnSize::Medium),
                IconColumn::make('get_offer')
                    ->label('Kaphat ajánlatot')
                    ->icon(fn(string $state): string => match ($state) {
                        '0' => '',
                        '1' => 'tabler-circle-check',
                    })
                    ->color(fn(string $state): string => match ($state) {
                        '0' => '',
                        '1' => 'success',
                    })
                    ->size(IconColumn\IconColumnSize::Medium),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make()
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('Új kapcsolat')->icon('tabler-circle-plus')->modalHeading('Új kapcsolat')->createAnother(false),
            ])
            ->actions([
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
