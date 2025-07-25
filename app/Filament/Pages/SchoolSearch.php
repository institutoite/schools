<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

use App\Models\School;
use Filament\Forms\Components\TextInput;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;


class SchoolSearch extends Page
{
    use InteractsWithTable;

    // protected static ?string $navigationIcon = 'heroicon-o-search';
    protected static ?string $navigationIcon = 'heroicon-o-magnifying-glass';
    protected static string $view = 'filament.pages.school-search';
    protected static ?string $navigationLabel = 'Buscar Colegios';
    protected static ?string $title = 'Búsqueda de Colegios';

    public function table(Table $table): Table
    {
        return $table
            ->query(School::with(['ubicacion', 'servicios', 'ambientes', 'estadisticas']))
            ->columns([
                Tables\Columns\TextColumn::make('codigo_rue')
                    ->searchable()
                    ->sortable()
                    ->label('Código RUE'),
                    
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('dependencia')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('ubicacion.departamento')
                    ->label('Departamento'),
                    
                Tables\Columns\TextColumn::make('ubicacion.provincia')
                    ->label('Provincia'),
            ])
            ->filters([
                // Puedes agregar filtros adicionales aquí
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->form([
                        // Información básica
                        TextInput::make('codigo_rue')->label('Código RUE'),
                        TextInput::make('nombre')->label('Nombre'),
                        TextInput::make('director')->label('Director'),
                        TextInput::make('direccion')->label('Dirección'),
                        TextInput::make('telefonos')->label('Teléfonos'),
                        TextInput::make('dependencia')->label('Dependencia'),
                        TextInput::make('niveles')->label('Niveles'),
                        TextInput::make('turnos')->label('Turnos'),
                        
                        // Ubicación
                        TextInput::make('ubicacion.departamento')->label('Departamento'),
                        TextInput::make('ubicacion.provincia')->label('Provincia'),
                        TextInput::make('ubicacion.municipio')->label('Municipio'),
                        TextInput::make('ubicacion.distrito')->label('Distrito'),
                        TextInput::make('ubicacion.area')->label('Área'),
                        TextInput::make('ubicacion.latitud')->label('Latitud'),
                        TextInput::make('ubicacion.longitud')->label('Longitud'),
                        
                        // Servicios
                        TextInput::make('servicios.agua')->label('Agua')->formatStateUsing(fn ($state) => $state ? 'Sí' : 'No'),
                        TextInput::make('servicios.electricidad')->label('Electricidad')->formatStateUsing(fn ($state) => $state ? 'Sí' : 'No'),
                        TextInput::make('servicios.banos')->label('Baños')->formatStateUsing(fn ($state) => $state ? 'Sí' : 'No'),
                        TextInput::make('servicios.internet')->label('Internet')->formatStateUsing(fn ($state) => $state ? 'Sí' : 'No'),
                        
                        // Ambientes
                        TextInput::make('ambientes.aulas')->label('Aulas'),
                        TextInput::make('ambientes.laboratorios')->label('Laboratorios'),
                        TextInput::make('ambientes.bibliotecas')->label('Bibliotecas'),
                        TextInput::make('ambientes.computacion')->label('Sala de Computación'),
                        TextInput::make('ambientes.canchas')->label('Canchas'),
                        TextInput::make('ambientes.gimnasios')->label('Gimnasios'),
                        TextInput::make('ambientes.coliseos')->label('Coliseos'),
                        TextInput::make('ambientes.piscinas')->label('Piscinas'),
                        TextInput::make('ambientes.secretaria')->label('Secretaría'),
                        TextInput::make('ambientes.reuniones')->label('Salas de Reunión'),
                        TextInput::make('ambientes.talleres')->label('Talleres'),
                    ]),
            ])
            ->bulkActions([
                // ...
            ]);
    }

    protected function getTableQuery(): Builder
    {
        return School::query()
            ->when(
                $this->getTableSearch(),
                fn (Builder $query, $search) => $query->where('nombre', 'like', "%{$search}%")
                    ->orWhere('codigo_rue', 'like', "%{$search}%")
            );
    }
}
