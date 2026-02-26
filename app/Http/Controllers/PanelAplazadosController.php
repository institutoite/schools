<?php

namespace App\Http\Controllers;

use App\Models\Estadistica;
use App\Models\School;
use App\Models\Ubicacion;
use Illuminate\Http\Request;

class PanelAplazadosController extends Controller
{
    public function index(Request $request)
    {
        $anio = (int) ($request->get('anio')
            ?? Estadistica::where('categoria', 'reprobados')->max('anio')
            ?? now()->year);

        $departamento = $this->normalizeFilter($request->get('departamento'));
        $provincia = $this->normalizeFilter($request->get('provincia'));
        $municipio = $this->normalizeFilter($request->get('municipio'));
        $distrito = $this->normalizeFilter($request->get('distrito'));

        $query = School::query()
            ->join('ubicacions', 'ubicacions.school_id', '=', 'schools.id')
            ->leftJoin('estadisticas as rep', function ($join) use ($anio) {
                $join->on('rep.school_id', '=', 'schools.id')
                    ->where('rep.categoria', '=', 'reprobados')
                    ->where('rep.anio', '=', $anio);
            })
            ->select(
                'schools.id',
                'schools.nombre',
                'schools.codigo_rue',
                'ubicacions.departamento',
                'ubicacions.provincia',
                'ubicacions.municipio',
                'ubicacions.distrito',
                'ubicacions.latitud',
                'ubicacions.longitud'
            )
            ->selectRaw('COALESCE(rep.total, 0) as aplazados')
            ->whereNotNull('ubicacions.latitud')
            ->whereNotNull('ubicacions.longitud');

        if ($departamento !== '') {
            $query->where('ubicacions.departamento', $departamento);
        }

        if ($provincia !== '') {
            $query->where('ubicacions.provincia', $provincia);
        }

        if ($municipio !== '') {
            $query->where('ubicacions.municipio', $municipio);
        }

        if ($distrito !== '') {
            $query->where('ubicacions.distrito', $distrito);
        }

        $schools = $query->orderByDesc('aplazados')->get();

        $ubicacionOptions = $this->buildUbicacionOptions($departamento, $provincia, $municipio);

        $aniosDisponibles = Estadistica::query()
            ->where('categoria', 'reprobados')
            ->select('anio')
            ->distinct()
            ->orderBy('anio', 'desc')
            ->pluck('anio');

        $resumen = [
            'total_colegios' => $schools->count(),
            'total_aplazados' => (int) $schools->sum('aplazados'),
            'max_aplazados' => (int) ($schools->max('aplazados') ?? 0),
            'min_aplazados' => (int) ($schools->min('aplazados') ?? 0),
            'promedio_aplazados' => round((float) ($schools->avg('aplazados') ?? 0), 2),
        ];

        $topEscuelas = $schools->take(30);

        $departamentos = $ubicacionOptions['departamentos'];
        $provincias = $ubicacionOptions['provincias'];
        $municipios = $ubicacionOptions['municipios'];
        $distritos = $ubicacionOptions['distritos'];

        return view('panels.aplazados_heatmap', compact(
            'schools',
            'topEscuelas',
            'resumen',
            'anio',
            'aniosDisponibles',
            'departamento',
            'departamentos',
            'provincia',
            'provincias',
            'municipio',
            'municipios',
            'distrito',
            'distritos'
        ));
    }

    public function opcionesUbicacion(Request $request)
    {
        $departamento = $this->normalizeFilter($request->get('departamento'));
        $provincia = $this->normalizeFilter($request->get('provincia'));
        $municipio = $this->normalizeFilter($request->get('municipio'));

        return response()->json(
            $this->buildUbicacionOptions($departamento, $provincia, $municipio)
        );
    }

    private function buildUbicacionOptions(string $departamento = '', string $provincia = '', string $municipio = ''): array
    {
        $departamentos = Ubicacion::query()
            ->whereNotNull('departamento')
            ->where('departamento', '!=', '')
            ->select('departamento')
            ->distinct()
            ->orderBy('departamento')
            ->pluck('departamento')
            ->values();

        $provincias = Ubicacion::query()
            ->whereNotNull('provincia')
            ->where('provincia', '!=', '')
            ->when($departamento !== '', function ($q) use ($departamento) {
                $q->where('departamento', $departamento);
            })
            ->select('provincia')
            ->distinct()
            ->orderBy('provincia')
            ->pluck('provincia')
            ->values();

        $municipios = Ubicacion::query()
            ->whereNotNull('municipio')
            ->where('municipio', '!=', '')
            ->when($departamento !== '', function ($q) use ($departamento) {
                $q->where('departamento', $departamento);
            })
            ->when($provincia !== '', function ($q) use ($provincia) {
                $q->where('provincia', $provincia);
            })
            ->select('municipio')
            ->distinct()
            ->orderBy('municipio')
            ->pluck('municipio')
            ->values();

        $distritos = Ubicacion::query()
            ->whereNotNull('distrito')
            ->where('distrito', '!=', '')
            ->when($departamento !== '', function ($q) use ($departamento) {
                $q->where('departamento', $departamento);
            })
            ->when($provincia !== '', function ($q) use ($provincia) {
                $q->where('provincia', $provincia);
            })
            ->when($municipio !== '', function ($q) use ($municipio) {
                $q->where('municipio', $municipio);
            })
            ->select('distrito')
            ->distinct()
            ->orderBy('distrito')
            ->pluck('distrito')
            ->values();

        return [
            'departamentos' => $departamentos,
            'provincias' => $provincias,
            'municipios' => $municipios,
            'distritos' => $distritos,
        ];
    }

    private function normalizeFilter(?string $value): string
    {
        $clean = trim((string) $value);

        if ($clean === '' || strtoupper($clean) === 'TODOS') {
            return '';
        }

        return $clean;
    }
}