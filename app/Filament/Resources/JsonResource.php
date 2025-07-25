<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JsonResource\Pages;
use App\Filament\Resources\JsonResource\RelationManagers;
use App\Models\Json;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class JsonResource extends Resource
{
    protected static ?string $model = Json::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('json') // El nombre del campo que se usará para el archivo
                ->label('Archivo JSON') // Etiqueta visible en el formulario
                ->acceptedFileTypes(['application/json']) // Valida que solo se acepten archivos JSON
                ->columnSpanFull() // Hace que ocupe todo el ancho disponible en el layout
                ->required() // Hace que la subida del archivo sea obligatoria
                ->directory('json-uploads') // Directorio dentro de storage/app/public/ donde se guardará el archivo
                ->visibility('private'), // Opcional: El archivo no será accesible directamente vía URL pública
            // Otros campos de tu formulario si los tienes
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('json')
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
            'index' => Pages\ListJsons::route('/'),
            'create' => Pages\CreateJson::route('/create'),
            'edit' => Pages\EditJson::route('/{record}/edit'),
        ];
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        dd($data);
        // 1. Verificar si se subió un archivo JSON a través de nuestro campo temporal
        if (isset($data['json_file_upload'])) {
            try {
                // Obtener la ruta temporal del archivo subido por Filament
                $filePath = $data['json_file_upload'];

                // 2. Leer el contenido del archivo JSON desde el disco de almacenamiento
                $jsonContent = Storage::disk('local')->get($filePath);

                // 3. Decodificar el contenido JSON
                $processedData = json_decode($jsonContent, true);

                if (json_last_error() === JSON_ERROR_NONE) {
                    // El JSON se leyó correctamente. ¡Ahora mostramos el mensaje!
                    Notification::make()
                        ->title('JSON interceptado y procesado!')
                        ->body('Contenido del JSON (solo para depuración): ' . substr($jsonContent, 0, 100) . '...') // Muestra un extracto
                        ->success()
                        ->send();

                    Log::info('JSON interceptado desde Filament:', $processedData); // Para logs del servidor

                } else {
                    // Si el JSON está mal formado
                    throw new \Exception('El archivo JSON subido está mal formado: ' . json_last_error_msg());
                }

            } catch (\Exception $e) {
                // Captura cualquier error durante la lectura o decodificación
                Log::error("Error al interceptar archivo JSON en Filament: " . $e->getMessage());

                Notification::make()
                    ->title('Error al interceptar JSON')
                    ->body($e->getMessage())
                    ->danger()
                    ->persistent()
                    ->send();

                // Si hay un error, puedes decidir si quieres que el registro se cree o no.
                // Si quieres detener la creación, puedes lanzar la excepción de nuevo:
                // throw new \RuntimeException("Fallo en la operación debido a un JSON inválido.");
            } finally {
                // 4. **MUY IMPORTANTE**: Eliminar el campo 'json_file_upload' de los datos
                // Esto asegura que Filament NO intente guardar la ruta del archivo JSON
                // en ninguna columna de tu tabla de la base de datos.
                unset($data['json_file_upload']);

                // 5. Opcional: Eliminar el archivo físico temporal del disco después de procesarlo
                // Si no necesitas mantenerlo guardado en el storage después de interceptarlo.
                if (isset($filePath) && Storage::disk('local')->exists($filePath)) {
                    Storage::disk('local')->delete($filePath);
                }
            }
        }

        // 6. Devolver los datos modificados.
        // Estos son los datos que Filament usará para crear el registro en la base de datos.
        // Ahora no contendrán la ruta del archivo JSON.
        return $data;
    }
}
