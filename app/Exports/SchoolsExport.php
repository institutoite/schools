<?php

namespace App\Exports;

use App\Models\School;
use Maatwebsite\Excel\Concerns\FromCollection;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SchoolsExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return School::all();
    }


    public function headings(): array
    {
        return [
            'ID',
            'Código RUE',
            'Nombre',
            'Director',
            'Dirección',
            'Teléfonos',
            'Dependencia',
            'Niveles',
            'Turnos',
            'URL Ficha',
            'Humanístico',
            'Creado',
            'Actualizado'
        ];
    }

    public function map($school): array
    {
        return [
            $school->id,
            $school->codigo_rue,
            $school->nombre,
            $school->director,
            $school->direccion,
            $school->telefonos,
            $school->dependencia,
            $school->niveles,
            $school->turnos,
            $school->url_ficha,
            $school->humanistico,
            $school->created_at->format('d/m/Y H:i'),
            $school->updated_at->format('d/m/Y H:i')
        ];
    }
}
