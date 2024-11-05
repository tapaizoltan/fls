<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use STS\FilamentImpersonate\Tables\Actions\Impersonate;
use App\Filament\Resources\UserResource\RelationManagers;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $modelLabel = 'felhasználó';
    protected static ?string $pluralModelLabel = 'felhasználók';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->label('Név')
                    ->required()
                    ->maxLength(255)
                    ->autofocus(),

                TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),

                TextInput::make('phone')->label('Telefonszám')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                ->label('Név')
                ->searchable(),
                TextColumn::make('email')
                ->label('E-mail cím'),
                TextColumn::make('company.name')
                ->label('Társaság'),
                TextColumn::make('last_login_at')
                ->label('Utoljára itt')
                ->formatStateUsing(function($state)
                {
                    $diff_day_nums = date_diff(date_create($state), date_create('now'))->format('%a');
                    if ($diff_day_nums == 0){$last_login = 'mai napon';}
                    if ($diff_day_nums != 0)
                    {
                        $last_login = 'utoljára '.$diff_day_nums.' napja';
                    }
                    return '<p>'.Carbon::parse($state)->translatedFormat('Y F d').'</p>
                    <p class="text-xs text-gray-500 dark: text-xs text-gray-400">'.$last_login .'</p>';
                })->html()
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->hiddenLabel()->icon('tabler-pencil')->tooltip('Szerkesztés')->link(),
                Tables\Actions\DeleteAction::make()->hiddenLabel()->tooltip('Törlés')->icon('tabler-trash'),
                Tables\Actions\ForceDeleteAction::make()->label(false)->tooltip('Végleges törlés'),
                Tables\Actions\RestoreAction::make()->label(false)->tooltip('Helyteállítás'),
                /*
                Impersonate::make()
                ->guard('web')
                ->redirectTo(route('filament.admin.pages.dashboard'))
                ->icon('tabler-ghost-3'),*/
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
