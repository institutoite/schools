<?php

use App\Http\Controllers\SchoolController;
use App\Models\School;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
    
//     return view('welcome');
// });
// Página principal con búsqueda
Route::get('/', [SchoolController::class, 'index'])->name('home');
Route::get('/probabilidad', [SchoolController::class, 'probabilidad'])->name('probabilidad');

// Detalle del colegio (opcional)
Route::get('/schools/{school}', [SchoolController::class, 'show'])->name('schools.show');

Route::get('/schools/{id}', [\App\Http\Controllers\SchoolController::class, 'showDetails'])->name('school.details');

Route::get('/reprobados', [SchoolController::class, 'reprobados'])->name('reprobados.index');

Route::get('/colegios/fix-matriculas', [SchoolController::class, 'fixMatriculasFromJson']);

Route::get('/memory-test', function() {
    $usage = [];
    
    // Antes de la consulta
    $usage['start'] = memory_get_usage();
    
    // Consulta paginada
    $schools = School::paginate(15);
    $usage['after_query'] = memory_get_usage();
    
    return response()->json([
        'memory_usage' => [
            'start' => $usage['start'],
            'after_query' => $usage['after_query'],
            'difference' => $usage['after_query'] - $usage['start']
        ],
        'data_count' => $schools->count(),
        'total_records' => $schools->total()
    ]);
});

Route::get('/densidad-educativa', [SchoolController::class, 'densidadEducativa'])
    ->name('densidad.educativa');

Route::get('/debug-density-data', [SchoolController::class, 'debugDensityData'])
    ->name('debug.density.data');
Route::get('/test-densidad', function () {
    return view('schools.test-densidad');
});
Route::get('/debug-densidad', function () {
    return view('schools.debug-densidad');
});

Route::get('/test-main', function () {
    return view('schools.test-main');
});

Route::get('/test-data', function () {
    $latestYear = \App\Models\Estadistica::where('categoria', 'matricula')
        ->max('anio') ?? 2023;
    
    $results = \App\Models\Ubicacion::query()
        ->select('departamento')
        ->selectRaw('COUNT(DISTINCT schools.id) as cantidad_colegios')
        ->selectRaw('COALESCE(SUM(estadisticas.total), 0) as total_estudiantes')
        ->selectRaw('ROUND(COUNT(DISTINCT schools.id) * 100.0 / (SELECT COUNT(*) FROM schools), 2) as densidad')
        ->join('schools', 'ubicacions.school_id', '=', 'schools.id')
        ->leftJoin('estadisticas', function($join) use ($latestYear) {
            $join->on('estadisticas.school_id', '=', 'schools.id')
                ->where('estadisticas.categoria', '=', 'matricula')
                ->where('estadisticas.anio', '=', $latestYear);
        })
        ->groupBy('departamento')
        ->orderBy('cantidad_colegios', 'desc')
        ->get();
    
    return response()->json([
        'latestYear' => $latestYear,
        'results' => $results,
        'total_colegios' => $results->sum('cantidad_colegios'),
        'total_estudiantes' => $results->sum('total_estudiantes'),
        'densidad_promedio' => $results->avg('densidad'),
        'count' => $results->count()
    ]);
});
