<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EstadisticaResource\Pages;
use App\Filament\Resources\EstadisticaResource\RelationManagers;
use App\Models\Estadistica;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EstadisticaResource extends Resource
{
    protected static ?string $model = Estadistica::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('school_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('categoria')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('total')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('anio')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('mujer')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('hombre')
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
                Tables\Columns\TextColumn::make('categoria')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total')
                    ->searchable(),
                Tables\Columns\TextColumn::make('anio')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('mujer')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('hombre')
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
            'index' => Pages\ListEstadisticas::route('/'),
            'create' => Pages\CreateEstadistica::route('/create'),
            'edit' => Pages\EditEstadistica::route('/{record}/edit'),
        ];
    }
}
