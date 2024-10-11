<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use App\Models\Customer;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Resources\Pages\Page;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms\Components\RichEditor;
use Filament\Pages\SubNavigationPosition;
use Filament\Tables\Actions\DeleteAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\ToggleButtons;
use App\Filament\Resources\CustomerResource\Pages;
use AnourValar\EloquentSerialize\Tests\Models\Post;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CustomerResource\RelationManagers;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationGroup = 'Értékesítés';
    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?string $modelLabel = 'ügyfél';
    protected static ?string $pluralModelLabel = 'ügyfelek';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(4)
                ->schema([
                    Section::make() 
                    ->schema([
                        Section::make()
                        ->schema([
                            TextInput::make('name')
                            ->label('Ügyfél neve')
                            ->helperText('Adja meg az ügyfél nevét.')
                            ->prefixIcon('tabler-writing-sign')
                            ->required()
                            ->minLength(3)
                            ->maxLength(255)
                            ->columnSpanFull(),

                            TextInput::make('registration_number')
                            ->helperText('Adja meg az ügyfél nyilvántartási (cégjegyzék) számát.')
                            ->label('Nyilvántartási szám')
                            ->prefixIcon('tabler-writing-sign')
                            ->mask('99 99 99999999')
                            ->placeholder('__ __ ________')
                            ->required()
                            ->minLength(3)
                            ->maxLength(255),

                            TextInput::make('tax_number')
                            ->helperText('Adja meg az ügyfél adószámát.')
                            ->label('Adó szám')
                            ->prefixIcon('tabler-writing-sign')
                            ->mask('99999999-9-99')
                            ->placeholder('________-_-__')
                            ->required()
                            ->minLength(3)
                            ->maxLength(255),
                        ])->columns(2),
                        
                        Fieldset::make('Ügyfél információk')
                            ->schema([
                                Select::make('industrytypes')
                                //->hidden((!auth()->user()->hasRole(['super_admin'])))
                                ->label('Iparágak')
                                ->helperText('Válaszd ki, hogy az adott ügyfél melyik szektorhoz tartozik...több is kiválasztható.')
                                ->multiple()
                                ->relationship(titleAttribute: 'name')
                                ->createOptionForm([
                                    Forms\Components\TextInput::make('name')
                                        ->required()->unique(),])
                                ->preload(),

                                Textarea::make('description')
                                ->label('Rövid ismertető, jegyzet az ügyfélről.')
                                ->rows(10)
                                ->cols(20)
                                
                                ])->columns([
                                    'sm' => 1,
                                    'md' => 1,
                                    'lg' => 1,
                                    'xl' => 1,
                                    '2xl' => 1,
                                ]),
                                
                    ])->columnSpan([
                        'sm' => 4,
                        'md' => 4,
                        'lg' => 4,
                        'xl' => 2,
                        '2xl' => 2,
                    ]),
                    
                    Section::make()
                    ->schema([
                        Fieldset::make('Számlázási beállítások')
                        ->schema([
                            ToggleButtons::make('payment_deadline')
                                ->label('Fizetési határidő')
                                ->helperText('Válassza ki az ügyfélhez tartózó fizetési határidőt.')
                                ->inline()
                                //->grouped()
                                ->options([
                                    '0' => 'készpénz',
                                    '8' => '8 munkanap',
                                    '14' => '14 munkanap',
                                    '30' => '1 hónap',
                                    '90' => '3 hónap',
                                    '1' => 'egyéb',
                                ])
                                ->colors([
                                    '0' => 'success',
                                    '8' => 'info',
                                    '14' => 'info',
                                    '30' => 'info',
                                    '90' => 'info',
                                    '1' => 'warning',
                                ])
                                //->disabled(!auth()->user()->hasRole(['super_admin']))
                                ->default(8)
                                ->live()
                                ->required(),

                                TextInput::make('unique_payment_deadline')
                                ->hidden(fn (Get $get): bool => ($get('payment_deadline')!='1'))
                                ->label('Egyéb fizetési határidő')
                                ->helperText('Adja meg az ügyfélhez tartózó fizetési határidőt.')
                                ->prefixIcon('tabler-calendar-question')
                                ->numeric()
                                ->default(0)
                                ->minLength(1)
                                ->maxLength(10)
                                //->disabled(!auth()->user()->hasRole(['super_admin']))
                                ->suffix('munkanap')
                        ])->columns([
                            'sm' => 1,
                            'md' => 2,
                            'lg' => 2,
                            'xl' => 2,
                            '2xl' => 2,
                        ]),
                        
                            

                            Fieldset::make('Pénzügyi kockázat')
                            ->schema([
                                Placeholder::make('documentation')
                                ->label('Pénzügyi kockázati szint')
                                ->content(function($get) :HtmlString{
                                    $riskLevel = $get('financial_risk_rate');
                                    if ($riskLevel <= 2)
                                    {
                                        $riskLevelText ='
                                            <span><div style="display: inline-block; margin-right:8px; margin-bottom:-10px; margin-top:10px; color:rgb(38,186,75); float:left; position:relative;">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M17 3.34a10 10 0 1 1 -14.995 8.984l-.005 -.324l.005 -.324a10 10 0 0 1 14.995 -8.336zm-1.293 5.953a1 1 0 0 0 -1.32 -.083l-.094 .083l-3.293 3.292l-1.293 -1.292l-.094 -.083a1 1 0 0 0 -1.403 1.403l.083 .094l2 2l.094 .083a1 1 0 0 0 1.226 0l.094 -.083l4 -4l.083 -.094a1 1 0 0 0 -.083 -1.32z" stroke-width="0" fill="currentColor" />
                                                </svg>
                                            </div><div style="float:left; position:relative; padding-top:24px; font-size:14pt;">Alacsony kockázati szint.</div></span>';
                                    }
                                    if ($riskLevel >= 3 && $riskLevel <= 5)
                                    {
                                        $riskLevelText = '
                                            <span><div style="display: inline-block; margin-right:8px; margin-bottom:-10px; margin-top:10px; color:rgb(240,141,14); float:left; position:relative;">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M12 2c5.523 0 10 4.477 10 10a10 10 0 0 1 -19.995 .324l-.005 -.324l.004 -.28c.148 -5.393 4.566 -9.72 9.996 -9.72zm0 13a1 1 0 0 0 -.993 .883l-.007 .117l.007 .127a1 1 0 0 0 1.986 0l.007 -.117l-.007 -.127a1 1 0 0 0 -.993 -.883zm1.368 -6.673a2.98 2.98 0 0 0 -3.631 .728a1 1 0 0 0 1.44 1.383l.171 -.18a.98 .98 0 0 1 1.11 -.15a1 1 0 0 1 -.34 1.886l-.232 .012a1 1 0 0 0 .111 1.994a3 3 0 0 0 1.371 -5.673z" stroke-width="0" fill="currentColor" />
                                                </svg>
                                            </div><div style="float:left; position:relative; padding-top:24px; font-size:14pt;">Közepes kockázati szint!</div></span>';
                                    }
                                    if ($riskLevel >= 6)
                                    {
                                        $riskLevelText = '
                                            <span><div style="display: inline-block; margin-right:8px; margin-bottom:-10px; margin-top:10px; color:rgb(231,43,53); float:left; position:relative;">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M12 2c5.523 0 10 4.477 10 10a10 10 0 0 1 -19.995 .324l-.005 -.324l.004 -.28c.148 -5.393 4.566 -9.72 9.996 -9.72zm.01 13l-.127 .007a1 1 0 0 0 0 1.986l.117 .007l.127 -.007a1 1 0 0 0 0 -1.986l-.117 -.007zm-.01 -8a1 1 0 0 0 -.993 .883l-.007 .117v4l.007 .117a1 1 0 0 0 1.986 0l.007 -.117v-4l-.007 -.117a1 1 0 0 0 -.993 -.883z" stroke-width="0" fill="currentColor" />
                                                </svg>
                                            </div><div style="float:left; position:relative; padding-top:24px; font-size:14pt;">Közepes kockázati szint!</div></span>';
                                    }
                                    return new HtmlString($riskLevelText);
                                }),

                                ToggleButtons::make('financial_risk_rate')
                                ->label(false)
                                ->helperText('Válassza ki az ügyfél pénzügyi kockázati szintjét.')
                                ->inline()
                                ->grouped()
                                ->options([
                                    '0' => '0',
                                    '1' => '1',
                                    '2' => '2',
                                    '3' => '3',
                                    '4' => '4',
                                    '5' => '5',
                                    '6' => '6',
                                    '7' => '7',
                                    '8' => '8',
                                    '9' => '9'
                                ])
                                ->colors([
                                    '0' => 'success',
                                    '1' => 'success',
                                    '2' => 'success',
                                    '3' => 'warning',
                                    '4' => 'warning',
                                    '5' => 'warning',
                                    '6' => 'danger',
                                    '7' => 'danger',
                                    '8' => 'danger',
                                    '9' => 'danger'
                                ])
                                //->disabled(!auth()->user()->hasRole(['super_admin']))
                                ->default(0)
                                ->live(),
                                
                                
                                
                                RichEditor::make('justification_of_risk')
                                ->label('Kockázat indoklása')
                                ->helperText('Itt röviden megindokolhatja, hogy az adott ügyfelet miért a kiválasztott kockázati szinten tartózkodik.')
                                ->toolbarButtons([
                                    //'attachFiles',
                                    'blockquote',
                                    'bold',
                                    'bulletList',
                                    //'codeBlock',
                                    'h2',
                                    'h3',
                                    'italic',
                                    'link',
                                    'orderedList',
                                    'redo',
                                    'strike',
                                    'underline',
                                    'undo',
                                ])
                                ->columnSpanFull(),

                                ])->columns([
                                    'sm' => 1,
                                    'md' => 1,
                                    'lg' => 1,
                                    'xl' => 1,
                                    '2xl' => 1,
                                ]),

                    ])->columnSpan([
                        'sm' => 4,
                        'md' => 4,
                        'lg' => 4,
                        'xl' => 2,
                        '2xl' => 2,
                    ]),
                ]), 
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->heading('Ügyfelek, társaságok, vállalkozások.')
            ->description('Ebben a modulban rögzítheti és kezelheti azokat a társaságokat, vállalkozásokat akiket megkeresett ajánlatával.')
            ->emptyStateHeading('Nincs megjeleníthető ügyfél, társaság, vállalkozás.')
            ->emptyStateDescription('Az "Új ügyfél" gombra kattintva rögzíthet új ügyfelet, társaságot, vállalkozást a rendszerhez.')
            ->emptyStateIcon('tabler-database-x')
            ->columns([
                TextColumn::make('name')
                ->label('Név/Cégnév')
                ->description(function($record): HtmlString{
                    if ($record->description != null)
                    {
                        $text = $record->description;
                        $wrapText='...';
                        $count = 40;
                        if(strlen($record->description)>$count){
                            preg_match('/^.{0,' . $count . '}(?:.*?)\b/siu', $record->description, $matches);
                            $text = $matches[0];
                        }else{
                            $wrapText = '';
                        }
                        return new HtmlString('<span class="text-gray-500 dark:text-gray-400" style="font-size:9pt;">'.$text.$wrapText.'</span>');
                    }
                })
                ->searchable(['name','description']),
                TextColumn::make('industrytypes.name')
                ->label('Iparág')
                ->badge()
                ->separator(',')
                ->listWithLineBreaks(),
                TextColumn::make('tax_number')
                ->label('Azonosítók')
                ->formatStateUsing(function ($record) {
                    return'<p><span class="text-gray-500 dark:text-gray-400" style="font-size:9pt;">Adószám: </span><span class="text-custom-600 dark:text-custom-400" style="font-size:11pt;">'.$record->tax_number.'</span></p>
                    <p><span class="text-gray-500 dark:text-gray-400" style="font-size:9pt;">Cégj.szám: </span><span class="text-custom-600 dark:text-custom-400" style="font-size:11pt;">'.$record->registration_number.'</span></p>';
                })->html()
                ->searchable(['tax_number', 'registration_number']),
                TextColumn::make('financial_risk_rate')
                ->label('Pénzügyi kockázat')
                ->formatStateUsing(function($state): HtmlString{
                    $rate0Active = '
                        <div style="display: inline-block; margin-right:-4px; margin-bottom:-4px; color:rgb(38,186,75);">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 3.34a10 10 0 1 1 -14.995 8.984l-.005 -.324l.005 -.324a10 10 0 0 1 14.995 -8.336zm-1.293 5.953a1 1 0 0 0 -1.32 -.083l-.094 .083l-3.293 3.292l-1.293 -1.292l-.094 -.083a1 1 0 0 0 -1.403 1.403l.083 .094l2 2l.094 .083a1 1 0 0 0 1.226 0l.094 -.083l4 -4l.083 -.094a1 1 0 0 0 -.083 -1.32z" stroke-width="0" fill="currentColor" />
                            </svg>
                        </div>';
                    $rate0Full = '
                        <div style="display: inline-block; margin-right:-4px; margin-bottom:-4px; color:rgb(38,186,75);">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 3.34a10 10 0 1 1 -14.995 8.984l-.005 -.324l.005 -.324a10 10 0 0 1 14.995 -8.336zm-5 6.66a2 2 0 0 0 -1.977 1.697l-.018 .154l-.005 .149l.005 .15a2 2 0 1 0 1.995 -2.15z" stroke-width="0" fill="currentColor" />
                            </svg>
                        </div>
                    ';
                    $rate1Active = '
                        <div style="display: inline-block; margin-right:-4px; margin-bottom:-4px; color:rgb(38,186,75);">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 3.34a10 10 0 1 1 -14.995 8.984l-.005 -.324l.005 -.324a10 10 0 0 1 14.995 -8.336zm-1.293 5.953a1 1 0 0 0 -1.32 -.083l-.094 .083l-3.293 3.292l-1.293 -1.292l-.094 -.083a1 1 0 0 0 -1.403 1.403l.083 .094l2 2l.094 .083a1 1 0 0 0 1.226 0l.094 -.083l4 -4l.083 -.094a1 1 0 0 0 -.083 -1.32z" stroke-width="0" fill="currentColor" />
                            </svg>
                        </div>';
                    $rate1Full = '
                        <div style="display: inline-block; margin-right:-4px; margin-bottom:-4px; color:rgb(38,186,75);">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 3.34a10 10 0 1 1 -14.995 8.984l-.005 -.324l.005 -.324a10 10 0 0 1 14.995 -8.336zm-5 6.66a2 2 0 0 0 -1.977 1.697l-.018 .154l-.005 .149l.005 .15a2 2 0 1 0 1.995 -2.15z" stroke-width="0" fill="currentColor" />
                            </svg>
                        </div>
                    ';
                    $rate1Empty = '
                        <div style="display: inline-block; margin-right:-4px; margin-bottom:-4px; color:gray;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M8.56 3.69a9 9 0 0 0 -2.92 1.95" />
                                <path d="M3.69 8.56a9 9 0 0 0 -.69 3.44" />
                                <path d="M3.69 15.44a9 9 0 0 0 1.95 2.92" />
                                <path d="M8.56 20.31a9 9 0 0 0 3.44 .69" />
                                <path d="M15.44 20.31a9 9 0 0 0 2.92 -1.95" />
                                <path d="M20.31 15.44a9 9 0 0 0 .69 -3.44" />
                                <path d="M20.31 8.56a9 9 0 0 0 -1.95 -2.92" />
                                <path d="M15.44 3.69a9 9 0 0 0 -3.44 -.69" />
                                <path d="M10 10l2 -2v8" />
                            </svg>
                        </div>
                    ';
                    $rate2Active = '
                        <div style="display: inline-block; margin-right:-4px; margin-bottom:-4px; color:rgb(38,186,75);">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 3.34a10 10 0 1 1 -14.995 8.984l-.005 -.324l.005 -.324a10 10 0 0 1 14.995 -8.336zm-1.293 5.953a1 1 0 0 0 -1.32 -.083l-.094 .083l-3.293 3.292l-1.293 -1.292l-.094 -.083a1 1 0 0 0 -1.403 1.403l.083 .094l2 2l.094 .083a1 1 0 0 0 1.226 0l.094 -.083l4 -4l.083 -.094a1 1 0 0 0 -.083 -1.32z" stroke-width="0" fill="currentColor" />
                            </svg>
                        </div>
                    ';
                    $rate2Full = '
                        <div style="display: inline-block; margin-right:-4px; margin-bottom:-4px; color:rgb(38,186,75);">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 3.34a10 10 0 1 1 -14.995 8.984l-.005 -.324l.005 -.324a10 10 0 0 1 14.995 -8.336zm-5 6.66a2 2 0 0 0 -1.977 1.697l-.018 .154l-.005 .149l.005 .15a2 2 0 1 0 1.995 -2.15z" stroke-width="0" fill="currentColor" />
                            </svg>
                        </div>
                    ';
                    $rate2Empty = '
                        <div style="display: inline-block; margin-right:-4px; margin-bottom:-4px; color:gray;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M8.56 3.69a9 9 0 0 0 -2.92 1.95" />
                                <path d="M3.69 8.56a9 9 0 0 0 -.69 3.44" />
                                <path d="M3.69 15.44a9 9 0 0 0 1.95 2.92" />
                                <path d="M8.56 20.31a9 9 0 0 0 3.44 .69" />
                                <path d="M15.44 20.31a9 9 0 0 0 2.92 -1.95" />
                                <path d="M20.31 15.44a9 9 0 0 0 .69 -3.44" />
                                <path d="M20.31 8.56a9 9 0 0 0 -1.95 -2.92" />
                                <path d="M15.44 3.69a9 9 0 0 0 -3.44 -.69" />
                                <path d="M10 8h3a1 1 0 0 1 1 1v2a1 1 0 0 1 -1 1h-2a1 1 0 0 0 -1 1v2a1 1 0 0 0 1 1h3" />
                            </svg>
                        </div>
                    ';
                    $rate3Active = '
                        <div style="display: inline-block; margin-right:-4px; margin-bottom:-4px; color:rgb(240,141,14);">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 2c5.523 0 10 4.477 10 10a10 10 0 0 1 -19.995 .324l-.005 -.324l.004 -.28c.148 -5.393 4.566 -9.72 9.996 -9.72zm0 13a1 1 0 0 0 -.993 .883l-.007 .117l.007 .127a1 1 0 0 0 1.986 0l.007 -.117l-.007 -.127a1 1 0 0 0 -.993 -.883zm1.368 -6.673a2.98 2.98 0 0 0 -3.631 .728a1 1 0 0 0 1.44 1.383l.171 -.18a.98 .98 0 0 1 1.11 -.15a1 1 0 0 1 -.34 1.886l-.232 .012a1 1 0 0 0 .111 1.994a3 3 0 0 0 1.371 -5.673z" stroke-width="0" fill="currentColor" />
                            </svg>
                        </div>
                    ';
                    $rate3Full = '
                        <div style="display: inline-block; margin-right:-4px; margin-bottom:-4px; color:rgb(240,141,14);">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 3.34a10 10 0 1 1 -14.995 8.984l-.005 -.324l.005 -.324a10 10 0 0 1 14.995 -8.336zm-5 6.66a2 2 0 0 0 -1.977 1.697l-.018 .154l-.005 .149l.005 .15a2 2 0 1 0 1.995 -2.15z" stroke-width="0" fill="currentColor" />
                            </svg>
                        </div>
                    ';
                    $rate3Empty = '
                        <div style="display: inline-block; margin-right:-4px; margin-bottom:-4px; color:gray;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M8.56 3.69a9 9 0 0 0 -2.92 1.95" />
                                <path d="M3.69 8.56a9 9 0 0 0 -.69 3.44" />
                                <path d="M3.69 15.44a9 9 0 0 0 1.95 2.92" />
                                <path d="M8.56 20.31a9 9 0 0 0 3.44 .69" />
                                <path d="M15.44 20.31a9 9 0 0 0 2.92 -1.95" />
                                <path d="M20.31 15.44a9 9 0 0 0 .69 -3.44" />
                                <path d="M20.31 8.56a9 9 0 0 0 -1.95 -2.92" />
                                <path d="M15.44 3.69a9 9 0 0 0 -3.44 -.69" />
                                <path d="M10 8h2.5a1.5 1.5 0 0 1 1.5 1.5v1a1.5 1.5 0 0 1 -1.5 1.5h-1.5h1.5a1.5 1.5 0 0 1 1.5 1.5v1a1.5 1.5 0 0 1 -1.5 1.5h-2.5" />
                            </svg>
                        </div>
                    ';
                    $rate4Active = '
                        <div style="display: inline-block; margin-right:-4px; margin-bottom:-4px; color:rgb(240,141,14);">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 2c5.523 0 10 4.477 10 10a10 10 0 0 1 -19.995 .324l-.005 -.324l.004 -.28c.148 -5.393 4.566 -9.72 9.996 -9.72zm0 13a1 1 0 0 0 -.993 .883l-.007 .117l.007 .127a1 1 0 0 0 1.986 0l.007 -.117l-.007 -.127a1 1 0 0 0 -.993 -.883zm1.368 -6.673a2.98 2.98 0 0 0 -3.631 .728a1 1 0 0 0 1.44 1.383l.171 -.18a.98 .98 0 0 1 1.11 -.15a1 1 0 0 1 -.34 1.886l-.232 .012a1 1 0 0 0 .111 1.994a3 3 0 0 0 1.371 -5.673z" stroke-width="0" fill="currentColor" />
                            </svg>
                        </div>
                    ';
                    $rate4Full = '
                        <div style="display: inline-block; margin-right:-4px; margin-bottom:-4px; color:rgb(240,141,14);">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 3.34a10 10 0 1 1 -14.995 8.984l-.005 -.324l.005 -.324a10 10 0 0 1 14.995 -8.336zm-5 6.66a2 2 0 0 0 -1.977 1.697l-.018 .154l-.005 .149l.005 .15a2 2 0 1 0 1.995 -2.15z" stroke-width="0" fill="currentColor" />
                            </svg>
                        </div>
                    ';
                    $rate4Empty = '
                        <div style="display: inline-block; margin-right:-4px; margin-bottom:-4px; color:gray;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M8.56 3.69a9 9 0 0 0 -2.92 1.95" />
                                <path d="M3.69 8.56a9 9 0 0 0 -.69 3.44" />
                                <path d="M3.69 15.44a9 9 0 0 0 1.95 2.92" />
                                <path d="M8.56 20.31a9 9 0 0 0 3.44 .69" />
                                <path d="M15.44 20.31a9 9 0 0 0 2.92 -1.95" />
                                <path d="M20.31 15.44a9 9 0 0 0 .69 -3.44" />
                                <path d="M20.31 8.56a9 9 0 0 0 -1.95 -2.92" />
                                <path d="M15.44 3.69a9 9 0 0 0 -3.44 -.69" />
                                <path d="M10 8v3a1 1 0 0 0 1 1h3" />
                                <path d="M14 8v8" />
                            </svg>
                        </div>
                    ';
                    $rate5Active = '
                        <div style="display: inline-block; margin-right:-4px; margin-bottom:-4px; color:rgb(240,141,14);">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 2c5.523 0 10 4.477 10 10a10 10 0 0 1 -19.995 .324l-.005 -.324l.004 -.28c.148 -5.393 4.566 -9.72 9.996 -9.72zm0 13a1 1 0 0 0 -.993 .883l-.007 .117l.007 .127a1 1 0 0 0 1.986 0l.007 -.117l-.007 -.127a1 1 0 0 0 -.993 -.883zm1.368 -6.673a2.98 2.98 0 0 0 -3.631 .728a1 1 0 0 0 1.44 1.383l.171 -.18a.98 .98 0 0 1 1.11 -.15a1 1 0 0 1 -.34 1.886l-.232 .012a1 1 0 0 0 .111 1.994a3 3 0 0 0 1.371 -5.673z" stroke-width="0" fill="currentColor" />
                            </svg>
                        </div>
                    ';
                    $rate5Full = '
                        <div style="display: inline-block; margin-right:-4px; margin-bottom:-4px; color:rgb(240,141,14);">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 3.34a10 10 0 1 1 -14.995 8.984l-.005 -.324l.005 -.324a10 10 0 0 1 14.995 -8.336zm-5 6.66a2 2 0 0 0 -1.977 1.697l-.018 .154l-.005 .149l.005 .15a2 2 0 1 0 1.995 -2.15z" stroke-width="0" fill="currentColor" />
                            </svg>
                        </div>
                    ';
                    $rate5Empty = '
                        <div style="display: inline-block; margin-right:-4px; margin-bottom:-4px; color:gray;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M8.56 3.69a9 9 0 0 0 -2.92 1.95" />
                                <path d="M3.69 8.56a9 9 0 0 0 -.69 3.44" />
                                <path d="M3.69 15.44a9 9 0 0 0 1.95 2.92" />
                                <path d="M8.56 20.31a9 9 0 0 0 3.44 .69" />
                                <path d="M15.44 20.31a9 9 0 0 0 2.92 -1.95" />
                                <path d="M20.31 15.44a9 9 0 0 0 .69 -3.44" />
                                <path d="M20.31 8.56a9 9 0 0 0 -1.95 -2.92" />
                                <path d="M15.44 3.69a9 9 0 0 0 -3.44 -.69" />
                                <path d="M10 15a1 1 0 0 0 1 1h2a1 1 0 0 0 1 -1v-2a1 1 0 0 0 -1 -1h-3v-4h4" />
                            </svg>
                        </div>
                    ';
                    $rate6Active = '
                        <div style="display: inline-block; margin-right:-4px; margin-bottom:-4px; color:rgb(231,43,53);">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 2c5.523 0 10 4.477 10 10a10 10 0 0 1 -19.995 .324l-.005 -.324l.004 -.28c.148 -5.393 4.566 -9.72 9.996 -9.72zm.01 13l-.127 .007a1 1 0 0 0 0 1.986l.117 .007l.127 -.007a1 1 0 0 0 0 -1.986l-.117 -.007zm-.01 -8a1 1 0 0 0 -.993 .883l-.007 .117v4l.007 .117a1 1 0 0 0 1.986 0l.007 -.117v-4l-.007 -.117a1 1 0 0 0 -.993 -.883z" stroke-width="0" fill="currentColor" />
                            </svg>
                        </div>
                    ';
                    $rate6Full = '
                        <div style="display: inline-block; margin-right:-4px; margin-bottom:-4px; color:rgb(231,43,53);">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 3.34a10 10 0 1 1 -14.995 8.984l-.005 -.324l.005 -.324a10 10 0 0 1 14.995 -8.336zm-5 6.66a2 2 0 0 0 -1.977 1.697l-.018 .154l-.005 .149l.005 .15a2 2 0 1 0 1.995 -2.15z" stroke-width="0" fill="currentColor" />
                            </svg>
                        </div>
                    ';
                    $rate6Empty = '
                        <div style="display: inline-block; margin-right:-4px; margin-bottom:-4px; color:gray;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M8.56 3.69a9 9 0 0 0 -2.92 1.95" />
                                <path d="M3.69 8.56a9 9 0 0 0 -.69 3.44" />
                                <path d="M3.69 15.44a9 9 0 0 0 1.95 2.92" />
                                <path d="M8.56 20.31a9 9 0 0 0 3.44 .69" />
                                <path d="M15.44 20.31a9 9 0 0 0 2.92 -1.95" />
                                <path d="M20.31 15.44a9 9 0 0 0 .69 -3.44" />
                                <path d="M20.31 8.56a9 9 0 0 0 -1.95 -2.92" />
                                <path d="M15.44 3.69a9 9 0 0 0 -3.44 -.69" />
                                <path d="M14 9a1 1 0 0 0 -1 -1h-2a1 1 0 0 0 -1 1v6a1 1 0 0 0 1 1h2a1 1 0 0 0 1 -1v-2a1 1 0 0 0 -1 -1h-3" />
                            </svg>
                        </div>
                    ';
                    $rate7Active = '
                        <div style="display: inline-block; margin-right:-4px; margin-bottom:-4px; color:rgb(231,43,53);">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 2c5.523 0 10 4.477 10 10a10 10 0 0 1 -19.995 .324l-.005 -.324l.004 -.28c.148 -5.393 4.566 -9.72 9.996 -9.72zm.01 13l-.127 .007a1 1 0 0 0 0 1.986l.117 .007l.127 -.007a1 1 0 0 0 0 -1.986l-.117 -.007zm-.01 -8a1 1 0 0 0 -.993 .883l-.007 .117v4l.007 .117a1 1 0 0 0 1.986 0l.007 -.117v-4l-.007 -.117a1 1 0 0 0 -.993 -.883z" stroke-width="0" fill="currentColor" />
                            </svg>
                        </div>
                    ';
                    $rate7Full = '
                        <div style="display: inline-block; margin-right:-4px; margin-bottom:-4px; color:rgb(231,43,53);">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 3.34a10 10 0 1 1 -14.995 8.984l-.005 -.324l.005 -.324a10 10 0 0 1 14.995 -8.336zm-5 6.66a2 2 0 0 0 -1.977 1.697l-.018 .154l-.005 .149l.005 .15a2 2 0 1 0 1.995 -2.15z" stroke-width="0" fill="currentColor" />
                            </svg>
                        </div>
                    ';
                    $rate7Empty = '
                        <div style="display: inline-block; margin-right:-4px; margin-bottom:-4px; color:gray;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M8.56 3.69a9 9 0 0 0 -2.92 1.95" />
                                <path d="M3.69 8.56a9 9 0 0 0 -.69 3.44" />
                                <path d="M3.69 15.44a9 9 0 0 0 1.95 2.92" />
                                <path d="M8.56 20.31a9 9 0 0 0 3.44 .69" />
                                <path d="M15.44 20.31a9 9 0 0 0 2.92 -1.95" />
                                <path d="M20.31 15.44a9 9 0 0 0 .69 -3.44" />
                                <path d="M20.31 8.56a9 9 0 0 0 -1.95 -2.92" />
                                <path d="M15.44 3.69a9 9 0 0 0 -3.44 -.69" />
                                <path d="M10 8h4l-2 8" />
                            </svg>
                        </div>
                    ';
                    $rate8Active = '
                        <div style="display: inline-block; margin-right:-4px; margin-bottom:-4px; color:rgb(231,43,53);">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 2c5.523 0 10 4.477 10 10a10 10 0 0 1 -19.995 .324l-.005 -.324l.004 -.28c.148 -5.393 4.566 -9.72 9.996 -9.72zm.01 13l-.127 .007a1 1 0 0 0 0 1.986l.117 .007l.127 -.007a1 1 0 0 0 0 -1.986l-.117 -.007zm-.01 -8a1 1 0 0 0 -.993 .883l-.007 .117v4l.007 .117a1 1 0 0 0 1.986 0l.007 -.117v-4l-.007 -.117a1 1 0 0 0 -.993 -.883z" stroke-width="0" fill="currentColor" />
                            </svg>
                        </div>
                    ';
                    $rate8Full = '
                        <div style="display: inline-block; margin-right:-4px; margin-bottom:-4px; color:rgb(231,43,53);">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 3.34a10 10 0 1 1 -14.995 8.984l-.005 -.324l.005 -.324a10 10 0 0 1 14.995 -8.336zm-5 6.66a2 2 0 0 0 -1.977 1.697l-.018 .154l-.005 .149l.005 .15a2 2 0 1 0 1.995 -2.15z" stroke-width="0" fill="currentColor" />
                            </svg>
                        </div>
                    ';
                    $rate8Empty = '
                        <div style="display: inline-block; margin-right:-4px; margin-bottom:-4px; color:gray;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M8.56 3.69a9 9 0 0 0 -2.92 1.95" />
                                <path d="M3.69 8.56a9 9 0 0 0 -.69 3.44" />
                                <path d="M3.69 15.44a9 9 0 0 0 1.95 2.92" />
                                <path d="M8.56 20.31a9 9 0 0 0 3.44 .69" />
                                <path d="M15.44 20.31a9 9 0 0 0 2.92 -1.95" />
                                <path d="M20.31 15.44a9 9 0 0 0 .69 -3.44" />
                                <path d="M20.31 8.56a9 9 0 0 0 -1.95 -2.92" />
                                <path d="M15.44 3.69a9 9 0 0 0 -3.44 -.69" />
                                <path d="M12 12h-1a1 1 0 0 1 -1 -1v-2a1 1 0 0 1 1 -1h2a1 1 0 0 1 1 1v2a1 1 0 0 1 -1 1h-2a1 1 0 0 0 -1 1v2a1 1 0 0 0 1 1h2a1 1 0 0 0 1 -1v-2a1 1 0 0 0 -1 -1" />
                            </svg>
                        </div>
                    ';

                    $rate9Active = '
                        <div style="display: inline-block; margin-right:-4px; margin-bottom:-4px; color:rgb(231,43,53);">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 2c5.523 0 10 4.477 10 10a10 10 0 0 1 -19.995 .324l-.005 -.324l.004 -.28c.148 -5.393 4.566 -9.72 9.996 -9.72zm.01 13l-.127 .007a1 1 0 0 0 0 1.986l.117 .007l.127 -.007a1 1 0 0 0 0 -1.986l-.117 -.007zm-.01 -8a1 1 0 0 0 -.993 .883l-.007 .117v4l.007 .117a1 1 0 0 0 1.986 0l.007 -.117v-4l-.007 -.117a1 1 0 0 0 -.993 -.883z" stroke-width="0" fill="currentColor" />
                            </svg>
                        </div>
                    ';
                    $rate9Empty = '
                        <div style="display: inline-block; margin-right:-4px; margin-bottom:-4px; color:gray;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M8.56 3.69a9 9 0 0 0 -2.92 1.95" />
                                <path d="M3.69 8.56a9 9 0 0 0 -.69 3.44" />
                                <path d="M3.69 15.44a9 9 0 0 0 1.95 2.92" />
                                <path d="M8.56 20.31a9 9 0 0 0 3.44 .69" />
                                <path d="M15.44 20.31a9 9 0 0 0 2.92 -1.95" />
                                <path d="M20.31 15.44a9 9 0 0 0 .69 -3.44" />
                                <path d="M20.31 8.56a9 9 0 0 0 -1.95 -2.92" />
                                <path d="M15.44 3.69a9 9 0 0 0 -3.44 -.69" />
                                <path d="M10 15a1 1 0 0 0 1 1h2a1 1 0 0 0 1 -1v-6a1 1 0 0 0 -1 -1h-2a1 1 0 0 0 -1 1v2a1 1 0 0 0 1 1h3" />
                            </svg>
                        </div>
                    ';

                    $lowRiskLevel = '<span class="text-gray-500 dark:text-gray-400" style="font-size:9pt;">Alacsony kockázati szint.</span>';
                    $midRiskLevel = '<span class="text-gray-500 dark:text-gray-400" style="font-size:9pt;">Közepes kockázati szint!</span>';
                    $highRiskLevel = '<span class="text-gray-500 dark:text-gray-400" style="font-size:9pt;">Magas kockázati szint!</span>';

                    if ($state == '0')
                    {
                        return new HtmlString('
                        <p>'.$rate0Active.$rate1Empty.$rate2Empty.$rate3Empty.$rate4Empty.$rate5Empty.$rate6Empty.$rate7Empty.$rate8Empty.$rate9Empty.'</p>'.$lowRiskLevel);
                    }
                    if ($state == '1')
                    {
                        return new HtmlString('
                        <p>'.$rate0Full.$rate1Active.$rate2Empty.$rate3Empty.$rate4Empty.$rate5Empty.$rate6Empty.$rate7Empty.$rate8Empty.$rate9Empty.'</p>'.$lowRiskLevel);
                    }
                    if ($state == '2')
                    {
                        return new HtmlString('
                        <p>'.$rate0Full.$rate1Full.$rate2Active.$rate3Empty.$rate4Empty.$rate5Empty.$rate6Empty.$rate7Empty.$rate8Empty.$rate9Empty.'</p>'.$lowRiskLevel);
                    }
                    if ($state == '3')
                    {
                        return new HtmlString('
                        <p>'.$rate0Full.$rate1Full.$rate2Full.$rate3Active.$rate4Empty.$rate5Empty.$rate6Empty.$rate7Empty.$rate8Empty.$rate9Empty.'</p>'.$midRiskLevel);
                    }
                    if ($state == '4')
                    {
                        return new HtmlString('
                        <p>'.$rate0Full.$rate1Full.$rate2Full.$rate3Full.$rate4Active.$rate5Empty.$rate6Empty.$rate7Empty.$rate8Empty.$rate9Empty.'</p>'.$midRiskLevel);
                    }
                    if ($state == '5')
                    {
                        return new HtmlString('
                        <p>'.$rate0Full.$rate1Full.$rate2Full.$rate3Full.$rate4Full.$rate5Active.$rate6Empty.$rate7Empty.$rate8Empty.$rate9Empty.'</p>'.$midRiskLevel);
                    }
                    if ($state == '6')
                    {
                        return new HtmlString('
                        <p>'.$rate0Full.$rate1Full.$rate2Full.$rate3Full.$rate4Full.$rate5Full.$rate6Active.$rate7Empty.$rate8Empty.$rate9Empty.'</p>'.$highRiskLevel);
                    }
                    if ($state == '7')
                    {
                        return new HtmlString('
                        <p>'.$rate0Full.$rate1Full.$rate2Full.$rate3Full.$rate4Full.$rate5Full.$rate6Full.$rate7Active.$rate8Empty.$rate9Empty.'</p>'.$highRiskLevel);
                    }
                    if ($state == '8')
                    {
                        return new HtmlString('
                        <p>'.$rate0Full.$rate1Full.$rate2Full.$rate3Full.$rate4Full.$rate5Full.$rate6Full.$rate7Full.$rate8Active.$rate9Empty.'</p>'.$highRiskLevel);
                    }
                    if ($state == '9')
                    {
                        return new HtmlString('
                        <p>'.$rate0Full.$rate1Full.$rate2Full.$rate3Full.$rate4Full.$rate5Full.$rate6Full.$rate7Full.$rate8Full.$rate9Active.'</p>'.$highRiskLevel);
                    }
                })
                
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make()->icon('tabler-pencil'),
                    DeleteAction::make()->icon('tabler-trash'),
                ])

                
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
            Pages\EditCustomer::class,
            Pages\ManageCustomerContacts::class,
            Pages\ManageCustomerAddresses::class,
            Pages\ManageCustomerFinancialrisks::class,
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
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
            'contacts' => Pages\ManageCustomerContacts::route('/{record}/contacts'),
            'addresses' => Pages\ManageCustomerAddresses::route('/{record}/addresses'),
            'financialrisks' => Pages\ManageCustomerFinancialrisks::route('/{record}/financialrisks'),
        ];
    }

    public static function getNavigationBadge(): ?string //ez kiírja a menü mellé, hogy mennyi ügyfél van már rögzítve
    {
        /** @var class-string<Model> $modelClass */
        $modelClass = static::$model;

        return (string) $modelClass::all()->count();
    }
}
