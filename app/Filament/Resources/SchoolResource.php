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

use App\Exports\SchoolsByDepartmentExport;
use Filament\Forms\Components\Select;

use App\Exports\SchoolsExport;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Tables\Actions\Action;

use Illuminate\Support\Str;

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
        $departamentos = \App\Models\Ubicacion::select('departamento')
            ->distinct()
            ->orderBy('departamento')
            ->pluck('departamento', 'departamento');

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
                ])
            ])
            ->headerActions([
                Action::make('export')
                    ->label('Exportar a Excel')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(function () {
                        return Excel::download(new SchoolsExport, 'colegios.xlsx');
                    }),
                Action::make('exportByDepartment')
                ->label('Exportar por Departamento')
                ->icon('heroicon-o-document-arrow-down')
                ->form([
                    Select::make('departamento')
                        ->label('Departamento')
                        ->options($departamentos)
                        ->required()
                        ->searchable()
                        ->placeholder('Seleccione un departamento'),
                    
                    Select::make('formato')
                        ->label('Formato de exportación')
                        ->options([
                            'xlsx' => 'Excel (.xlsx)',
                            'csv' => 'CSV (.csv)',
                            'pdf' => 'PDF (.pdf)'
                        ])
                        ->default('xlsx')
                ])
                ->action(function (array $data) {
                    $export = new SchoolsByDepartmentExport($data['departamento']);
                    $filename = 'colegios_' . Str::slug($data['departamento']) . '_' . now()->format('Y-m-d');
                    
                    return match($data['formato']) {
                        'csv' => Excel::download($export, "$filename.csv", \Maatwebsite\Excel\Excel::CSV),
                        'pdf' => Excel::download($export, "$filename.pdf", \Maatwebsite\Excel\Excel::DOMPDF),
                        default => Excel::download($export, "$filename.xlsx")
                    };
                })
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
