<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Filament\Clusters\Settings;
use App\Models\Productmaincategory;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SubNavigationPosition;
use Filament\Tables\Actions\CreateAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ProductmaincategoryResource\Pages;
use App\Filament\Resources\ProductmaincategoryResource\RelationManagers;

class ProductmaincategoryResource extends Resource
{
    protected static ?string $model = Productmaincategory::class;

    protected static ?string $cluster = Settings::class;
    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?string $navigationIcon = 'tabler-category';
    protected static ?string $modelLabel = 'termék főkategória';
    protected static ?string $pluralModelLabel = 'termék főkategóriák';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Főkategória neve')
                    ->helperText('Módosítsa a termék főkategória elnevezését.')
                    ->required()
                    ->prefixIcon('tabler-writing')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('Termék főkategóriák')
            ->heading('Termék főkategóriák')
            ->description('Ebben a modulban rögzítheti azokat a főkatgóriákat, amelyekbe később rendezhetőek lesznek a termékek.')
            ->emptyStateHeading('Nincs megjeleníthető termék főkategória.')
            ->emptyStateDescription('Az "Új termék főkategória" gombra kattintva rögzíthet új főkatógiát.')
            ->emptyStateIcon('tabler-database-search')
            ->headerActions([
                CreateAction::make()
                    ->createAnother(false)
                    ->modalWidth('md')
                    ->icon('tabler-circle-plus')
                    ->slideOver()
                    ->form([
                        TextInput::make('name')
                            ->label('Főkategória neve')
                            ->required()
                            ->helperText('Adja mega az új termék főkategória elnevezését.')
                            ->maxLength(255),

                    ]),
            ])
            ->columns([
                TextColumn::make('name')
                    ->label('Főkategória neve')
                    ->searchable()
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make()
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label(false)->icon('tabler-pencil')->modalHeading('Főkategória szerkesztése')->modalWidth('md'),
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
            'index' => Pages\ListProductmaincategories::route('/'),
            // 'create' => Pages\CreateProductmaincategory::route('/create'),
            // 'edit' => Pages\EditProductmaincategory::route('/{record}/edit'),
        ];
    }
}
