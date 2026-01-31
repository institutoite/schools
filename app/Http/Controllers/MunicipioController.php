<?php

namespace App\Http\Controllers;

use App\Models\Estadistica;
use App\Models\School;
use App\Models\Ubicacion;
use App\Services\DistritoMunicipalGeoService;
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

    public function aplazadosPorDistritoMunicipal(Request $request)
    {
        $aniosDisponibles = Estadistica::select('anio')->distinct()->orderBy('anio', 'desc')->pluck('anio');
        $anio = (int)($request->get('gestion') ?? $aniosDisponibles->first() ?? now()->year);
        $distritoSeleccionado = $request->get('distrito_municipal');
        $orderBy = $request->get('orderBy', 'reprobados');
        $orderDir = $request->get('orderDir', 'desc');
        $orderByDetalle = $request->get('orderByDetalle', 'reprobados');
        $orderDirDetalle = $request->get('orderDirDetalle', 'desc');

        $geoService = app(DistritoMunicipalGeoService::class);
        $districts = $geoService->getDistricts();
        $districtLabels = collect($districts)->pluck('label')->sort()->values();

        $schools = School::query()
            ->select('schools.id', 'schools.nombre', 'ubicacions.latitud', 'ubicacions.longitud')
            ->join('ubicacions', 'ubicacions.school_id', '=', 'schools.id')
            ->leftJoin('estadisticas as rep', function ($join) use ($anio) {
                $join->on('rep.school_id', '=', 'schools.id')
                    ->where('rep.categoria', 'reprobados')
                    ->where('rep.anio', $anio);
            })
            ->leftJoin('estadisticas as mat', function ($join) use ($anio) {
                $join->on('mat.school_id', '=', 'schools.id')
                    ->where('mat.categoria', 'matricula')
                    ->where('mat.anio', $anio);
            })
            ->selectRaw('COALESCE(rep.total, 0) as reprobados')
            ->selectRaw('COALESCE(mat.total, 0) as matricula')
            ->whereNotNull('ubicacions.latitud')
            ->whereNotNull('ubicacions.longitud')
            ->get();

        $schoolsWithDistrict = $schools->map(function ($school) use ($geoService) {
            $lat = (float)($school->latitud ?? 0);
            $lng = (float)($school->longitud ?? 0);
            $district = $geoService->findDistrict($lng, $lat);
            if ($district) {
                $school->distrito_municipal = $district['label'];
            }

            return $school;
        })->filter(fn ($school) => !empty($school->distrito_municipal))->values();

        $rankingPorDistrito = $schoolsWithDistrict
            ->groupBy('distrito_municipal')
            ->map(function ($items) {
                $top = $items->sortByDesc('reprobados')->first();
                $matricula = (int)($top->matricula ?? 0);
                $reprobados = (int)($top->reprobados ?? 0);
                $porcentaje = $matricula > 0 ? round(($reprobados / $matricula) * 100, 2) : 0;

                return (object) [
                    'distrito' => $top->distrito_municipal,
                    'colegio' => $top->nombre,
                    'reprobados' => $reprobados,
                    'matricula' => $matricula,
                    'porcentaje' => $porcentaje,
                    'id' => $top->id,
                ];
            })
            ->values()
            ->pipe(function ($items) use ($orderBy, $orderDir) {
                return $this->sortCollection($items, $orderBy, $orderDir)->values();
            });

        $colegiosDistrito = collect();
        if ($distritoSeleccionado) {
            $colegiosDistrito = $schoolsWithDistrict
                ->where('distrito_municipal', $distritoSeleccionado)
                ->map(function ($school) {
                    $matricula = (int)($school->matricula ?? 0);
                    $reprobados = (int)($school->reprobados ?? 0);
                    $porcentaje = $matricula > 0 ? round(($reprobados / $matricula) * 100, 2) : 0;

                    return [
                        'id' => $school->id,
                        'nombre' => $school->nombre,
                        'reprobados' => $reprobados,
                        'matricula' => $matricula,
                        'porcentaje' => $porcentaje,
                    ];
                })
                ->pipe(function ($items) use ($orderByDetalle, $orderDirDetalle) {
                    return $this->sortCollection($items, $orderByDetalle, $orderDirDetalle)->values();
                });
        }

        return view('schools.distritos_municipales_aplazados', [
            'anio' => $anio,
            'aniosDisponibles' => $aniosDisponibles,
            'districtLabels' => $districtLabels,
            'distritoSeleccionado' => $distritoSeleccionado,
            'rankingPorDistrito' => $rankingPorDistrito,
            'colegiosDistrito' => $colegiosDistrito,
            'orderBy' => $orderBy,
            'orderDir' => $orderDir,
            'orderByDetalle' => $orderByDetalle,
            'orderDirDetalle' => $orderDirDetalle,
        ]);
    }

    public function aplazadosPorDistrito(Request $request)
    {
        $municipio = $request->get('municipio');
        $distrito = $request->get('distrito');
        $orderBy = $request->get('orderBy', 'reprobados');
        $orderDir = $request->get('orderDir', 'desc');
        $orderByDetalle = $request->get('orderByDetalle', 'reprobados');
        $orderDirDetalle = $request->get('orderDirDetalle', 'desc');

        $aniosDisponibles = Estadistica::select('anio')->distinct()->orderBy('anio', 'desc')->pluck('anio');
        $anio = (int)($request->get('gestion') ?? $aniosDisponibles->first() ?? now()->year);

        $municipios = Ubicacion::whereNotNull('municipio')
            ->distinct()
            ->orderBy('municipio')
            ->pluck('municipio');

        $distritos = collect();
        $rankingPorDistrito = collect();
        $colegiosDistrito = collect();

        if ($municipio) {
            $distritos = Ubicacion::where('municipio', $municipio)
                ->whereNotNull('distrito')
                ->distinct()
                ->orderBy('distrito')
                ->pluck('distrito');

            $schools = School::query()
                ->select('schools.id', 'schools.nombre', 'ubicacions.distrito')
                ->join('ubicacions', 'ubicacions.school_id', '=', 'schools.id')
                ->leftJoin('estadisticas as rep', function ($join) use ($anio) {
                    $join->on('rep.school_id', '=', 'schools.id')
                        ->where('rep.categoria', 'reprobados')
                        ->where('rep.anio', $anio);
                })
                ->leftJoin('estadisticas as mat', function ($join) use ($anio) {
                    $join->on('mat.school_id', '=', 'schools.id')
                        ->where('mat.categoria', 'matricula')
                        ->where('mat.anio', $anio);
                })
                ->where('ubicacions.municipio', $municipio)
                ->whereNotNull('ubicacions.distrito')
                ->selectRaw('COALESCE(rep.total, 0) as reprobados')
                ->selectRaw('COALESCE(mat.total, 0) as matricula')
                ->get();

            $rankingPorDistrito = $schools->groupBy('distrito')->map(function ($items) {
                $top = $items->sortByDesc('reprobados')->first();
                $matricula = (int)($top->matricula ?? 0);
                $reprobados = (int)($top->reprobados ?? 0);
                $porcentaje = $matricula > 0 ? round(($reprobados / $matricula) * 100, 2) : 0;

                return (object) [
                    'distrito' => $top->distrito,
                    'colegio' => $top->nombre,
                    'reprobados' => $reprobados,
                    'matricula' => $matricula,
                    'porcentaje' => $porcentaje,
                    'id' => $top->id,
                ];
            })->values()
              ->pipe(function ($items) use ($orderBy, $orderDir) {
                  return $this->sortCollection($items, $orderBy, $orderDir)->values();
              });

            if ($distrito) {
                $colegiosDistrito = $schools->where('distrito', $distrito)
                    ->map(function ($school) {
                        $matricula = (int)($school->matricula ?? 0);
                        $reprobados = (int)($school->reprobados ?? 0);
                        $porcentaje = $matricula > 0 ? round(($reprobados / $matricula) * 100, 2) : 0;

                        return [
                            'id' => $school->id,
                            'nombre' => $school->nombre,
                            'reprobados' => $reprobados,
                            'matricula' => $matricula,
                            'porcentaje' => $porcentaje,
                        ];
                    })
                    ->pipe(function ($items) use ($orderByDetalle, $orderDirDetalle) {
                        return $this->sortCollection($items, $orderByDetalle, $orderDirDetalle)->values();
                    });
            }
        }

        return view('schools.distritos_aplazados', [
            'municipios' => $municipios,
            'municipio' => $municipio,
            'distritos' => $distritos,
            'distrito' => $distrito,
            'anio' => $anio,
            'aniosDisponibles' => $aniosDisponibles,
            'rankingPorDistrito' => $rankingPorDistrito,
            'colegiosDistrito' => $colegiosDistrito,
            'orderBy' => $orderBy,
            'orderDir' => $orderDir,
            'orderByDetalle' => $orderByDetalle,
            'orderDirDetalle' => $orderDirDetalle,
        ]);
    }

    private function sortCollection($items, string $field, string $dir)
    {
        $allowed = ['reprobados', 'porcentaje'];
        $field = in_array($field, $allowed, true) ? $field : 'reprobados';
        $dir = $dir === 'asc' ? 'asc' : 'desc';

        $callback = function ($item) use ($field) {
            if (is_array($item)) {
                return $item[$field] ?? 0;
            }
            if (is_object($item)) {
                return $item->{$field} ?? 0;
            }
            return 0;
        };

        return $dir === 'asc' ? $items->sortBy($callback) : $items->sortByDesc($callback);
    }
}