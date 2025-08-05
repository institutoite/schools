<?php

namespace App\Exports;

use App\Models\School;
use Maatwebsite\Excel\Concerns\FromCollection;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SchoolsByDepartmentExport implements FromQuery, WithHeadings, WithMapping, WithStyles
{
    protected $departamento;

    public function __construct(string $departamento)
    {
        $this->departamento = $departamento;
    }

    public function query()
    {
        return School::query()
            ->whereHas('ubicacion', function($query) {
                $query->where('departamento', 'LIKE', "%{$this->departamento}%");
            })
            ->with('ubicacion')
            ->orderBy('nombre');
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
            'Departamento',
            'Provincia',
            'Municipio',
            'Área',
            'Coordenadas'
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
            $school->ubicacion->departamento ?? 'N/D',
            $school->ubicacion->provincia ?? 'N/D',
            $school->ubicacion->municipio ?? 'N/D',
            $school->ubicacion->area ?? 'N/D',
            $school->ubicacion->coordenadas_texto ?? 'N/D'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'color' => ['rgb' => 'D9E1F2']
                ]
            ],
            'A:N' => ['autoSize' => true],
        ];
    }

}
