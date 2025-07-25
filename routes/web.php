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

