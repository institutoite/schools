<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SchoolResource\Pages;
use App\Filament\Resources\SchoolResource\RelationManagers;
use App\Models\School;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SchoolResource extends Resource
{
    protected static ?string $model = School::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('codigo_rue')
                    ->required()
                    ->visible(auth()->user()->can('subir_fotos'))
                    ->maxLength(20),
                Forms\Components\TextInput::make('nombre')
                    ->required()
                    ->maxLength(100),
                Forms\Components\TextInput::make('director')
                    ->maxLength(100)
                    ->default(null),
                Forms\Components\TextInput::make('direccion')
                    ->maxLength(150)
                    ->default(null),
                Forms\Components\TextInput::make('telefonos')
                    ->tel()
                    ->maxLength(35)
                    ->default(null),
                Forms\Components\TextInput::make('dependencia')
                    ->required(),
                Forms\Components\TextInput::make('niveles')
                    ->maxLength(70)
                    ->default(null),
                Forms\Components\TextInput::make('turnos')
                    ->maxLength(50)
                    ->default(null),
                Forms\Components\TextInput::make('url_ficha')
                    ->maxLength(100)
                    ->default(null),
                Forms\Components\TextInput::make('humanistico')
                    ->maxLength(10)
                    ->default(null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('codigo_rue')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('director')
                    ->searchable(),
                Tables\Columns\TextColumn::make('direccion')
                    ->searchable(),
                Tables\Columns\TextColumn::make('telefonos')
                    ->searchable(),
                Tables\Columns\TextColumn::make('dependencia'),
                Tables\Columns\TextColumn::make('niveles')
                    ->searchable(),
                Tables\Columns\TextColumn::make('turnos')
                    ->searchable(),
                Tables\Columns\TextColumn::make('url_ficha')
                    ->searchable(),
                Tables\Columns\TextColumn::make('humanistico')
                    ->searchable(),
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
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListSchools::route('/'),
            'create' => Pages\CreateSchool::route('/create'),
            'view' => Pages\ViewSchool::route('/{record}'),
            'edit' => Pages\EditSchool::route('/{record}/edit'),
        ];
    }
}
