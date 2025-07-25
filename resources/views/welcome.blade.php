<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('images/ite.ico') }}" type="image/x-icon">
    <title>Colegios en Bolivia | Mapa Educativo con Geolocalización</title>
    <meta name="description" content="Mapa interactivo de colegios en Bolivia con datos de ubicación, distritos y códigos RUE. Encuentra escuelas por departamento.">
    <meta name="keywords" content="colegios Bolivia, mapa educativo, geolocalización escuelas, RUE Bolivia">
    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}

    <!-- Tailwind CDN para pruebas rápidas, para producción usa instalación local -->
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: 'rgb(38,186,165)',
                        secondary: 'rgb(55,95,122)',
                    }
                }
            }
        }
    </script>
    <!-- Font Awesome para iconos -->
    <script src="https://kit.fontawesome.com/your-code.js" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="{{ asset('css/vistas/welcome/menu.css') }}">
    <link rel="icon" href="{{ asset('image/ite.ico') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('css/vistas/welcome/mapa.css') }}">
    <link rel="stylesheet" href="{{ asset('css/vistas/welcome/redes.css') }}">
    <link rel="stylesheet" href="{{ asset('css/vistas/welcome/servicios.css') }}">
    <link rel="stylesheet" href="{{ asset('css/vistas/welcome/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/vistas/welcome/unete.css') }}">
    <!-- Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- CSS de Slick -->
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.css"/>
    <!-- CSS de Slick Theme -->
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick-theme.min.css"/>


</head>
<body class="bg-primary/10 min-h-screen font-sans">

    {{-- %%%%%%%%%%%%%%%%%%%%%  menu %%%%%%%%%%%%%%%%%%%%%%% --}}
    <nav>
        <button class="menu-toggle" onclick="toggleMenu()">☰ Menú</button>
        <ul class="menu" id="menu">
            <li><a href="#">Inicio</a></li>
            <li>
                <a href="#">Matrícula ▾</a>
                <ul class="submenu">
                    <li><a href="#">Top 10 más poblados</a></li>
                    <li><a href="#">Top 10 menos poblados</a></li>
                    <li><a href="#">Crecimiento anual</a></li>
                </ul>
            </li>
            <li>
                <a href="#">Reprobados ▾</a>
                <ul class="submenu">
                    <li><a href="#">Reprobados por departamento</a></li>
                    <li><a href="#">Reprobados Bolivia</a></li>
                    <li><a href="#">Con mayor tasa Reprobación</a></li>
                    <li><a href="#">Con Menor Tasa de Reprobación</a></li>
                    <li><a href="#">Reprobación Urbana vs Rural</a></li>
                    <li><a href="#">Reprobación por Género</a></li>
                    <li><a href="#">Colegios con Reprobación Cero</a></li>
                    <li><a href="#">Reprobación creciente</a></li>
                    <li><a href="#">Promedio Nacional de Reprobación</a></li>
                    <li><a href="#">Departamentos con Mayor Promedio de Reprobación</a></li>
                    <li><a href="#">Índice de Riesgo por Reprobación</a></li>
                    <li><a href="#">Distribución Geográfica de Reprobados</a></li>
                    <li><a href="#">Relación Infraestructura vs Reprobación</a></li>
                    <li><a href="#">Proyección de Reprobación (Tendencia)</a></li>
                    <li><a href="#">Alertas de Reprobación Alta</a></li>
                    <li><a href="#">Mapa</a></li>
                </ul>
                <a href="/reprobados">Reprobados</a>
            </li>
            <li>
                <a href="#">Abandono ▾</a>
                <ul class="submenu">
                    <li><a href="#">Departamentos críticos</a></li>
                    <li><a href="#">Comparación urbana vs rural</a></li>
                </ul>
            </li>
            <li><a href="#">Infraestructura</a></li>
            <li><a href="#">Mapa</a></li>
        </ul>
    </nav>
    {{-- %%%%%%%%%%%%%%%%%%%%%  menu %%%%%%%%%%%%%%%%%%%%%%% --}}

    <div class="container mx-auto py-8 px-2">
        <div class="text-center mb-8">
            <h1 class="text-3xl md:text-4xl font-bold text-primary flex items-center justify-center gap-2">
                <i class="fas fa-school"></i> Directorio de Colegios
            </h1>
            <p class="text-secondary text-lg mt-2 flex items-center justify-center gap-2">
                <i class="fas fa-search-location"></i> Encuentra información sobre instituciones educativas
            </p>
        </div>
        <div class="bg-white rounded-xl shadow-lg max-w-2xl mx-auto p-6 mb-8">
            <form method="GET" action="{{ url('/') }}">
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1 relative">
                        <label for="search" class="block text-secondary font-semibold mb-1">
                            <i class="fas fa-search"></i> Buscar colegio
                        </label>
                        <input type="text" id="search" name="search"
                            class="pl-10 pr-4 py-3 rounded-lg border border-primary/30 bg-primary/5 text-secondary w-full focus:ring-2 focus:ring-primary outline-none transition"
                            placeholder="Ingrese nombre, código o departamento..."
                            value="{{ $search ?? '' }}">
                        <span class="absolute left-3 top-10 text-primary pointer-events-none">
                            <i class="fas fa-search"></i>
                        </span>
                    </div>
                    <div class="w-full md:w-48">
                        <label for="filter" class="block text-secondary font-semibold mb-1">
                            <i class="fas fa-filter"></i> Filtrar por
                        </label>
                        <select id="filter" name="filter"
                            class="py-3 px-4 rounded-lg border border-primary/30 bg-primary/5 text-secondary w-full focus:ring-2 focus:ring-primary outline-none transition">
                            <option value="nombre" {{ ($filter ?? 'nombre') == 'nombre' ? 'selected' : '' }}>Nombre</option>
                            <option value="codigo" {{ ($filter ?? '') == 'codigo' ? 'selected' : '' }}>Código RUE</option>
                            <option value="departamento" {{ ($filter ?? '') == 'departamento' ? 'selected' : '' }}>Departamento</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit"
                            class="bg-primary hover:bg-secondary text-white font-bold py-3 px-6 rounded-lg shadow transition flex items-center gap-2">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                    </div>
                </div>
            </form>
        </div>
        @if($schools->count() > 0)
            <div class="bg-white rounded-xl shadow-lg p-0 mb-8 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-primary text-white">
                            <th class="py-3 px-2 font-semibold"><i class="fas fa-barcode"></i> Código</th>
                            <th class="py-3 px-2 font-semibold"><i class="fas fa-school"></i> Nombre</th>
                            <th class="py-3 px-2 font-semibold"><i class="fas fa-map-marker-alt"></i> Ubicación</th>
                            <th class="py-3 px-2 font-semibold"><i class="fas fa-building"></i> Tipo</th>
                            <th class="py-3 px-2 font-semibold"><i class="fas fa-cogs"></i> Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($schools as $item)
                        <tr class="border-b hover:bg-primary/10 transition">
                            <td class="py-2 px-2 font-bold">{{ $item->codigo_rue }}</td>
                            <td class="py-2 px-2">{{ $item->nombre }}</td>
                            <td class="py-2 px-2 text-secondary">
                                <i class="fas fa-map-marker-alt"></i>
                                {{ $item->ubicacion->departamento ?? 'N/A' }},
                                {{ $item->ubicacion->provincia ?? '' }}
                            </td>
                            <td class="py-2 px-2">
                                <span class="inline-block px-3 py-1 rounded-full font-semibold text-white
                                    @if(strtolower($item->dependencia) == 'fiscal') bg-primary
                                    @elseif(strtolower($item->dependencia) == 'privado') bg-secondary
                                    @else bg-gray-500 @endif">
                                    <i class="fas fa-shield-alt"></i> {{ $item->dependencia }}
                                </span>
                            </td>
                            <td class="py-2 px-2">
                                <a href="{{ route('schools.show', $item->id) }}"
                                    class="inline-flex items-center gap-1 px-3 py-1 rounded-lg border border-primary text-primary hover:bg-primary hover:text-white transition font-semibold">
                                    <i class="fas fa-eye"></i> Ver
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="py-4 flex justify-center">
                    {{ $schools->appends(request()->query())->links() }}
                </div>
            </div>
        @else
            <div class="bg-primary/10 text-primary rounded-xl p-6 text-center shadow mb-8">
                @if(!empty($search))
                    <i class="fas fa-exclamation-circle"></i> No se encontraron colegios con <strong>"{{ $search }}"</strong>
                @else
                    <i class="fas fa-info-circle"></i> No hay colegios para mostrar. Intente con otro criterio de búsqueda.
                @endif
            </div>
        @endif

        @include('includes.unete')
        @include('includes.servicios')
        @include('includes.redes')
        @include('includes.escribenos')   
        @include('includes.pie')
        
        {{-- %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%  footer  %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% --}}

    </div>

    
    <script src="{{ asset('js/vistas/welcome/menu.js') }}"></script>
</body>
</html>





