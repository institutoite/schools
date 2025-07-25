<?php

namespace App\Filament\Resources\JsonResource\Pages;

use App\Filament\Resources\JsonResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\School;
use App\Models\Ubicacion;
use App\Models\Estadistica;
use App\Models\Servicio;
use App\Models\Ambiente;

class CreateJson extends CreateRecord
{
    protected static string $resource = JsonResource::class;
    public function mutateFormDataBeforeCreate(array $data): array
    {
        $jsonEscuelas = $data['json']; // Aquí llega el JSON desde el formulario
        dd($jsonEscuelas); // Para depurar y ver el contenido del JSON
    $escuelas = json_decode($jsonEscuelas, true); // lo conviertes a array asociativo

    foreach ($escuelas as $escuela) {
        // 1. Crear la escuela
        $school = School::create([
            'codigo_rue' => $escuela['general']['codigo_rue'],
            'nombre' => $escuela['general']['nombre'],
            'director' => $escuela['general']['director'] ?? null,
            'direccion' => $escuela['general']['direccion'] ?? null,
            'telefonos' => $escuela['general']['telefonos'] ?? null,
            'dependencia' => $escuela['general']['dependencia'] ?? 'FISCAL',
            'niveles' => $escuela['general']['niveles'] ?? null,
            'turnos' => $escuela['general']['turnos'] ?? null,
            'url_ficha' => $escuela['url'] ?? null,
        ]);

        // 2. Ubicación
        $coordenadas = $escuela['ubicacion']['coordenadas'] ?? [];
        Ubicacion::create([
            'school_id' => $school->id,
            'departamento' => $escuela['ubicacion']['departamento'],
            'provincia' => $escuela['ubicacion']['provincia'],
            'municipio' => $escuela['ubicacion']['municipio'],
            'distrito' => $escuela['ubicacion']['distrito'],
            'area' => $escuela['ubicacion']['area'],
            'latitud' => $coordenadas['latitud'] ?? null,
            'longitud' => $coordenadas['longitud'] ?? null,
            'coordenadas_texto' => $coordenadas['texto'] ?? null,
        ]);

        // 3. Estadísticas
        foreach (['matricula', 'promovidos', 'reprobados', 'abandono'] as $tipo) {
            foreach ($escuela['estadisticas'][$tipo] as $categoria => $anios) {
                foreach ($anios as $anio => $valor) {
                    Estadistica::create([
                        'school_id' => $school->id,
                        'tipo' => $tipo,
                        'categoria' => $categoria,
                        'anio' => (int) $anio,
                        'valor' => (int) $valor,
                    ]);
                }
            }
        }

        // 4. Servicios
        $servicios = $escuela['infraestructura']['servicios'] ?? [];
        Servicio::create([
            'school_id' => $school->id,
            'agua' => $servicios['agua'] ?? null,
            'electricidad' => $servicios['electricidad'] ?? null,
            'banos' => $servicios['banos'] ?? null,
            'internet' => $servicios['internet'] ?? null,
        ]);

        // 5. Ambientes
        $ambientes = $escuela['infraestructura']['ambientes'] ?? [];
        Ambiente::create([
            'school_id' => $school->id,
            'aulas' => $ambientes['aulas'] ?? null,
            'laboratorios' => $ambientes['laboratorios'] ?? null,
            'bibliotecas' => $ambientes['bibliotecas'] ?? null,
            'computacion' => $ambientes['computacion'] ?? null,
            'canchas' => $ambientes['canchas'] ?? null,
            'gimnasios' => $ambientes['gimnasios'] ?? null,
        ]);
    }

    // No necesitas retornar nada para la creación porque ya lo gestionaste manualmente.
    return [];
    }
}
