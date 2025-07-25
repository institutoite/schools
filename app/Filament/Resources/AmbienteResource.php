<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AmbienteResource\Pages;
use App\Filament\Resources\AmbienteResource\RelationManagers;
use App\Models\Ambiente;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AmbienteResource extends Resource
{
    protected static ?string $model = Ambiente::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('school_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('aulas')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('laboratorios')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('bibliotecas')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('computacion')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('canchas')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('gimnasios')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('coliseos')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('piscinas')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('secretaria')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('reuniones')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('talleres')
                    ->numeric()
                    ->default(null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('school_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('aulas')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('laboratorios')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('bibliotecas')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('computacion')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('canchas')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('gimnasios')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('coliseos')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('piscinas')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('secretaria')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('reuniones')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('talleres')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListAmbientes::route('/'),
            'create' => Pages\CreateAmbiente::route('/create'),
            'edit' => Pages\EditAmbiente::route('/{record}/edit'),
        ];
    }
}
