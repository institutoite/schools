<?php

namespace App\Console\Commands;

use App\Models\Ubicacion;
use App\Services\DistritoMunicipalGeoService;
use Illuminate\Console\Command;

class AsignarDistritosMunicipales extends Command
{
    protected $signature = 'schools:asignar-distritos-municipales {--limpiar : Limpia los distritos municipales antes de asignar}';
    protected $description = 'Asigna el distrito municipal (GeoJSON Santa Cruz) a colegios con coordenadas';

    public function handle(DistritoMunicipalGeoService $geoService): int
    {
        $this->warn('Comando deshabilitado: pruebas solo con consultas, sin almacenar en BD.');
        return self::SUCCESS;
    }
}
