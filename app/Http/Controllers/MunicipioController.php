<?php

namespace App\Http\Controllers;

use App\Models\Estadistica;
use App\Models\School;
use App\Models\Ubicacion;
use Illuminate\Http\Request;

class MunicipioController extends Controller
{
    public function comparar(Request $request)
    {
        // Solo obtener la lista de municipios para el formulario de selección
        $municipios = Ubicacion::select('municipio')->distinct()->orderBy('municipio')->pluck('municipio');

        // No procesar datos si no se han seleccionado ambos municipios
        $municipio1 = $request->get('municipio1');
        $municipio2 = $request->get('municipio2');
        $datos = [];

        if ($municipio1 && $municipio2) {
            foreach ([$municipio1, $municipio2] as $municipio) {
                // Solo cuenta, no traigas todos los modelos
                $cantidadColegios = School::whereHas('ubicacion', function($q) use ($municipio) {
                    $q->where('municipio', $municipio);
                })->count();

                $cantidadPrivados = School::whereHas('ubicacion', function($q) use ($municipio) {
                    $q->where('municipio', $municipio);
                })->where('dependencia', 'privado')->count();

                $cantidadFiscales = School::whereHas('ubicacion', function($q) use ($municipio) {
                    $q->where('municipio', $municipio);
                })->where('dependencia', 'fiscal')->count();

                $totalEstudiantes = Estadistica::whereHas('school.ubicacion', function($q) use ($municipio) {
                    $q->where('municipio', $municipio);
                })->where('categoria', 'matricula')->sum('total');

                $densidad = $cantidadColegios > 0 ? round($totalEstudiantes / $cantidadColegios, 2) : 0;

                $datos[$municipio] = [
                    'cantidadColegios' => $cantidadColegios,
                    'cantidadPrivados' => $cantidadPrivados,
                    'cantidadFiscales' => $cantidadFiscales,
                    'totalEstudiantes' => $totalEstudiantes,
                    'densidad' => $densidad,
                ];
            }
        }

        return view('schools.comparar_municipios', compact('municipios', 'municipio1', 'municipio2', 'datos'));
    }
    public function listarColegios(Request $request)
    {
        $municipio = $request->get('municipio');
        $orderBy = $request->get('orderBy', 'matriculados');
        $orderDir = $request->get('orderDir', 'desc');
        $municipios = \App\Models\Ubicacion::select('municipio')->distinct()->orderBy('municipio')->pluck('municipio');

        $colegios = collect();
        $totales = [
            'matriculados' => 0,
            'abandono' => 0,
            'reprobados' => 0,
            'aprobados' => 0,
        ];

        if ($municipio) {
            $colegios = \App\Models\School::whereHas('ubicacion', function($q) use ($municipio) {
                $q->where('municipio', $municipio);
            })
            ->with(['ubicacion', 'estadisticas' => function($q) {
                $q->whereIn('categoria', ['matricula', 'abandono', 'reprobados']);
            }])
            ->get()
            ->map(function($school) use (&$totales) {
                $ultimoAnio = $school->estadisticas->max('anio');
                $matricula = $school->estadisticas->where('categoria', 'matricula')->where('anio', $ultimoAnio)->first();
                $abandono = $school->estadisticas->where('categoria', 'abandono')->where('anio', $ultimoAnio)->first();
                $reprobados = $school->estadisticas->where('categoria', 'reprobados')->where('anio', $ultimoAnio)->first();

                $matriculados = $matricula ? (int)$matricula->total : 0;
                $abandonoTotal = $abandono ? (int)$abandono->total : 0;
                $reprobadosTotal = $reprobados ? (int)$reprobados->total : 0;
                $aprobadosTotal = $matriculados - $abandonoTotal - $reprobadosTotal;

                $totales['matriculados'] += $matriculados;
                $totales['abandono'] += $abandonoTotal;
                $totales['reprobados'] += $reprobadosTotal;
                $totales['aprobados'] += $aprobadosTotal;

                return [
                    'nombre' => $school->nombre,
                    'dependencia' => $school->dependencia,
                    'lat' => $school->ubicacion->latitud ?? null,   // <-- usa 'latitud'
                    'lng' => $school->ubicacion->longitud ?? null,  // <-- usa 'longitud'
                    'matriculados' => $matriculados,
                    'abandono' => $abandonoTotal,
                    'reprobados' => $reprobadosTotal,
                    'aprobados' => $aprobadosTotal,
                ];
            });

            // Ordenar por columna seleccionada
            $colegios = $orderDir === 'asc'
                ? $colegios->sortBy($orderBy)->values()
                : $colegios->sortByDesc($orderBy)->values();
        }

        return view('schools.listar_colegios_municipio', compact('municipios', 'municipio', 'colegios', 'totales', 'orderBy', 'orderDir'));
    }
    public function getColegiosPorMunicipio(Request $request)
    {
        $municipio = $request->get('municipio');

        if (!$municipio) {
            return response()->json([]);
        }

        $colegios = School::whereHas('ubicacion', function ($q) use ($municipio) {
            $q->where('municipio', $municipio);
        })->pluck('nombre', 'id');

        return response()->json($colegios);
    }
    public function masAplazadosPorMunicipio(Request $request)
    {
        // Mostrar todos los municipios con su colegio más aplazado (limitado a 30 para evitar error de memoria)
        $ultimoAnio = Estadistica::where('categoria', 'reprobados')->max('anio');
        $municipios = Ubicacion::select('municipio')->distinct()->orderBy('municipio')->pluck('municipio');
        $colegiosConReprobados = School::select('schools.id', 'schools.nombre', 'ubicacions.municipio')
            ->leftJoin('ubicacions', function($join) {
                $join->on('schools.id', '=', 'ubicacions.school_id');
            })
            ->join('estadisticas', function($join) use ($ultimoAnio) {
                $join->on('estadisticas.school_id', '=', 'schools.id')
                    ->where('estadisticas.categoria', 'reprobados')
                    ->where('estadisticas.anio', $ultimoAnio);
            })
            ->addSelect(['reprobados' => Estadistica::select('total')
                ->whereColumn('school_id', 'schools.id')
                ->where('categoria', 'reprobados')
                ->where('anio', $ultimoAnio)
                ->limit(1)
            ])
            ->get();
        $porMunicipio = $colegiosConReprobados->groupBy('municipio')->map(function($items) {
            return $items->sortByDesc('reprobados')->first();
        });
        $resultados = collect();
        foreach ($municipios as $municipio) {
            $colegio = $porMunicipio->get($municipio);
            $resultados->push((object)[
                'municipio' => $municipio,
                'colegio' => $colegio ? $colegio->nombre : null,
                'reprobados' => $colegio ? (int)$colegio->reprobados : 0,
                'id' => $colegio ? $colegio->id : null,
            ]);
        }
        // Ordenar por reprobados descendente
        $resultados = $resultados->sortByDesc('reprobados')->values();
        return view('schools.municipios_aplazados', [
            'resultados' => $resultados,
            'ultimoAnio' => $ultimoAnio
        ]);
    }
}