<?php

namespace App\Filament\Resources;

use Filament\Forms\Form;
use Filament\Tables;
use App\Models\Supplier;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\Split;
use Filament\Pages\SubNavigationPosition;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\TextEntry;
use App\Filament\Resources\SupplierResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists\Components\Grid as InfolistsGrid;
use App\Filament\Resources\SupplierResource\RelationManagers;
use Filament\Infolists\Components\Section as InfolistsSection;

class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;

    protected static ?string $navigationGroup = 'Beszállítás';
    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?string $modelLabel = 'beszállító';
    protected static ?string $pluralModelLabel = 'beszállítók';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(12)
                    ->schema([
                        Section::make()
                            ->schema([
                                TextInput::make('name')
                                    ->label('Beszállító neve')
                                    ->helperText('Adja meg a beszállító nevét.')
                                    ->prefixIcon('tabler-writing-sign')
                                    ->required()
                                    ->minLength(3)
                                    ->maxLength(255)
                                    ->columns(3),
                            ])->columnSpan([
                                'sm' => 6,
                                'md' => 6,
                                'lg' => 6,
                                'xl' => 6,
                                '2xl' => 4,
                            ]),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->heading('Beszállítók.')
            ->description('Ebben a modulban rögzítheti és kezelheti a beszállítókat.')
            ->emptyStateHeading('Nincs megjeleníthető beszállító.')
            ->emptyStateDescription('Az "Új beszállító" gombra kattintva rögzíthet új beszállítót a rendszerhez.')
            ->emptyStateIcon('tabler-database-search')
            ->columns([
                TextColumn::make('name')
                    ->label('Név/Cégnév')
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
                    })
                    ->searchable(['name', 'description']),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make()
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label(false)->icon('tabler-pencil')->slideOver()->modalWidth('md'),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSuppliers::route('/'),
            // 'create' => Pages\CreateSupplier::route('/create'),
            // 'edit' => Pages\EditSupplier::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string //ez kiírja a menü mellé, hogy mennyi ügyfél van már rögzítve
    {
        /** @var class-string<Model> $modelClass */
        $modelClass = static::$model;

        return (string) $modelClass::all()->count();
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                InfolistsSection::make()
                    ->schema([
                        InfolistsGrid::make(2)
                            ->schema([
                                TextEntry::make('name')
                                    ->label(false),
                            ]),
                    ]),

                Split::make([
                    InfolistsSection::make('Kapcsolatok')
                        ->description('Az ügyfélhez rögzített kapcsolat(ok) listája.')
                        ->icon('tabler-address-book')
                        ->schema([
                            //..
                        ]),
                    InfolistsSection::make('Címek')
                        ->description('Az ügyfélhez rögzített címe(ek) listája.')
                        ->icon('tabler-map-pin-2')
                        ->schema([
                            //..
                        ]),
                ])->from('xl')->columnSpanFull(),

                InfolistsSection::make('Előzmények')
                    ->description('Nyomonkövethető az ügyfélhez köthető összes esemény.')
                    ->icon('tabler-history')
                    ->schema([
                        //..
                    ]),
            ]);
    }
}
