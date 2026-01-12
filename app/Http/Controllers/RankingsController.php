<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\Estadistica;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RankingsController extends Controller
{
    public static function buildKpis(): array
    {
        $total = School::count();

        // Último año con datos de matrícula
        $latestYear = Estadistica::where('categoria', 'matricula')->max('anio');

        // Promedios nacionales simples (reprobación / abandono) para el último año disponible
        $totalMat = Estadistica::where('categoria', 'matricula')->when($latestYear, fn($q) => $q->where('anio', $latestYear))->sum('total');
        $totalRep = Estadistica::where('categoria', 'reprobados')->when($latestYear, fn($q) => $q->where('anio', $latestYear))->sum('total');
        $totalAban = Estadistica::where('categoria', 'abandono')->when($latestYear, fn($q) => $q->where('anio', $latestYear))->sum('total');

        $repProm = $totalMat > 0 ? round(($totalRep / $totalMat) * 100, 2) : null;
        $abanProm = $totalMat > 0 ? round(($totalAban / $totalMat) * 100, 2) : null;

        // Porcentaje de colegios con algún indicador de internet en servicios (heurística: columna 'internet' no nula/negativa)
        // Evitamos dependencias fuertes por variabilidad de datos; si no existe, quedará null.
        try {
            $withInternet = DB::table('servicios')->whereNotNull('internet')->whereNotIn('internet', ['--', 'No', 'NO', '0', ''])->count();
            $pctInternet = $total > 0 ? round(($withInternet / $total) * 100, 2) : null;
        } catch (\Throwable $e) {
            $pctInternet = null;
        }

        return [
            'total' => $total ?: null,
            'pct_internet' => $pctInternet,
            'rep_prom' => $repProm,
            'aban_prom' => $abanProm,
            'anio' => $latestYear,
        ];
    }

    public static function buildHighlights(): array
    {
        // Años por categoría para cruzar correctamente
        $matYear = Estadistica::where('categoria', 'matricula')->max('anio');
        $repYear = Estadistica::where('categoria', 'reprobados')->max('anio');
        $abanYear = Estadistica::where('categoria', 'abandono')->max('anio');
        $repCommonYear = $matYear && $repYear ? min($matYear, $repYear) : ($repYear ?: $matYear);
        $abanCommonYear = $matYear && $abanYear ? min($matYear, $abanYear) : ($abanYear ?: $matYear);

        // Top matrícula (último año)
        $topMatricula = Estadistica::select('school_id', DB::raw('SUM(total) as total'))
            ->where('categoria', 'matricula')
            ->when($matYear, fn($q) => $q->where('anio', $matYear))
            ->groupBy('school_id')
            ->orderByDesc('total')
            ->first();

        $topMatData = null;
        if ($topMatricula) {
            $school = School::find($topMatricula->school_id);
            $topMatData = [
                'nombre' => $school?->nombre,
                'valor' => number_format((int)$topMatricula->total),
                'detalle' => $matYear ? "Matrícula $matYear" : 'Matrícula',
            ];
        }

        // Mayor reprobación (ratio reprobados/matrícula)
        $ratios = Estadistica::select('school_id')
            ->selectRaw("SUM(CASE WHEN categoria='reprobados' AND anio=? THEN total ELSE 0 END) as rep", [$repCommonYear])
            ->selectRaw("SUM(CASE WHEN categoria='matricula' AND anio=? THEN total ELSE 0 END) as mat", [$repCommonYear])
            ->groupBy('school_id')
            ->get()
            ->map(function ($r) {
                $r->ratio = ($r->mat ?? 0) > 0 ? ($r->rep * 100.0 / $r->mat) : 0;
                return $r;
            })
            ->sortByDesc('ratio')
            ->first();

        $topRepData = null;
        if ($ratios && $ratios->mat > 0) {
            $school = School::find($ratios->school_id);
            $topRepData = [
                'nombre' => $school?->nombre,
                'valor' => round($ratios->ratio, 2) . '%',
                'detalle' => $repCommonYear ? "Reprobación $repCommonYear" : 'Reprobación',
            ];
        }

        // Cero reprobación (colegio con rep=0 y mat>0, tomar uno cualquiera destacado)
        $cero = Estadistica::select('school_id')
            ->selectRaw("SUM(CASE WHEN categoria='reprobados' AND anio=? THEN total ELSE 0 END) as rep", [$repCommonYear])
            ->selectRaw("SUM(CASE WHEN categoria='matricula' AND anio=? THEN total ELSE 0 END) as mat", [$repCommonYear])
            ->groupBy('school_id')
            ->having('mat', '>', 0)
            ->having('rep', '=', 0)
            ->first();

        $ceroData = null;
        if ($cero) {
            $school = School::find($cero->school_id);
            $ceroData = [
                'nombre' => $school?->nombre,
                'valor' => '0%',
                'detalle' => 'Sin reprobados',
            ];
        }

        // Mayor abandono (ratio abandono/matrícula)
        $abanRows = Estadistica::select('school_id')
            ->selectRaw("SUM(CASE WHEN categoria='abandono' AND anio=? THEN total ELSE 0 END) as aban", [$abanCommonYear])
            ->selectRaw("SUM(CASE WHEN categoria='matricula' AND anio=? THEN total ELSE 0 END) as mat", [$abanCommonYear])
            ->groupBy('school_id')
            ->get()
            ->map(function ($r) {
                $r->ratio = ($r->mat ?? 0) > 0 ? ($r->aban * 100.0 / $r->mat) : 0;
                return $r;
            })
            ->sortByDesc('ratio')
            ->first();

        $topAbanData = null;
        if ($abanRows && $abanRows->mat > 0) {
            $school = School::find($abanRows->school_id);
            $topAbanData = [
                'nombre' => $school?->nombre,
                'valor' => round($abanRows->ratio, 2) . '%',
                'detalle' => $abanCommonYear ? "Abandono $abanCommonYear" : 'Abandono',
            ];
        }

        // Mejor infraestructura (heurística con servicios: agua, electricidad, baños, internet)
        try {
            $infraTop = DB::table('servicios')
                ->select('school_id')
                ->selectRaw(
                    "(CASE WHEN LOWER(COALESCE(agua,'')) IN ('si','sí','yes','1') THEN 1 ELSE 0 END) +" .
                    " (CASE WHEN LOWER(COALESCE(electricidad,'')) IN ('si','sí','yes','1') THEN 1 ELSE 0 END) +" .
                    " (CASE WHEN LOWER(COALESCE(banos,'')) IN ('si','sí','yes','1') THEN 1 ELSE 0 END) +" .
                    " (CASE WHEN LOWER(COALESCE(internet,'')) IN ('si','sí','yes','1') THEN 1 ELSE 0 END) AS score"
                )
                ->orderByDesc('score')
                ->orderBy('school_id')
                ->first();
        } catch (\Throwable $e) {
            $infraTop = null;
        }

        $infraData = null;
        if ($infraTop) {
            $school = School::find($infraTop->school_id);
            $infraData = [
                'nombre' => $school?->nombre,
                'valor' => (string) ($infraTop->score ?? 0),
                'detalle' => 'Suma de servicios (agua, electricidad, baños, internet)',
            ];
        }

        // Cobertura de internet (nacional, usando KPI existente)
        $kpis = self::buildKpis();
        $internetData = [
            'nombre' => 'Cobertura nacional',
            'valor' => isset($kpis['pct_internet']) ? ($kpis['pct_internet'] . '%') : '—',
            'detalle' => 'Colegios con servicio de internet',
        ];

        return [
            'top_matricula' => $topMatData,
            'top_reprobacion' => $topRepData,
            'cero_reprobacion' => $ceroData,
            'top_abandono' => $topAbanData,
            'infra_mejor' => $infraData,
            'con_internet' => $internetData,
        ];
    }

    public function index(Request $request)
    {
        // Detectar tipo de ranking
        $tipo = $request->query('tipo', 'reprobacion');
        $anio = $request->query('anio');
        $departamento = $request->query('departamento');
        $nivel = $request->query('nivel'); // distrital, municipal, provincial, departamental, nacional
        $q = trim((string)$request->query('q', '')); // nombre de colegio para posición
        $schoolIdParam = $request->query('school_id');
        // Años por categoría para seleccionar año consistente
        $matYear = Estadistica::where('categoria', 'matricula')->max('anio');
        $repYear = Estadistica::where('categoria', 'reprobados')->max('anio');
        $abanYear = Estadistica::where('categoria', 'abandono')->max('anio');
        // Lista de años disponibles para reprobación (intersección con matrícula)
        $yearsRep = Estadistica::where('categoria', 'reprobados')->select('anio')->distinct()->pluck('anio')->sort()->values();
        $yearsMat = Estadistica::where('categoria', 'matricula')->select('anio')->distinct()->pluck('anio')->sort()->values();
        $years = $yearsRep->intersect($yearsMat)->sort()->values();
        if (!$anio) {
            $anio = $years->last();
        }

        // Filtros por ubicación y nivel
        // Intentar autocompletar filtros por ubicación según el colegio buscado
        $schoolIds = null; $autoUbic = null;
        if ($q || $schoolIdParam) {
            $match = $schoolIdParam ? School::with('ubicacion')->find($schoolIdParam) : School::with('ubicacion')->where('nombre', 'like', "%$q%")->first();
            if ($match && $match->ubicacion) {
                $autoUbic = $match->ubicacion; // departamento, provincia, municipio, distrito
                $ubic = \App\Models\Ubicacion::query();
                if ($nivel && strtolower($nivel) !== 'nacional') {
                    if ($nivel === 'departamental') $ubic->where('departamento', $autoUbic->departamento);
                    if ($nivel === 'provincial') { $ubic->where('departamento', $autoUbic->departamento)->where('provincia', $autoUbic->provincia); }
                    if ($nivel === 'municipal') { $ubic->where('departamento', $autoUbic->departamento)->where('municipio', $autoUbic->municipio); }
                    if ($nivel === 'distrital') { $ubic->where('departamento', $autoUbic->departamento)->where('municipio', $autoUbic->municipio)->where('distrito', $autoUbic->distrito); }
                    $schoolIds = $ubic->pluck('school_id');
                }
            }
        }
        // Si no hubo búsqueda o nivel nacional, aplicar filtros manuales si se proporcionan
        if (!$schoolIds) {
            if ($nivel && strtolower($nivel) !== 'nacional') {
                $ubic = \App\Models\Ubicacion::query();
                if ($departamento) $ubic->where('departamento', $departamento);
                if ($request->query('provincia')) $ubic->where('provincia', $request->query('provincia'));
                if ($request->query('municipio')) $ubic->where('municipio', $request->query('municipio'));
                if ($request->query('distrito')) $ubic->where('distrito', $request->query('distrito'));
                $schoolIds = $ubic->pluck('school_id');
            } elseif ($departamento) {
                $schoolIds = \App\Models\Ubicacion::where('departamento', $departamento)->pluck('school_id');
            }
        }

        // Lógica separada para cada tipo
        $orderDir = strtolower($request->get('orderDir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $page = max(1, (int)$request->query('page', 1));
        $perPage = 50;
        if ($tipo === 'matricula') {
            // Ranking por matrícula
            $rows = Estadistica::select('school_id')
                ->selectRaw("SUM(CASE WHEN categoria='matricula' AND anio=? THEN total ELSE 0 END) as mat", [$anio])
                ->when($schoolIds, fn($q) => $q->whereIn('school_id', $schoolIds))
                ->groupBy('school_id')->get();
            $itemsCol = $orderDir === 'asc'
                ? $rows->sortBy(fn($r) => (int)($r->mat ?? 0))
                : $rows->sortByDesc(fn($r) => (int)($r->mat ?? 0));
            $itemsCol = $itemsCol->values()->map(fn($r) => (object)[
                'school_id' => $r->school_id,
                'mat' => (int)($r->mat ?? 0),
            ]);
            $items = new \Illuminate\Pagination\LengthAwarePaginator(
                $itemsCol->slice(($page-1)*$perPage, $perPage)->values(),
                $itemsCol->count(),
                $perPage,
                $page,
                ['path' => $request->url(), 'query' => $request->query()]
            );
            $itemsCount = $items;
        } else {
            // Ranking por reprobados
            $rows = Estadistica::select('school_id')
                ->selectRaw("SUM(CASE WHEN categoria='reprobados' AND anio=? THEN total ELSE 0 END) as rep", [$anio])
                ->when($schoolIds, fn($q) => $q->whereIn('school_id', $schoolIds))
                ->groupBy('school_id')->get();
            $itemsCol = $orderDir === 'asc'
                ? $rows->sortBy(fn($r) => (int)($r->rep ?? 0))
                : $rows->sortByDesc(fn($r) => (int)($r->rep ?? 0));
            $itemsCol = $itemsCol->values()->map(fn($r) => (object)[
                'school_id' => $r->school_id,
                'rep' => (int)($r->rep ?? 0),
            ]);
            $items = new \Illuminate\Pagination\LengthAwarePaginator(
                $itemsCol->slice(($page-1)*$perPage, $perPage)->values(),
                $itemsCol->count(),
                $perPage,
                $page,
                ['path' => $request->url(), 'query' => $request->query()]
            );
            $itemsCount = $items;
        }

        // Calcular posición de un colegio buscado si se proporciona 'q'
        $pos = null; $posTotal = null; $posLabel = null; $targetName = $q ?: null; $posValue = null; $posMetric = null;
        $selectedSchool = null; $selectedRep = null; $selectedMat = null; $selectedRatio = null;
        if ($q || $schoolIdParam) {
            if ($tipo === 'reprobacion') {
            $modo = $request->query('modo', 'cantidad');
                if ($modo === 'cantidad') {
                    $all = Estadistica::select('school_id')
                        ->selectRaw("SUM(CASE WHEN categoria='reprobados' AND anio=? THEN total ELSE 0 END) as rep", [$anio])
                        ->selectRaw("SUM(CASE WHEN categoria='matricula' AND anio=? THEN total ELSE 0 END) as mat", [$anio])
                        ->when($schoolIds, fn($q) => $q->whereIn('school_id', $schoolIds))
                        ->groupBy('school_id')->get()
                        ->sortByDesc(fn($r) => (int)($r->rep ?? 0))
                        ->values();
                } else { // porcentaje por defecto
                    $all = Estadistica::select('school_id')
                        ->selectRaw("SUM(CASE WHEN categoria='reprobados' AND anio=? THEN total ELSE 0 END) as rep", [$anio])
                        ->selectRaw("SUM(CASE WHEN categoria='matricula' AND anio=? THEN total ELSE 0 END) as mat", [$anio])
                        ->when($schoolIds, fn($q) => $q->whereIn('school_id', $schoolIds))
                        ->groupBy('school_id')->get()
                        ->map(function ($r) { $r->ratio = ($r->mat ?? 0) > 0 ? ($r->rep * 100.0 / $r->mat) : 0; return $r; })
                        ->sortByDesc('ratio')
                        ->values();
                }
                $posTotal = $all->count();
                $pos = null;
                foreach ($all as $idx => $r) {
                    $school = School::find($r->school_id);
                    $matchesById = $schoolIdParam && $school && (string)$school->id === (string)$schoolIdParam;
                    $matchesByName = $q && $school && stripos($school->nombre, $q) !== false;
                    if ($matchesById || $matchesByName) {
                        $pos = $idx + 1; $targetName = $school->nombre;
                        if ($modo === 'cantidad') { $posValue = (int)($r->rep ?? 0); $posMetric = 'cantidad'; }
                        else { $posValue = round((float)($r->ratio ?? 0), 2); $posMetric = 'porcentaje'; }
                        $selectedSchool = $school;
                        $selectedRep = (int)($r->rep ?? 0);
                        $selectedMat = (int)($r->mat ?? 0);
                        $selectedRatio = ($selectedMat > 0) ? round($selectedRep * 100.0 / $selectedMat, 2) : null;
                        break;
                    }
                }
                $posLabel = 'posición por cantidad';
            }
        }

        // Si hay un colegio seleccionado y estamos en modo cantidad, ajustar la página para mostrar su fila
        if (($q || $schoolIdParam) && isset($itemsCount) && $itemsCount instanceof \Illuminate\Pagination\LengthAwarePaginator && $pos) {
            $perPage = $itemsCount->perPage();
            $desiredPage = (int)ceil($pos / $perPage);
            $itemsColAll = $rows->sortByDesc(fn($r) => (int)($r->rep ?? 0))
                ->values()
                ->map(fn($r) => (object)['school_id' => $r->school_id, 'rep' => (int)($r->rep ?? 0), 'mat' => (int)($r->mat ?? 0)]);
            $itemsCount = new \Illuminate\Pagination\LengthAwarePaginator(
                $itemsColAll->slice(($desiredPage-1)*$perPage, $perPage)->values(),
                $itemsColAll->count(),
                $perPage,
                $desiredPage,
                ['path' => $request->url(), 'query' => array_merge($request->query(), ['page' => $desiredPage])]
            );
        }

        // Respuesta AJAX parcial usando sub-vistas si disponibles
        if ($request->ajax() || $request->query('ajax')) {
            try {
                $contextHtml = view('rankings._context', [
                    'itemsCount' => $itemsCount ?? null,
                    'autoUbic' => $autoUbic,
                    'nivel' => $nivel,
                    'targetName' => $targetName,
                    'pos' => $pos,
                    'posValue' => $posValue,
                    'posTotal' => $posTotal,
                    'selectedSchool' => $selectedSchool,
                    'selectedRep' => $selectedRep,
                    'selectedMat' => $selectedMat,
                    'selectedRatio' => $selectedRatio,
                    'anio' => $anio,
                ])->render();
                $tableHtml = view('rankings._table', [
                    'itemsCount' => $itemsCount ?? null,
                    'anio' => $anio,
                    'selectedId' => $schoolIdParam,
                ])->render();
            } catch (\Throwable $e) {
                // Fallback: renderizar secciones directamente desde la vista principal
                $full = view('rankings.index', [
                    'tipo' => $tipo,
                    'anio' => $anio,
                    'itemsCount' => $itemsCount ?? null,
                    'autoUbic' => $autoUbic,
                    'nivel' => $nivel,
                    'targetName' => $targetName,
                    'pos' => $pos,
                    'posValue' => $posValue,
                    'posTotal' => $posTotal,
                    'selectedSchool' => $selectedSchool,
                    'selectedRep' => $selectedRep,
                    'selectedMat' => $selectedMat,
                    'selectedRatio' => $selectedRatio,
                ])->render();
                $contextHtml = $full; // como mínimo devolver algo
                $tableHtml = $full;
            }
            return response()->json(['contextHtml' => $contextHtml, 'tableHtml' => $tableHtml]);
        }
        // Renderizar la vista correspondiente
        if ($tipo === 'matricula') {
            return view('rankings.index_matricula', [
                'anio' => $anio,
                'itemsCount' => $itemsCount ?? null,
                'departamento' => $departamento,
                'years' => $years,
                'autoUbic' => $autoUbic,
                'nivel' => $nivel,
                'q' => $q,
                'school_id' => $schoolIdParam,
            ]);
        } else {
            return view('rankings.index_reprobacion', [
                'anio' => $anio,
                'itemsCount' => $itemsCount ?? null,
                'departamento' => $departamento,
                'years' => $years,
                'autoUbic' => $autoUbic,
                'nivel' => $nivel,
                'q' => $q,
                'school_id' => $schoolIdParam,
            ]);
        }
    }

    // Endpoint para autocompletar colegios
    public function searchSchools(Request $request)
    {
        $q = trim((string)$request->query('q', ''));
        $limit = min(30, (int)$request->query('limit', 20));
        $query = School::query()->with('ubicacion');
        if ($q !== '') {
            $query->where('nombre', 'like', "%$q%");
        }
        $schools = $query->orderBy('nombre')->limit($limit)->get();
        $data = $schools->map(function ($s) {
            return [
                'id' => $s->id,
                'nombre' => $s->nombre,
                'codigo_rue' => $s->codigo_rue ?? null,
                'turno' => $s->turno ?? null,
                'nivel' => $s->nivel ?? null,
                'dependencia' => $s->dependencia ?? null,
                'ubicacion' => [
                    'departamento' => $s->ubicacion->departamento ?? null,
                    'provincia' => $s->ubicacion->provincia ?? null,
                    'municipio' => $s->ubicacion->municipio ?? null,
                    'distrito' => $s->ubicacion->distrito ?? null,
                ],
            ];
        });
        return response()->json($data);
    }
}
