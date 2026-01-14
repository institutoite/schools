<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ficha de Colegio</title>
    <link rel="icon" href="{{ asset('images/ite.ico') }}" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: 'rgb(38,186,165)',
                        secondary: 'rgb(55,95,122)',
                        accent: '#f9fafb'
                    }
                }
            }
        }
    </script>
    <script src="https://kit.fontawesome.com/your-code.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <link rel="stylesheet" href="{{ asset('css/vistas/welcome/mapa.css') }}">
    <link rel="stylesheet" href="{{ asset('css/vistas/welcome/unete.css') }}">
    <link rel="stylesheet" href="{{ asset('css/vistas/welcome/servicios.css') }}">
    <link rel="stylesheet" href="{{ asset('css/vistas/welcome/redes.css') }}">
    <link rel="stylesheet" href="{{ asset('css/vistas/welcome/style.css') }}">
</head>
<body class="bg-accent min-h-screen font-sans">
    <div class="max-w-5xl mx-auto py-6 px-2 sm:px-4">
        <!-- Botón de regreso -->
        <div class="mb-4">
            <a href="{{ url()->previous() }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-primary text-white font-semibold shadow hover:bg-primary-600 transition">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
        <!-- Encabezado -->
        <div class="bg-white rounded-2xl shadow-lg p-6 sm:p-8 mb-8 border border-primary-100 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div class="flex-1 text-center md:text-left">
                <h1 class="text-2xl sm:text-3xl font-extrabold text-primary flex items-center gap-3 justify-center md:justify-start">
                    <i class="fas fa-school"></i> {{ $school->nombre }}
                </h1>
                <p class="text-secondary text-base sm:text-lg mt-2 flex items-center gap-2 justify-center md:justify-start">
                    <i class="fas fa-map-marker-alt"></i> {{ $ubicaciones->departamento ?? 'N/A' }}, {{ $ubicaciones->provincia ?? '' }}
                </p>
                <span class="inline-block mt-3 px-4 py-1 rounded-full font-semibold text-white text-sm
                    @if(strtolower($school->dependencia) == 'fiscal') bg-primary
                    @elseif(strtolower($school->dependencia) == 'privado') bg-secondary
                    @else bg-gray-500 @endif">
                    <i class="fas fa-shield-alt"></i> {{ $school->dependencia }}
                </span>
            </div>
            <div class="flex flex-col items-center gap-2">
                <span class="text-xs uppercase tracking-wider text-secondary-500">Código RUE</span>
                <span class="text-xl sm:text-2xl font-bold text-primary">{{ $school->codigo_rue }}</span>
            </div>
        </div>

        <!-- Información General -->
        <div class="grid md:grid-cols-2 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow p-5 border border-primary-50">
                <h2 class="text-base sm:text-lg font-bold text-primary mb-3 flex items-center gap-2"><i class="fas fa-info-circle"></i> Información</h2>
                <ul class="space-y-1 text-secondary text-sm sm:text-base">
                    <li><i class="fas fa-user-tie"></i> <strong>Director:</strong> {{ $school->director ?? 'N/A' }}</li>
                    <li><i class="fas fa-location-arrow"></i> <strong>Dirección:</strong> {{ $school->direccion ?? 'N/A' }}</li>
                    <li><i class="fas fa-phone"></i> <strong>Teléfonos:</strong> {{ $school->telefonos ?? 'N/A' }}</li>
                    <li><i class="fas fa-layer-group"></i> <strong>Niveles:</strong> {{ $school->niveles ?? 'N/A' }}</li>
                    <li><i class="fas fa-clock"></i> <strong>Turnos:</strong> {{ $school->turnos ?? 'N/A' }}</li>
                </ul>
            </div>
            <div class="bg-white rounded-2xl shadow p-5 border border-primary-50">
                <h2 class="text-base sm:text-lg font-bold text-primary mb-3 flex items-center gap-2"><i class="fas fa-globe-americas"></i> Ubicación</h2>
                <ul class="space-y-1 text-secondary text-sm sm:text-base">
                    <li><i class="fas fa-map"></i> <strong>Municipio:</strong> {{ $ubicaciones->municipio ?? 'N/A' }}</li>
                    <li><i class="fas fa-map-pin"></i> <strong>Distrito:</strong> {{ $ubicaciones->distrito ?? 'N/A' }}</li>
                    <li><i class="fas fa-tree"></i> <strong>Área:</strong> {{ $ubicaciones->area ?? 'N/A' }}</li>
                    <li><i class="fas fa-location-arrow"></i> <strong>Coordenadas:</strong> {{ $ubicaciones->coordenadas_texto ?? 'N/A' }}</li>
                </ul>
            </div>
        </div>

        

        <!-- Servicios y Ambientes -->
        <div class="grid md:grid-cols-2 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow p-6 border border-primary-50">
                <h2 class="text-lg font-bold text-primary mb-4 flex items-center gap-2"><i class="fas fa-concierge-bell"></i> Servicios</h2>
                <div class="flex flex-wrap gap-3">
                    <span class="flex items-center gap-2 px-3 py-2 rounded-lg bg-primary/10 text-primary font-semibold">
                        <i class="fas fa-tint"></i> Agua: <span>{{ $servicios->agua ? 'Sí' : 'No' }}</span>
                    </span>
                    <span class="flex items-center gap-2 px-3 py-2 rounded-lg bg-secondary/10 text-secondary font-semibold">
                        <i class="fas fa-bolt"></i> Electricidad: <span>{{ $servicios->electricidad ? 'Sí' : 'No' }}</span>
                    </span>
                    <span class="flex items-center gap-2 px-3 py-2 rounded-lg bg-primary/10 text-primary font-semibold">
                        <i class="fas fa-restroom"></i> Baños: <span>{{ $servicios->banos ? 'Sí' : 'No' }}</span>
                    </span>
                    <span class="flex items-center gap-2 px-3 py-2 rounded-lg bg-secondary/10 text-secondary font-semibold">
                        <i class="fas fa-wifi"></i> Internet: <span>{{ $servicios->internet ? 'Sí' : 'No' }}</span>
                    </span>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow p-6 border border-primary-50">
                <h2 class="text-lg font-bold text-primary mb-4 flex items-center gap-2"><i class="fas fa-building"></i> Ambientes</h2>
                <div class="grid grid-cols-2 gap-3 text-secondary">
                    <div><i class="fas fa-chalkboard"></i> <strong>Aulas:</strong> {{ $ambientes->aulas ?? 0 }}</div>
                    <div><i class="fas fa-flask"></i> <strong>Laboratorios:</strong> {{ $ambientes->laboratorios ?? 0 }}</div>
                    <div><i class="fas fa-book"></i> <strong>Bibliotecas:</strong> {{ $ambientes->bibliotecas ?? 0 }}</div>
                    <div><i class="fas fa-desktop"></i> <strong>Computación:</strong> {{ $ambientes->computacion ?? 0 }}</div>
                    <div><i class="fas fa-futbol"></i> <strong>Canchas:</strong> {{ $ambientes->canchas ?? 0 }}</div>
                    <div><i class="fas fa-dumbbell"></i> <strong>Gimnasios:</strong> {{ $ambientes->gimnasios ?? 0 }}</div>
                    <div><i class="fas fa-university"></i> <strong>Coliseos:</strong> {{ $ambientes->coliseos ?? 0 }}</div>
                    <div><i class="fas fa-swimmer"></i> <strong>Piscinas:</strong> {{ $ambientes->piscinas ?? 0 }}</div>
                    <div><i class="fas fa-user-secret"></i> <strong>Secretaría:</strong> {{ $ambientes->secretaria ?? 0 }}</div>
                    <div><i class="fas fa-users"></i> <strong>Reuniones:</strong> {{ $ambientes->reuniones ?? 0 }}</div>
                    <div><i class="fas fa-tools"></i> <strong>Talleres:</strong> {{ $ambientes->talleres ?? 0 }}</div>
                </div>
            </div>
        </div>

        

<div class="bg-white rounded-xl shadow p-6 mb-8">
    <h2 class="text-xl font-bold text-primary mb-4"><i class="fas fa-chart-bar"></i> Estadísticas</h2>
    @php
        $categorias = collect($estadisticas)->groupBy('categoria');
        $anios = collect($estadisticas)->pluck('anio')->unique()->sort()->values();
        $iconos = [
            'matricula' => 'fa-users',
            'promovidos' => 'fa-arrow-up',
            'reprobados' => 'fa-arrow-down',
            'abandono' => 'fa-person-walking-arrow-right',
        ];
    @endphp
    <div class="flex flex-col gap-10">
        @foreach($categorias as $categoria => $stats)
        <div>
            <h3 class="text-lg font-semibold text-secondary mb-2 capitalize flex items-center gap-2">
                <i class="fas {{ $iconos[$categoria] ?? 'fa-tag' }}"></i> {{ $categoria }}
            </h3>
            <div class="w-full overflow-x-auto rounded-lg border border-primary-50">
                <table class="min-w-full mb-4">
                    <thead>
                        <tr class="bg-primary text-white">
                            <th class="py-3 px-4 text-base sm:text-lg font-bold whitespace-nowrap">Dato</th>
                            @foreach($anios as $anio)
                                <th class="py-3 px-4 text-base sm:text-lg font-bold whitespace-nowrap">{{ $anio }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(['total', 'mujer', 'hombre'] as $dato)
                        <tr class="@if($dato == 'total') bg-primary/10 @elseif($dato == 'mujer') bg-primary/5 @elseif($dato == 'hombre') bg-secondary/5 @endif">
                            <td class="py-2 px-4 font-bold capitalize whitespace-nowrap text-base sm:text-lg @if($dato == 'total') text-primary-700 @elseif($dato == 'mujer') text-primary-600 @else text-secondary-700 @endif">
                                @if($dato == 'total') <i class="fas fa-users"></i> Total
                                @elseif($dato == 'mujer') <i class="fas fa-female"></i> Mujeres
                                @else <i class="fas fa-male"></i> Hombres
                                @endif
                            </td>
                            @foreach($anios as $anio)
                                @php
                                    $registro = $stats->firstWhere('anio', $anio);
                                @endphp
                                <td class="py-2 px-4 text-center whitespace-nowrap text-base sm:text-lg font-semibold
                                    @if($dato == 'total') text-primary-700
                                    @elseif($dato == 'mujer') text-primary-600
                                    @elseif($dato == 'hombre') text-secondary-700
                                    @endif">
                                    {{ $registro ? $registro[$dato] : '-' }}
                                </td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- Selector de tipo de gráfico -->
            <div class="flex flex-wrap gap-4 items-center justify-center mb-2 mt-4">
                <label class="inline-flex items-center">
                    <input type="radio" name="tipo-grafico-{{ $categoria }}" value="line" class="form-radio accent-primary" checked onchange="mostrarGrafico('{{ $categoria }}', 'line')">
                    <span class="ml-2">Lineal</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="radio" name="tipo-grafico-{{ $categoria }}" value="bar" class="form-radio accent-primary" onchange="mostrarGrafico('{{ $categoria }}', 'bar')">
                    <span class="ml-2">Barras</span>
                </label>
            </div>
            <!-- Gráficos -->
            <div class="w-full flex flex-col items-center">
                <canvas id="chart-line-{{ $categoria }}" class="w-full max-h-[280px] md:max-h-[350px]" style="max-width:700px"></canvas>
                <canvas id="chart-bar-{{ $categoria }}" class="w-full max-h-[280px] md:max-h-[350px]" style="max-width:400px;display:none;"></canvas>
            </div>
        </div>
        @endforeach
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function mostrarGrafico(categoria, tipo) {
    document.getElementById('chart-line-' + categoria).style.display = tipo === 'line' ? '' : 'none';
    document.getElementById('chart-bar-' + categoria).style.display = tipo === 'bar' ? '' : 'none';
}
@foreach($categorias as $categoria => $stats)
    const labels_{{ $categoria }} = @json($anios);
    const total_{{ $categoria }} = @json($stats->pluck('total', 'anio')->toArray());
    const mujer_{{ $categoria }} = @json($stats->pluck('mujer', 'anio')->toArray());
    const hombre_{{ $categoria }} = @json($stats->pluck('hombre', 'anio')->toArray());
    // Gráfico lineal
    new Chart(document.getElementById('chart-line-{{ $categoria }}'), {
        type: 'line',
        data: {
            labels: labels_{{ $categoria }},
            datasets: [
                {
                    label: 'Total',
                    data: labels_{{ $categoria }}.map(a => total_{{ $categoria }}[a] ?? 0),
                    borderColor: '#888888',
                    backgroundColor: 'rgba(136,136,136,0.1)',
                    fill: false
                },
                {
                    label: 'Mujer',
                    data: labels_{{ $categoria }}.map(a => mujer_{{ $categoria }}[a] ?? 0),
                    borderColor: 'rgb(38,186,165)',
                    backgroundColor: 'rgba(38,186,165,0.1)',
                    fill: false
                },
                {
                    label: 'Hombre',
                    data: labels_{{ $categoria }}.map(a => hombre_{{ $categoria }}[a] ?? 0),
                    borderColor: 'rgb(55,95,122)',
                    backgroundColor: 'rgba(55,95,122,0.1)',
                    fill: false
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' },
                title: { display: false }
            }
        }
    });
    // Gráfico de barras: dos barras por año (mujer y hombre)
    new Chart(document.getElementById('chart-bar-{{ $categoria }}'), {
        type: 'bar',
        data: {
            labels: labels_{{ $categoria }},
            datasets: [
                {
                    label: 'Mujer',
                    data: labels_{{ $categoria }}.map(a => mujer_{{ $categoria }}[a] ?? 0),
                    backgroundColor: 'rgb(38,186,165)',
                    borderColor: 'rgb(38,186,165)',
                    borderWidth: 2
                },
                {
                    label: 'Hombre',
                    data: labels_{{ $categoria }}.map(a => hombre_{{ $categoria }}[a] ?? 0),
                    backgroundColor: 'rgb(55,95,122)',
                    borderColor: 'rgb(55,95,122)',
                    borderWidth: 2
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' },
                title: { display: true, text: 'Comparativa Hombres vs Mujeres (Histórico)'}
            }
        }
    });
@endforeach
</script>
        
        


        @php
            // Calcular probabilidad de reprobar para el colegio actual SOLO del último año
            $ultimoAnio = collect($estadisticas)->max('anio');
            $matriculaUltimo = collect($estadisticas)->where('categoria', 'matricula')->where('anio', $ultimoAnio)->first();
            $reprobadosUltimo = collect($estadisticas)->where('categoria', 'reprobados')->where('anio', $ultimoAnio)->first();
        
            $totalMatricula = $matriculaUltimo ? (int)$matriculaUltimo->total : 0;
            $totalReprobados = $reprobadosUltimo ? (int)$reprobadosUltimo->total : 0;
            $probabilidadReprobar = $totalMatricula > 0 ? round(($totalReprobados / $totalMatricula) * 100, 2) : 0;
        @endphp

        <div class="bg-white rounded-xl shadow p-6 mb-8">
            <h2 class="text-xl font-bold text-primary mb-4 flex items-center gap-2">
                <i class="fas fa-exclamation-triangle"></i> Probabilidad de Reprobar 
            </h2>
            <div class="flex items-center gap-4">
                <div class="text-4xl font-extrabold text-secondary">{{ $probabilidadReprobar }}%</div>
                <div class="text-secondary">
                    <span class="font-semibold">Un estudiante tiene una probabilidad de <span class="text-primary">{{ $probabilidadReprobar }}%</span> de reprobar si ingresa a este colegio, según los datos históricos.</span>
                </div>
            </div>
        </div>

        @php
            $direccion="https://maps.google.com/?q=".$school->ubicacion->latitud.",".$school->ubicacion->longitud."&z=12&t=k";
            
        @endphp

        <!-- Enlace a ficha PDF si existe -->
        @if($school->url_ficha)
            <div class="text-center mt-6">
                <a target="_blank" href="{{$direccion}}" target="_blank"
                   class="inline-flex items-center gap-2 px-6 py-3 rounded-lg bg-primary text-white font-bold shadow hover:bg-secondary transition">
                    <i class="fa-solid fa-location-dot fa-beat"></i> Ver Google Maps
                </a>
            </div>
        @endif

        <section class="cta-section">
            <div class="container">
                <div class="cta-content">
                    <h2>Ubición del colegio</h2>
                    <div id="map"></div>
                </div>
            </div>
        </section>

        
        

        {{-- %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%  mas estadisticas  %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% --}}
            
        @php
            // Obtener el último año de estadística
            $ultimoAnio = collect($estadisticas)->max('anio');
            $matricula = collect($estadisticas)->where('categoria', 'matricula')->where('anio', $ultimoAnio)->first();
            $reprobados = collect($estadisticas)->where('categoria', 'reprobados')->where('anio', $ultimoAnio)->first();
            $abandono = collect($estadisticas)->where('categoria', 'abandono')->where('anio', $ultimoAnio)->first();

            $totalEstudiantes = $matricula ? (int)$matricula->total : 0;
            $totalReprobados = $reprobados ? (int)$reprobados->total : 0;
            $totalAbandono = $abandono ? (int)$abandono->total : 0;

            // Calcular porcentajes
            $porcentajeReprobados = $totalEstudiantes > 0 ? round(($totalReprobados / $totalEstudiantes) * 100, 2) : 0;
            $porcentajeAbandono = $totalEstudiantes > 0 ? round(($totalAbandono / $totalEstudiantes) * 100, 2) : 0;
            $porcentajeAprobados = $totalEstudiantes > 0 ? round((($totalEstudiantes - $totalReprobados - $totalAbandono) / $totalEstudiantes) * 100, 2) : 0;
        @endphp

        <div class="bg-white rounded-xl shadow p-6 mb-8 flex flex-col items-center max-w-2xl mx-auto">
            <h2 class="text-xl font-bold text-primary mb-4 flex items-center gap-2">
                <i class="fas fa-chart-pie"></i> Estadística del último año ({{ $ultimoAnio }})
            </h2>
            <div class="flex flex-col md:flex-row items-center gap-8 w-full justify-center">
                <div class="w-full md:w-1/2 flex justify-center">
                    <div class="w-full" style="max-width:320px;min-width:160px;">
                        <canvas id="pieEstadistica" class="aspect-square !h-auto" style="width:100%;aspect-ratio:1/1;min-height:160px;max-height:220px;@media(min-width:768px){max-height:350px;}"></canvas>
                    </div>
                </div>
                <div class="w-full md:w-1/2 flex justify-center">
                    <ul class="space-y-1 text-secondary text-xs md:text-base">
                        <li>
                            <span class="inline-block w-2 h-2 md:w-3 md:h-3 rounded-full mr-1 md:mr-2 align-middle" style="background: #26baa5"></span>
                            <strong class="font-normal md:font-semibold">Aprobados:</strong> {{ $totalEstudiantes - $totalReprobados - $totalAbandono }} ({{ $porcentajeAprobados }}%)
                        </li>
                        <li>
                            <span class="inline-block w-2 h-2 md:w-3 md:h-3 rounded-full mr-1 md:mr-2 align-middle" style="background: rgb(55,95,122)"></span>
                            <strong class="font-normal md:font-semibold">Reprobados:</strong> {{ $totalReprobados }} ({{ $porcentajeReprobados }}%)
                        </li>
                        <li>
                            <span class="inline-block w-2 h-2 md:w-3 md:h-3 rounded-full mr-1 md:mr-2 align-middle" style="background: #cccccc"></span>
                            <strong class="font-normal md:font-semibold">Abandono:</strong> {{ $totalAbandono }} ({{ $porcentajeAbandono }}%)
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%  footer %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% --}}
        @php
        // Datos del último año para reprobados por género
        $ultimoAnio = collect($estadisticas)->max('anio');
        $reprobados = collect($estadisticas)->where('categoria', 'reprobados')->where('anio', $ultimoAnio)->first();

        $reprobadosMujer = $reprobados ? (int)$reprobados->mujer : 0;
        $reprobadosHombre = $reprobados ? (int)$reprobados->hombre : 0;
        $totalReprobados = $reprobadosMujer + $reprobadosHombre;

        $porcentajeMujer = $totalReprobados > 0 ? round(($reprobadosMujer / $totalReprobados) * 100, 2) : 0;
        $porcentajeHombre = $totalReprobados > 0 ? round(($reprobadosHombre / $totalReprobados) * 100, 2) : 0;
    @endphp

    <div class="bg-white rounded-xl shadow p-6 mb-8 flex flex-col items-center max-w-2xl mx-auto">
        <h2 class="text-xl font-bold text-primary mb-4 flex items-center gap-2">
            <i class="fas fa-chart-pie"></i> Reprobados por género ({{ $ultimoAnio }})
        </h2>
        <div class="flex flex-col md:flex-row items-center gap-8 w-full justify-center">
            <div class="w-full md:w-1/2 flex justify-center">
                <div class="w-full" style="max-width:320px;min-width:160px;">
                    <canvas id="pieReprobadosGenero" class="aspect-square !h-auto" style="width:100%;aspect-ratio:1/1;min-height:160px;max-height:220px;@media(min-width:768px){max-height:350px;}"></canvas>
                </div>
            </div>
            <div class="w-full md:w-1/2 flex justify-center">
                <ul class="space-y-1 text-secondary text-xs md:text-base">
                    <li>
                        <span class="inline-block w-2 h-2 md:w-3 md:h-3 rounded-full mr-1 md:mr-2 align-middle" style="background: rgb(38,186,165)"></span>
                        <strong class="font-normal md:font-semibold">Mujeres:</strong> {{ $reprobadosMujer }} ({{ $porcentajeMujer }}%)
                    </li>
                    <li>
                        <span class="inline-block w-2 h-2 md:w-3 md:h-3 rounded-full mr-1 md:mr-2 align-middle" style="background: rgb(55,95,122)"></span>
                        <strong class="font-normal md:font-semibold">Hombres:</strong> {{ $reprobadosHombre }} ({{ $porcentajeHombre }}%)
                    </li>
                </ul>
            </div>
        </div>
    </div>

        {{-- %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%  footer %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% --}}
        @php
        // Evolución de reprobados por año y estimación
        $reprobadosPorAnio = collect($estadisticas)
            ->where('categoria', 'reprobados')
            ->sortBy('anio')
            ->groupBy('anio')
            ->map(function($items) {
                return (int)$items->first()->total;
            });

        $aniosReales = $reprobadosPorAnio->keys()->toArray();
        $valoresReales = $reprobadosPorAnio->values()->toArray();

        // Estimación simple: promedio de los últimos 2 años
        $ultimoAnio = max($aniosReales);
        $penultimoAnio = count($aniosReales) > 1 ? $aniosReales[count($aniosReales)-2] : $ultimoAnio;
        $promedio = count($valoresReales) > 1 ? round((end($valoresReales) + prev($valoresReales))/2) : end($valoresReales);

        $aniosEstimados = [$ultimoAnio+1, $ultimoAnio+2];
        $valoresEstimados = [$promedio, $promedio];

        $aniosGrafico = array_merge($aniosReales, $aniosEstimados);
        $valoresGrafico = array_merge($valoresReales, $valoresEstimados);

        // Comparación de aprobados vs promovidos por año
        $aprobadosPorAnio = [];
        $promovidosPorAnio = [];
        foreach($aniosReales as $anio) {
            $matricula = collect($estadisticas)->where('categoria', 'matricula')->where('anio', $anio)->first();
            $reprobados = collect($estadisticas)->where('categoria', 'reprobados')->where('anio', $anio)->first();
            $abandono = collect($estadisticas)->where('categoria', 'abandono')->where('anio', $anio)->first();
            $aprobadosPorAnio[] = $matricula ? ((int)$matricula->total - (int)($reprobados->total ?? 0) - (int)($abandono->total ?? 0)) : 0;
            $promovidos = collect($estadisticas)->where('categoria', 'promovidos')->where('anio', $anio)->first();
            $promovidosPorAnio[] = $promovidos ? (int)$promovidos->total : 0;
        }

        // Aprobados por género (último año)
        $matriculaUltimo = collect($estadisticas)->where('categoria', 'matricula')->where('anio', $ultimoAnio)->first();
        $reprobadosUltimo = collect($estadisticas)->where('categoria', 'reprobados')->where('anio', $ultimoAnio)->first();
        $abandonoUltimo = collect($estadisticas)->where('categoria', 'abandono')->where('anio', $ultimoAnio)->first();

        $aprobadosMujer = $matriculaUltimo ? ((int)$matriculaUltimo->mujer - (int)($reprobadosUltimo->mujer ?? 0) - (int)($abandonoUltimo->mujer ?? 0)) : 0;
        $aprobadosHombre = $matriculaUltimo ? ((int)$matriculaUltimo->hombre - (int)($reprobadosUltimo->hombre ?? 0) - (int)($abandonoUltimo->hombre ?? 0)) : 0;
    @endphp

    <div class="bg-white rounded-xl shadow p-6 mb-8 flex flex-col items-center max-w-2xl mx-auto">
        <h2 class="text-xl font-bold text-primary mb-4 flex items-center gap-2">
            <i class="fas fa-chart-line"></i> Evolución de reprobados por año
        </h2>
        <div style="max-width:480px;width:100%">
            <canvas id="evolucionReprobados" class="aspect-square !h-auto" style="width:100%;aspect-ratio:1.5/1;min-height:160px;max-height:220px;@media(min-width:768px){max-height:350px;}"></canvas>
        </div>
        <div class="text-xs text-secondary mt-2 flex gap-4 justify-center">
            <span class="inline-block w-3 h-3 rounded-full mr-1 align-middle" style="background:rgb(55,95,122)"></span> Datos reales
            <span class="inline-block w-3 h-3 rounded-full mx-2 align-middle" style="background:#cccccc"></span> Estimaciones
        </div>
    </div>

    <div class="bg-white rounded-xl shadow p-6 mb-8 flex flex-col items-center max-w-2xl mx-auto">
        <h2 class="text-xl font-bold text-primary mb-4 flex items-center gap-2">
            <i class="fas fa-chart-bar"></i> Comparación de aprobados vs promovidos por año
        </h2>
        <div style="max-width:480px;width:100%">
            <canvas id="aprobadosPromovidos" class="aspect-square !h-auto" style="width:100%;aspect-ratio:1.5/1;min-height:160px;max-height:220px;@media(min-width:768px){max-height:350px;}"></canvas>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow p-6 mb-8 flex flex-col items-center max-w-2xl mx-auto">
        <h2 class="text-xl font-bold text-primary mb-4 flex items-center gap-2">
            <i class="fas fa-venus-mars"></i> Aprobados por género ({{ $ultimoAnio }})
        </h2>
        </h2>
        <div class="w-full" style="max-width:320px;min-width:160px;">
            <canvas id="aprobadosGenero" class="aspect-square !h-auto" style="width:100%;aspect-ratio:1/1;min-height:160px;max-height:220px;@media(min-width:768px){max-height:350px;}"></canvas>
        </div>
        <div class="flex gap-2 md:gap-8 mt-4 text-secondary text-xs md:text-lg justify-center">
            <span><span class="inline-block w-2 h-2 md:w-3 md:h-3 rounded-full mr-1 md:mr-2 align-middle" style="background: rgb(38,186,165)"></span> Mujeres: {{ $aprobadosMujer }}</span>
            <span><span class="inline-block w-2 h-2 md:w-3 md:h-3 rounded-full mr-1 md:mr-2 align-middle" style="background: rgb(55,95,122)"></span> Hombres: {{ $aprobadosHombre }}</span>
        </div>
    </div>
        {{-- %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%  contactar %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% --}}
          
        {{-- %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%  contactar %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% --}}
        

        <!-- Contacto Section -->
        <section id="contacto" class="contacto-section">
            <div class="container">
                <div class="contacto-grid">
                    <div class="contacto-info">
                        <div class="section-header">
                            <h2>Contáctanos</h2>
                            <p>¿Tienes preguntas? Estamos aquí para ayudarte</p>
                        </div>
                        
                        <div class="info-items">
                            <div class="info-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <div>
                                    <h4>Dirección</h4>
                                    <p>Villa 1 de mayo, calle 16 oeste #9</p>
                                </div>
                            </div>
                            
                            <div class="info-item">
                                <i class="fas fa-phone"></i>
                                <div>
                                    <h4>Teléfono</h4>
                                    <p>+59160902299</p>
                                </div>
                            </div>
                            
                            <div class="info-item">
                                <i class="fas fa-envelope"></i>
                                <div>
                                    <h4>Email</h4>
                                    <p>colegios@ite.com.bo</p>
                                </div>
                            </div>
                            
                            <div class="info-item">
                                <i class="fas fa-clock"></i>
                                <div>
                                    <h4>Horario</h4>
                                    <p>Lunes a Sábado: 7:30 am - 06:30pm</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="contacto-form">
                        <form onsubmit="enviarWhatsApp(event)" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="nombre">Nombre</label>
                                <input type="text" id="nombre" name="nombre" required>
                            </div>
                            <div class="form-group">
                                <label for="telefono">Teléfono</label>
                                <input type="tel" id="telefono" name="telefono" required>
                            </div>
                            <div class="form-group">
                                <label for="mensaje">Mensaje</label>
                                <textarea id="mensaje" name="mensaje" rows="5" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Enviar a WhatsApp</button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
        {{-- %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%  contactar %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% --}}
    


        <!-- Newsletter Section -->
        <section class="cta-section">
            <div class="container">
                <div class="cta-content">
                    <h2>¿Listo para unirte a nuestro equipo innovador?</h2>
                    <p class="cta-subtitle">
                        Buscamos colaboradores apasionados por la educación tecnológica. 
                        ¡Tu experiencia es valiosa para nosotros!
                    </p>
                    
                    <div class="cta-actions">
                        <a href="https://wa.me/59160902299?text=¡Hola!%20Quiero%20ser%20parte%20de%20este%20proyecto.%20Puedo%20colaborar%20en:" 
                        class="cta-button whatsapp-btn" 
                        target="_blank" 
                        rel="noopener noreferrer">
                            <i class="fab fa-whatsapp"></i> ÚNETE AL EQUIPO
                        </a>
                    </div>
                    
                    <p class="cta-footer">
                        <i class="fas fa-lightbulb"></i> ¡Juntos crearemos el futuro de la educación tecnológica!
                    </p>
                </div>
            </div>
        </section>
    {{-- %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%  footer %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% --}}
  

        {{-- %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%  redes  %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% --}}
            <section class="social-banner">
                <div class="container">
                    <div class="social-content">
                        <h2>¡Conéctate con nuestra comunidad educativa!</h2>
                        <p class="subtitle">Contenido exclusivo, tips de estudio y novedades tecnológicas</p>
                        
                        <div class="social-links">
                            <a href="https://www.tiktok.com/@ite_educabol" target="_blank" class="social-link tiktok">
                                <div class="social-icon">
                                    <i class="fab fa-tiktok"></i>
                                </div>
                                <span>TikTok</span>
                            </a>
                            
                            <a href="https://www.facebook.com/ite.educabol" target="_blank" class="social-link facebook">
                                <div class="social-icon">
                                    <i class="fab fa-facebook-f"></i>
                                </div>
                                <span>Facebook</span>
                            </a>
                            
                            <a href="https://www.youtube.com/@ite_educabol" target="_blank" class="social-link youtube">
                                <div class="social-icon">
                                    <i class="fab fa-youtube"></i>
                                </div>
                                <span>YouTube</span>
                            </a>
                            
                            <a href="https://whatsapp.com/channel/0029VaAu3lwJJhzX5iSJBg44" target="_blank" class="social-link whatsapp">
                                <div class="social-icon">
                                    <i class="fab fa-whatsapp"></i>
                                </div>
                                <span>WhatsApp</span>
                            </a>
                            
                            <a href="#" target="_blank" class="social-link instagram">
                                <div class="social-icon">
                                    <i class="fab fa-instagram"></i>
                                </div>
                                <span>Instagram</span>
                            </a>
                            <a href="https://ite.com.bo" target="_blank" class="social-link website">
                                <div class="social-icon">
                                    <i class="fas fa-globe"></i>
                                </div>
                                <span>Sitio Web</span>
                            </a>
                            
                        </div>
                    </div>
                </div>
            </section>

    

    {{-- %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%  footer %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% --}}
        <!-- Footer -->
        <footer class="main-footer">
            <div class="container">
                <div class="footer-grid">
                    <div class="footer-about">
                        <h3>Qué es ite?</h3>
                        <p>Somos una institución educativa dedicada a proporcionar recursos de calidad para estudiantes de todos los niveles.</p>
                        <div class="footer-social">
                            <a target="_blank" href="https://www.tiktok.com/@ite_educabol" class="social-icon"><i class="fab fa-tiktok"></i></a>
                            <a target="_blank" href="https://www.facebook.com/ite.educabol" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                            <a target="_blank" href="https://www.youtube.com/@ite_educabol" class="social-icon"><i class="fab fa-youtube"></i></a>
                            <a target="_blank" href="https://wa.me/59160902299" class="social-icon"><i class="fab fa-whatsapp"></i></a>
                            <a target="_blank" href="https://www.instagram.com/tu_usuario" class="social-icon"><i class="fab fa-instagram"></i></a>
                            <a target="_blank" href="https://ite.com.bo" class="social-icon"><i class="fas fa-globe"></i></a>
                        </div>
                    </div>
                    
                    <div class="footer-links">
                        <h4>Enlaces rápidos</h4>
                        <ul>
                            <li><a target="_blank" href="https://ite.com.bo">Qué es ite?</a></li>
                            <li><a target="_blank" href="https://colegios.ite.com.bo">Fórmulas</a></li>
                            <li><a target="_blank" href="https://services.ite.com.bo">Cursos</a></li>
                        </ul>
                    </div>
                    
                    <div class="footer-links">
                        <h4>Materias</h4>
                        <ul class="materias-list">
                            <li><a href="#" class="whatsapp-link" data-msg="Computación">Computación</a></li>
                            <li><a href="#" class="whatsapp-link" data-msg="Robótica">Robótica</a></li>
                            <li><a href="#" class="whatsapp-link" data-msg="Cubo Rubik">Cubo Rubik</a></li>
                            <li><a href="#" class="whatsapp-link" data-msg="Programación">Programación</a></li>
                            <li><a href="#" class="whatsapp-link" data-msg="Apoyo escolar">Apoyo escolar</a></li>
                        </ul>
                    </div>
                    
                    <div class="footer-links">
                        <h4>Cursos</h4>
                        <ul class="materias-list">
                            <li><a href="#" class="whatsapp-link" data-msg="Matematicas">Matemáticas</a></li>
                            <li><a href="#" class="whatsapp-link" data-msg="Fisica">Física</a></li>
                            <li><a href="#" class="whatsapp-link" data-msg="Quimica">Química</a></li>
                            <li><a href="#" class="whatsapp-link" data-msg="Programacion">Programación</a></li>
                            <li><a href="#" class="whatsapp-link" data-msg="lenguaje">Escritura y Lectura</a></li>
                        </ul>
                    </div>
                    
                </div>
                
                <div class="footer-bottom">
                    <p>&copy; 2026 ITE. Todos los derechos reservados.</p>
                    <div class="footer-legal">
                        <a href="https://www.tiktok.com/@davidflores.ite" target="_blank">David Flores</a>
                        <a href="https://www.ite.com.bo" target="_blank">ite educabol</a>
                    </div>
                </div>
            </div>
        </footer>
        
    </div>
    
    
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script>
            // Convertir el objeto PHP completo a JSON
            const schoolData = <?php echo json_encode($school); ?>;
            // Acceder a los valores
            const latitudx = parseFloat(schoolData.ubicacion.latitud);
            const longitudx = parseFloat(schoolData.ubicacion.longitud);
            
            
           document.addEventListener('DOMContentLoaded', function() {
            // 1. COORDENADAS DEL COLEGIO (las que proporcionaste)
            var colegioCoords = [latitudx, longitudx];

            // 2. CAPAS BASE
            var capaOSM = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 18,
                attribution: 'OpenStreetMap | ite.com.bo'
            });
            var capaSat = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                maxZoom: 18,
                attribution: 'Esri World Imagery'
            });
            var capaTopo = L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {
                maxZoom: 17,
                attribution: 'OpenTopoMap'
            });

            // 3. CONFIGURACIÓN DEL MAPA
            var map = L.map('map', {
                center: [latitudx, longitudx],
                zoom: 14,
                layers: [capaOSM]
            });

            // 4. CONTROL DE CAPAS
            var baseMaps = {
                "Callejero": capaOSM,
                "Satélite": capaSat,
                "Topográfico": capaTopo
            };
            L.control.layers(baseMaps).addTo(map);

            // 5. MARCADOR DEL COLEGIO
            var colegioMarker = L.circleMarker(colegioCoords, {
                radius: 12,
                color: '#e74c3c',
                fillColor: '#e74c3c',
                fillOpacity: 0.4,
                weight: 2
            }).addTo(map);

            // 6. VENTANA EMERGENTE (POPUP) TRANSPARENTE
            colegioMarker.bindPopup(`
                <div class="transparent-popup">
                    <h4><b>Colegio:</b>${schoolData.nombre}</h4>
                    <b>Departamento:</b> ${schoolData.ubicacion.departamento} <br>
                    <b>Provincia:</b> ${schoolData.ubicacion.provincia} <br>
                    <b>Municipio:</b> ${schoolData.ubicacion.municipio} <br>
                    <b>Distrito:</b> ${schoolData.ubicacion.distrito} <br>
                    <b>Coordenadas:</b><br>
                    <b>Latitud:</b> ${colegioCoords[0].toFixed(6)}<br>
                    <b>Longitud:</b> ${colegioCoords[1].toFixed(6)}<br>
                    <b>RUE:</b> ${schoolData.codigo_rue}<br>
                    <b>Nivel:</b> ${schoolData.niveles}<br>
                </div>
            `, {
                className: 'transparent-popup'
            }).openPopup();

            // 7. CONTROL DE ESCALA
            L.control.scale({
                position: 'bottomleft',
                metric: true,
                imperial: false
            }).addTo(map);

            // 8. CÍRCULO DE VISIBILIDAD
            L.circle(colegioCoords, {
                radius: 100,
                color: '#3498db',
                fillColor: '#3498db',
                fillOpacity: 0.2
            }).addTo(map);
        });
    </script>

    <!-- JS de jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!-- JS de Slick -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js"></script>
    
    <script>

        document.querySelectorAll('.whatsapp-link').forEach(link => {
            const materia = link.getAttribute('data-msg');
            console.log("materia",materia);
            let mensaje = '';
            
            // Mensajes personalizados para cada materia
            switch(materia) {
                case 'Computación':
                    mensaje = '¡Hola! Estoy interesado/a en el curso de *COMPUTACIÓN* Mensaje enviado desde https://colegios.ite.com.bo';
                    break;
                case 'Robótica':
                    mensaje = '¡Buenos días! Quisiera información sobre el curso de *ROBÓTICA.* Mensaje enviado desde https://colegios.ite.com.bo';
                    break;
                case 'Cubo Rubik':
                    mensaje = '¡Saludos! Me interesa el curso de *CUBO RUBIK.* Mensaje enviado desde https://colegios.ite.com.bo';
                    break;
                case 'Programación':
                    mensaje = '¡Hola! Busco información sobre el curso de *PROGRAMACIÓN.* Mensaje enviado desde https://colegios.ite.com.bo';
                    break;
                case 'Apoyo escolar':
                    mensaje = '¡Buenas tardes! Necesito *APOYO ESCOLAR.* Mensaje enviado desde https://colegios.ite.com.bo';
                    break;
                case 'Matematicas':
                    mensaje = '¡Hola! Estoy interesado/a en clases de *MATEMÁTICAS.* Mensaje enviado desde https://colegios.ite.com.bo';
                    break;
                case 'Fisica':
                    mensaje = '¡Buenos días! Necesito clases de *FÍSICA.* Mensaje enviado desde https://colegios.ite.com.bo';
                    break;
                case 'Quimica':
                    mensaje = '¡Saludos! Busco clases de *QUÍMICA.* Mensaje enviado desde https://colegios.ite.com.bo';
                    break;
                case 'Programacion':
                    mensaje = '¡Hola! Quiero aprender *PROGRAMACIÓN.* Mensaje enviado desde https://colegios.ite.com.bo';
                    break;
                case 'lenguaje':
                    mensaje = '¡Buenas tardes! Me interesa el curso de *Escritura y Lectura.* Mensaje enviado desde https://colegios.ite.com.bo';
                    break;
               
            }
            console.log("mensaje",mensaje);
            link.href = `https://wa.me/59171324941?text=${encodeURIComponent(mensaje)}`;
            link.target = '_blank';
        });

        
        // document.addEventListener('DOMContentLoaded', () => {
        //     const whatsappLinks = document.querySelectorAll('.whatsapp-link');
        //     const numeroWhatsApp = '59160902299'; // Número destino (sin +)
          
        //     whatsappLinks.forEach(link => {
        //         link.addEventListener('click', (e) => {
        //             e.preventDefault();
        //             const mensaje = encodeURIComponent(link.dataset.msg);
        //             window.open(`https://wa.me/${numeroWhatsApp}?text=${mensaje}`, '_blank');
        //         });
        //     });
        // });
        
        function enviarWhatsApp(event) {
            event.preventDefault();
            
            const nombre = document.getElementById('nombre').value;
            const telefono = document.getElementById('telefono').value;
            const mensaje = document.getElementById('mensaje').value;
            
            // Formatea el mensaje para URL
            const texto = `*Nombre:* ${nombre}%0A*Teléfono:* ${telefono}%0A*Mensaje:* ${mensaje} Mensaje enviado desde https://colegios.ite.com.bo`;
            
            // Redirige a WhatsApp (cambia el número al destino)
            window.open(`https://wa.me/59160902299?text=${texto}`, '_blank');
        }

        $(document).ready(function(){
            // Menú móvil
            $('.menu-toggle').click(function() {
                $('.nav-links').toggleClass('active');
            });
            
            // Slider de profesores
            $('.slider').slick({
                slidesToShow: 3,
                slidesToScroll: 1,
                autoplay: true,
                autoplaySpeed: 3000,
                arrows: true,
                dots: true,
                responsive: [
                    {
                        breakpoint: 992,
                        settings: {
                            slidesToShow: 2
                        }
                    },
                    {
                        breakpoint: 768,
                        settings: {
                            slidesToShow: 1
                        }
                    }
                ]
            });
            
            // Slider de testimonios
            $('.testimonios-slider').slick({
                slidesToShow: 1,
                slidesToScroll: 1,
                autoplay: true,
                autoplaySpeed: 4000,
                arrows: true,
                dots: true
            });
        });
    </script>

    {{-- ...existing code... --}}



<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
<script>
    const ctxPie = document.getElementById('pieEstadistica').getContext('2d');
    new Chart(ctxPie, {
        type: 'pie',
        data: {
            labels: [
                'Aprobados',
                'Reprobados',
                'Abandono'
            ],
            datasets: [{
                data: [
                    {{ $totalEstudiantes - $totalReprobados - $totalAbandono }},
                    {{ $totalReprobados }},
                    {{ $totalAbandono }}
                ],
                backgroundColor: [
                    '#26baa5', // Aprobados (igual que mujeres)
                    'rgb(55,95,122)', // Reprobados (igual que varones)
                    '#cccccc'  // Abandono (gris claro)
                ],
                borderColor: [
                    '#FFFFFFFF',
                    '#FFFFFFFF',
                    '#FFFFFFFF',
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        font: { size: 16 }
                    }
                },
                datalabels: {
                    color: '#fff',
                    font: { weight: 'bold', size: 20 },
                    align: 'center',
                    anchor: 'center',
                    textAlign: 'center',
                    textStrokeColor: '#222',
                    textStrokeWidth: 4,
                    shadowColor: 'rgba(0,0,0,0.0)',
                    shadowBlur: 8,
                    shadowOffsetX: 0,
                    shadowOffsetY: 0,
                    formatter: function(value, context) {
                        let label = context.chart.data.labels[context.dataIndex];
                        let total = context.dataset.data.reduce((a, b) => a + b, 0);
                        let percent = total > 0 ? ((value / total) * 100).toFixed(2) : 0;
                        return label + '\n' + percent + '%';
                    }
                },
                tooltip: {
                    enabled: true,
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            let value = context.raw || 0;
                            let total = context.dataset.data.reduce((a, b) => a + b, 0);
                            let percent = total > 0 ? ((value / total) * 100).toFixed(2) : 0;
                            return `${label}: ${value} (${percent}%)`;
                        }
                    }
                }
            }
        },
        plugins: [ChartDataLabels]
    });
</script>
{{-- ...existing code... --}}
{{-- ...existing code... --}}


<script>
    const ctxPieReprobadosGenero = document.getElementById('pieReprobadosGenero').getContext('2d');
    new Chart(ctxPieReprobadosGenero, {
        type: 'pie',
        data: {
            labels: [
                'Mujer',
                'Hombre'
            ],
            datasets: [{
                data: [
                    {{ $reprobadosMujer }},
                    {{ $reprobadosHombre }}
                ],
                backgroundColor: [
                    '#26baa5', // Mujer (verde corporativo)
                    '#375f7a'  // Hombre (azul corporativo)
                ],
                borderColor: [
                    '#e8eef2',
                    '#e8eef2'
                ],
                borderWidth: 3
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        font: { size: 16 }
                    }
                },
                datalabels: {
                    color: '#fff',
                    font: { weight: 'bold', size: 20 },
                    align: 'center',
                    anchor: 'center',
                    textAlign: 'center',
                    textStrokeColor: '#222',
                    textStrokeWidth: 4,
                    shadowColor: 'rgba(0,0,0,0.2)',
                    shadowBlur: 8,
                    shadowOffsetX: 0,
                    shadowOffsetY: 0,
                    formatter: function(value, context) {
                        let label = context.chart.data.labels[context.dataIndex];
                        let total = context.dataset.data.reduce((a, b) => a + b, 0);
                        let percent = total > 0 ? ((value / total) * 100).toFixed(2) : 0;
                        // Dos líneas: palabra y porcentaje
                        return label + '\n' + percent + '%';
                    }
                },
                tooltip: {
                    enabled: true,
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            let value = context.raw || 0;
                            let total = context.dataset.data.reduce((a, b) => a + b, 0);
                            let percent = total > 0 ? ((value / total) * 100).toFixed(2) : 0;
                            return `${label}: ${value} (${percent}%)`;
                        }
                    }
                }
            }
        },
        plugins: [ChartDataLabels]
    });
</script>


<script>
    // Evolución de reprobados por año
    const ctxEvolucion = document.getElementById('evolucionReprobados').getContext('2d');
    new Chart(ctxEvolucion, {
        type: 'bar',
        data: {
            labels: {!! json_encode($aniosGrafico) !!},
            datasets: [
                {
                    label: 'Reprobados (reales)',
                    data: {!! json_encode(array_merge($valoresReales, array_fill(0, count($aniosEstimados), null))) !!},
                    backgroundColor: 'rgb(55,95,122)',
                    borderColor: 'rgb(55,95,122)',
                    borderWidth: 2
                },
                {
                    label: 'Reprobados (estimado)',
                    data: {!! json_encode(array_merge(array_fill(0, count($aniosReales), null), $valoresEstimados)) !!},
                    backgroundColor: '#cccccc',
                    borderColor: '#cccccc',
                    borderWidth: 2
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            let value = context.raw || 0;
                            return `${label}: ${value}`;
                        }
                    }
                }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // Comparación de aprobados vs reprobados por año
    const ctxAprobadosReprobados = document.getElementById('aprobadosPromovidos').getContext('2d');
    new Chart(ctxAprobadosReprobados, {
        type: 'bar',
        data: {
            labels: {!! json_encode($aniosReales) !!},
            datasets: [
                {
                    label: 'Aprobados',
                    data: {!! json_encode($aprobadosPorAnio) !!},
                    backgroundColor: '#26baa5',
                    borderColor: '#26baa5',
                    borderWidth: 2
                },
                {
                    label: 'Reprobados',
                    data: {!! json_encode($valoresReales) !!},
                    backgroundColor: 'rgb(55,95,122)',
                    borderColor: 'rgb(55,95,122)',
                    borderWidth: 2
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // Aprobados por género (último año)
    const ctxAprobadosGenero = document.getElementById('aprobadosGenero').getContext('2d');
    new Chart(ctxAprobadosGenero, {
        type: 'pie',
        data: {
            labels: ['Mujer', 'Hombre'],
            datasets: [{
                data: [{{ $aprobadosMujer }}, {{ $aprobadosHombre }}],
                backgroundColor: ['rgb(38,186,165)', 'rgb(55,95,122)'],
                borderColor: ['#FFFFFF', '#FFFFFF'],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
</script>