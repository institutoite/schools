<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('images/ite.ico') }}" type="image/x-icon">
    <title>Colegios en Bolivia | Mapa Educativo con Geolocalización</title>
    <meta name="description" content="Mapa interactivo de colegios en Bolivia con datos de ubicación, distritos y códigos RUE. Encuentra escuelas por departamento.">
    <meta name="keywords" content="colegios Bolivia, mapa educativo, geolocalización escuelas, RUE Bolivia">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#e6f8f5',
                            100: '#b3ede3',
                            200: '#80e2d1',
                            300: '#4dd7bf',
                            400: '#26baa5',
                            500: '#1fa48f',
                            600: '#188d78',
                            700: '#127662',
                            800: '#0b5f4c',
                            900: '#044836',
                        },
                        secondary: {
                            50: '#e8eef2',
                            100: '#c3d3e0',
                            200: '#9cb8cd',
                            300: '#759dba',
                            400: '#557ea3',
                            500: '#375f7a',
                            600: '#2d4d63',
                            700: '#233b4c',
                            800: '#192935',
                            900: '#0f171f',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/vistas/welcome/mapa.css') }}">
    <link rel="stylesheet" href="{{ asset('css/vistas/welcome/unete.css') }}">
    <link rel="stylesheet" href="{{ asset('css/vistas/welcome/servicios.css') }}">
    <link rel="stylesheet" href="{{ asset('css/vistas/welcome/redes.css') }}">
    <link rel="stylesheet" href="{{ asset('css/vistas/welcome/style.css') }}">
</head>
<body class="bg-primary-50 text-secondary-800 min-h-screen">

    <!-- Navbar (igual que antes) -->
    <nav class="sticky top-0 z-50 bg-white shadow-sm border-b border-primary-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                    <div class="p-2 bg-primary-500 text-white rounded-lg group-hover:bg-primary-600 transition">
                        <i class="fas fa-school text-lg"></i>
                    </div>
                    <span class="font-bold text-xl text-secondary-700">Colegios Bolivia</span>
                </a>

                <ul class="hidden lg:flex items-center gap-8">
                    <li><a href="{{ url('/') }}" class="font-medium text-secondary-700 hover:text-primary-600 transition">Inicio</a></li>
                    <li class="relative group">
                        <button class="font-medium text-secondary-700 hover:text-primary-600 flex items-center gap-1 transition">
                            Rankings <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        <div class="absolute left-0 mt-4 w-72 bg-white rounded-xl shadow-lg border border-primary-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                            <div class="py-2">
                                @if(Auth::check())
                                    <a href="{{ url('/rankings?tipo=reprobacion') }}" class="block px-5 py-3 hover:bg-primary-50 transition flex items-center gap-2">
                                        <i class="fas fa-chart-line text-red-600"></i> Reprobados
                                    </a>
                                    <a href="{{ url('/rankings?tipo=matricula') }}" class="block px-5 py-3 hover:bg-primary-50 transition flex items-center gap-2">
                                        <i class="fas fa-users text-primary-600"></i> Matrícula
                                    </a>
                                    <a href="{{ url('/rankings?tipo=abandono') }}" class="block px-5 py-3 hover:bg-primary-50 transition flex items-center gap-2">
                                        <i class="fas fa-user-times text-orange-600"></i> Abandono escolar
                                    </a>
                                    <a href="{{ url('/rankings?tipo=infraestructura') }}" class="block px-5 py-3 hover:bg-primary-50 transition flex items-center gap-2">
                                        <i class="fas fa-building text-secondary-600"></i> Infraestructura
                                    </a>
                                @else
                                    <a href="/admin/login" class="block px-5 py-3 hover:bg-primary-50 transition flex items-center gap-2 opacity-60 cursor-not-allowed">
                                        <i class="fas fa-chart-line text-red-600"></i> Reprobados <i class="fas fa-lock text-primary-500 ml-1"></i>
                                    </a>
                                    <a href="/admin/login" class="block px-5 py-3 hover:bg-primary-50 transition flex items-center gap-2 opacity-60 cursor-not-allowed">
                                        <i class="fas fa-users text-primary-600"></i> Matrícula <i class="fas fa-lock text-primary-500 ml-1"></i>
                                    </a>
                                    <a href="/admin/login" class="block px-5 py-3 hover:bg-primary-50 transition flex items-center gap-2 opacity-60 cursor-not-allowed">
                                        <i class="fas fa-user-times text-orange-600"></i> Abandono escolar <i class="fas fa-lock text-primary-500 ml-1"></i>
                                    </a>
                                    <a href="/admin/login" class="block px-5 py-3 hover:bg-primary-50 transition flex items-center gap-2 opacity-60 cursor-not-allowed">
                                        <i class="fas fa-building text-secondary-600"></i> Infraestructura <i class="fas fa-lock text-primary-500 ml-1"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </li>
                    <li class="relative group">
                        <button class="font-medium text-secondary-700 hover:text-primary-600 flex items-center gap-1 transition">
                            Mapa <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        <div class="absolute left-0 mt-4 w-56 bg-white rounded-xl shadow-lg border border-primary-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                            <div class="py-2">
                                @if(Auth::check())
                                    <a href="{{ url('/mapa') }}" class="block px-5 py-3 hover:bg-primary-50 transition flex items-center gap-2">
                                        <i class="fas fa-map-marked-alt text-primary-600"></i> Mapa interactivo
                                    </a>
                                    <a href="{{ url('/municipios-aplazados') }}" class="block px-5 py-3 hover:bg-primary-50 transition flex items-center gap-2">
                                        <i class="fas fa-city text-red-600"></i> Más aplazados por municipio
                                    </a>
                                @else
                                    <a href="/admin/login" class="block px-5 py-3 hover:bg-primary-50 transition flex items-center gap-2 opacity-60 cursor-not-allowed">
                                        <i class="fas fa-map-marked-alt text-primary-600"></i> Mapa interactivo <i class="fas fa-lock text-primary-500 ml-1"></i>
                                    </a>
                                    <a href="/admin/login" class="block px-5 py-3 hover:bg-primary-50 transition flex items-center gap-2 opacity-60 cursor-not-allowed">
                                        <i class="fas fa-city text-red-600"></i> Más aplazados por municipio <i class="fas fa-lock text-primary-500 ml-1"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </li>
                </ul>

                <button id="nav-toggle" class="lg:hidden p-2 rounded-lg hover:bg-primary-50 text-secondary-700">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Menu (mantener el mismo acordeón que antes) -->
        <div id="nav-mobile" class="hidden lg:hidden bg-white border-t border-primary-100">
            <div class="px-4 py-4 space-y-1">
                <a href="{{ url('/') }}" class="block py-3 px-4 rounded-lg hover:bg-primary-50 font-medium">Inicio</a>
                <button data-accordion="rankings" class="w-full flex justify-between items-center py-3 px-4 rounded-lg hover:bg-primary-50 font-medium">
                    Rankings <i class="fas fa-chevron-down"></i>
                </button>
                <div id="acc-rankings" class="hidden pl-4">
                    @if(Auth::check())
                        <a href="{{ url('/rankings?tipo=reprobacion') }}" class="block py-2 px-2 rounded-lg hover:bg-primary-50 flex items-center gap-2">
                            <i class="fas fa-chart-line text-red-600"></i> Reprobados
                        </a>
                        <a href="{{ url('/rankings?tipo=matricula') }}" class="block py-2 px-2 rounded-lg hover:bg-primary-50 flex items-center gap-2">
                            <i class="fas fa-users text-primary-600"></i> Matrícula
                        </a>
                        <a href="{{ url('/rankings?tipo=abandono') }}" class="block py-2 px-2 rounded-lg hover:bg-primary-50 flex items-center gap-2">
                            <i class="fas fa-user-times text-orange-600"></i> Abandono escolar
                        </a>
                        <a href="{{ url('/rankings?tipo=infraestructura') }}" class="block py-2 px-2 rounded-lg hover:bg-primary-50 flex items-center gap-2">
                            <i class="fas fa-building text-secondary-600"></i> Infraestructura
                        </a>
                    @else
                        <a href="/admin/login" class="block py-2 px-2 rounded-lg hover:bg-primary-50 flex items-center gap-2 opacity-60 cursor-not-allowed">
                            <i class="fas fa-chart-line text-red-600"></i> Reprobados <i class="fas fa-lock text-primary-500 ml-1"></i>
                        </a>
                        <a href="/admin/login" class="block py-2 px-2 rounded-lg hover:bg-primary-50 flex items-center gap-2 opacity-60 cursor-not-allowed">
                            <i class="fas fa-users text-primary-600"></i> Matrícula <i class="fas fa-lock text-primary-500 ml-1"></i>
                        </a>
                        <a href="/admin/login" class="block py-2 px-2 rounded-lg hover:bg-primary-50 flex items-center gap-2 opacity-60 cursor-not-allowed">
                            <i class="fas fa-user-times text-orange-600"></i> Abandono escolar <i class="fas fa-lock text-primary-500 ml-1"></i>
                        </a>
                        <a href="/admin/login" class="block py-2 px-2 rounded-lg hover:bg-primary-50 flex items-center gap-2 opacity-60 cursor-not-allowed">
                            <i class="fas fa-building text-secondary-600"></i> Infraestructura <i class="fas fa-lock text-primary-500 ml-1"></i>
                        </a>
                    @endif
                </div>
               
                <button data-accordion="mapa" class="w-full flex justify-between items-center py-3 px-4 rounded-lg hover:bg-primary-50 font-medium">
                    Mapa <i class="fas fa-chevron-down"></i>
                </button>
                <div id="acc-mapa" class="hidden pl-4">
                    @if(Auth::check())
                        <a href="{{ url('/mapa') }}" class="block py-2 px-2 rounded-lg hover:bg-primary-50 flex items-center gap-2">
                            <i class="fas fa-map-marked-alt text-primary-600"></i> Mapa interactivo
                        </a>
                        <a href="{{ url('/municipios-aplazados') }}" class="block py-2 px-2 rounded-lg hover:bg-primary-50 flex items-center gap-2">
                            <i class="fas fa-city text-red-600"></i> Más aplazados por municipio
                        </a>
                    @else
                        <a href="/admin/login" class="block py-2 px-2 rounded-lg hover:bg-primary-50 flex items-center gap-2 opacity-60 cursor-not-allowed">
                            <i class="fas fa-map-marked-alt text-primary-600"></i> Mapa interactivo <i class="fas fa-lock text-primary-500 ml-1"></i>
                        </a>
                        <a href="/admin/login" class="block py-2 px-2 rounded-lg hover:bg-primary-50 flex items-center gap-2 opacity-60 cursor-not-allowed">
                            <i class="fas fa-city text-red-600"></i> Más aplazados por municipio <i class="fas fa-lock text-primary-500 ml-1"></i>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Hero -->
        <div class="text-center mb-16">
            <h1 class="text-4xl sm:text-5xl font-bold text-secondary mb-6">
                <span style="background: linear-gradient(90deg, rgb(55,95,122) 0%, rgb(38,186,165) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; text-fill-color: transparent;">
                    Directorio Nacional de Colegios de Bolivia
                </span>
            </h1>
            <div class="text-xl text-secondary-600 max-w-4xl mx-auto mb-8 min-h-[3.5rem]">
                
            <script>
                @php
                    $messagesArray = ($serviceMessages->count() > 0)
                        ? $serviceMessages->map(function($m) {
                            return [
                                'message' => $m->message,
                                'service' => $m->service,
                                'boton' => $m->boton,
                                'whatsapp' => $m->whatsapp
                            ];
                        })->values()->all()
                        : [[
                            'message' => 'Accede a datos actualizados sobre matrícula, reprobación, abandono, infraestructura y conectividad de todas las instituciones educativas del país.',
                            'service' => '',
                            'boton' => '',
                            'whatsapp' => ''
                        ]];
                @endphp
                const messages = @json($messagesArray);
                let msgIndex = 0;
                let charIndex = 0;
                const typewriter = document.getElementById('typewriter');
                const serviceInfo = document.getElementById('service-info');

                function typeMessage() {
                    if (!typewriter) return;
                    const msg = messages[msgIndex].message;
                    if (charIndex < msg.length) {
                        typewriter.textContent += msg[charIndex];
                        charIndex++;
                        setTimeout(typeMessage, 40);
                    } else {
                        showServiceInfo();
                        setTimeout(eraseMessage, 1800);
                    }
                }
                function eraseMessage() {
                    if (!typewriter) return;
                    if (charIndex > 0) {
                        typewriter.textContent = messages[msgIndex].message.substring(0, charIndex - 1);
                        charIndex--;
                        setTimeout(eraseMessage, 18);
                    } else {
                        hideServiceInfo();
                        msgIndex = (msgIndex + 1) % messages.length;
                        setTimeout(typeMessage, 400);
                    }
                }
                function showServiceInfo() {
                    if (!serviceInfo) return;
                    const { service, boton, whatsapp } = messages[msgIndex];
                    let html = '';
                    if (service) {
                        html += `<span class='font-semibold text-primary-700'>${service}</span>`;
                    }
                    if (boton && whatsapp) {
                        const url = `https://wa.me/59171039910?text=${encodeURIComponent(whatsapp)}`;
                        html += ` <a href='${url}' target='_blank' class='ml-2 px-4 py-2 rounded-lg bg-green-500 text-white font-semibold hover:bg-green-600 transition'>${boton}</a>`;
                    }
                    serviceInfo.innerHTML = html;
                }
                function hideServiceInfo() {
                    if (serviceInfo) serviceInfo.innerHTML = '';
                }
                document.addEventListener('DOMContentLoaded', () => {
                    typewriter.textContent = '';
                    typeMessage();
                });
            </script>
            <!-- Buscador de colegios responsivo con radio buttons -->
            <form method="GET" action="{{ route('home') }}" class="max-w-2xl mx-auto w-full flex flex-col sm:flex-row gap-4 items-center justify-center mt-6">
                <div class="flex flex-col items-center w-full">
                    <div class="flex flex-row gap-6 items-center justify-center mb-2">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="radio" name="filter" value="nombre" class="form-radio text-primary-500" {{ request('filter', 'nombre') == 'nombre' ? 'checked' : '' }}>
                            <span class="ml-2 text-secondary-700 text-sm">Nombre</span>
                        </label>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="radio" name="filter" value="codigo" class="form-radio text-primary-500" {{ request('filter') == 'codigo' ? 'checked' : '' }}>
                            <span class="ml-2 text-secondary-700 text-sm">Código RUE</span>
                        </label>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nombre o código RUE" class="w-[90%] max-w-2xl px-4 py-3 rounded-lg border border-primary-200 focus:ring-2 focus:ring-primary-400 outline-none mx-auto mb-3" required>
                    <button type="submit" class="w-[70%] max-w-xs mx-auto px-6 py-3 rounded-lg bg-primary-500 text-white font-semibold hover:bg-primary-600 transition flex items-center gap-2 justify-center mt-1">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                </div>
            </form>
            @if(isset($schools) && $schools->count())
                <div class="mt-8 max-w-3xl mx-auto text-left">
                    <h2 class="text-lg font-semibold mb-2">Resultados de búsqueda:</h2>
                    <ul class="divide-y divide-primary-100">
                        @foreach($schools as $school)
                            <li class="py-3 flex flex-col sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <a href="{{ route('schools.show', $school->id) }}" class="font-bold text-primary-700 hover:underline">{{ $school->nombre }}</a>
                                    <div class="mt-1" style="font-size: 11px; color: rgb(121, 121, 121);">
                                        @if($school->ubicacion->departamento)
                                            <span class="mr-2"><i class="fas fa-map-marker-alt"></i> {{ $school->ubicacion->departamento }}</span>
                                        @endif
                                        @if($school->ubicacion->provincia)
                                            <span class="mr-2">Provincia: {{ $school->ubicacion->provincia }}</span>
                                        @endif
                                        @if($school->ubicacion->municipio)
                                            <span class="mr-2">Municipio: {{ $school->ubicacion->municipio }}</span>
                                        @endif
                                        @if($school->ubicacion->distrito)
                                            <span class="mr-2">Distrito: {{ $school->ubicacion->distrito }}</span>
                                        @endif
                                        @if($school->ubicacion->direccion)
                                            <span class="mr-2">Dirección: {{ $school->ubicacion->direccion }}</span>
                                        @endif
                                        @if($school->dependencia)
                                            <span class="mr-2">Dependencia: {{ $school->dependencia }}</span>
                                        @endif
                                    </div>
                                </div>
                                <a href="{{ route('schools.show', $school->id) }}" class="text-primary-600 hover:underline text-xs mt-2 sm:mt-0">Ver detalle</a>
                            </li>
                        @endforeach
                    </ul>
                    <div class="mt-4">{{ $schools->links('vendor.pagination.default') }}</div>
                </div>
            @elseif(request('search'))
                <div class="mt-8 max-w-2xl mx-auto text-center text-red-600 font-semibold">No se encontraron colegios con ese criterio.</div>
            @endif
        </div>

        <!-- KPIs principales -->
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-16">
            <div class="bg-white rounded-2xl shadow-sm p-6 border border-primary-100">
                <div class="flex items-center gap-4">
                    <div class="p-4 bg-primary-100 rounded-xl text-primary-600">
                        <i class="fas fa-school text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-secondary-600">Total de colegios</p>
                        <p class="text-3xl font-bold text-secondary-800">{{ $kpis['total'] ?? '—' }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-sm p-6 border border-primary-100">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm text-secondary-600">Colegio más poblado</p>
                        <p class="text-xl font-bold text-secondary-800">{{ $highlights['top_matricula']['nombre'] ?? '—' }}</p>
                    </div>
                    <span class="px-4 py-2 text-sm font-medium bg-primary-100 text-primary-700 rounded-full">
                        {{ $highlights['top_matricula']['valor'] ?? '—' }}
                    </span>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-sm p-6 border border-primary-100">
                <div class="flex items-center gap-4">
                    <div class="p-4 bg-red-100 rounded-xl text-red-600">
                        <i class="fas fa-arrow-trend-up text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-secondary-600">Reprobación promedio</p>
                        <p class="text-3xl font-bold text-secondary-800">{{ $kpis['rep_prom'] ?? '—' }}<span class="text-xl">%</span></p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-sm p-6 border border-primary-100">
                <div class="flex items-center gap-4">
                    <div class="p-4 bg-orange-100 rounded-xl text-orange-600">
                        <i class="fas fa-right-from-bracket text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-secondary-600">Abandono promedio</p>
                        <p class="text-3xl font-bold text-secondary-800">{{ $kpis['aban_prom'] ?? '—' }}<span class="text-xl">%</span></p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Opciones principales (tarjetas grandes de acción) -->
        <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-16 relative">
            @php $isGuest = !Auth::check(); @endphp
            <a href="{{ route('home') }}" class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl border border-primary-100 p-8 transition-all duration-300 text-center card-protegida">
                <div class="p-5 bg-primary-100 rounded-2xl w-20 h-20 mx-auto mb-6 group-hover:bg-primary-500 group-hover:text-white transition">
                    <i class="fas fa-search text-3xl text-primary-600 group-hover:text-white"></i>
                </div>
                <h3 class="text-2xl font-bold text-secondary-800 mb-3">Buscar un colegio</h3>
                <p class="text-secondary-600 mb-6">Encuentra cualquier institución por nombre, código RUE, distrito o ubicación.</p>
                <span class="inline-flex items-center gap-2 text-primary-600 font-semibold group-hover:gap-4 transition">
                    Ir al buscador → 
                </span>
            </a>
            <a href="{{ url('/rankings?tipo=reprobacion') }}" class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl border border-primary-100 p-8 transition-all duration-300 text-center card-protegida">
                <div class="p-5 bg-red-100 rounded-2xl w-20 h-20 mx-auto mb-6 group-hover:bg-red-500 group-hover:text-white transition">
                    <i class="fas fa-chart-line text-3xl text-red-600 group-hover:text-white"></i>
                </div>
                <h3 class="text-2xl font-bold text-secondary-800 mb-3">Rankings de reprobados</h3>
                <p class="text-secondary-600 mb-6">Colegios con mayor/menor tasa de reprobación, por departamento, urbana vs rural, etc.</p>
                <span class="inline-flex items-center gap-2 text-primary-600 font-semibold group-hover:gap-4 transition">
                    Ver rankings → 
                </span>
            </a>
            <a href="{{ url('/rankings?tipo=matricula') }}" class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl border border-primary-100 p-8 transition-all duration-300 text-center card-protegida">
                <div class="p-5 bg-primary-100 rounded-2xl w-20 h-20 mx-auto mb-6 group-hover:bg-primary-500 group-hover:text-white transition">
                    <i class="fas fa-users text-3xl text-primary-600 group-hover:text-white"></i>
                </div>
                <h3 class="text-2xl font-bold text-secondary-800 mb-3">Rankings de matrícula</h3>
                <p class="text-secondary-600 mb-6">Colegios más y menos poblados, crecimiento anual y comparativas departamentales.</p>
                <span class="inline-flex items-center gap-2 text-primary-600 font-semibold group-hover:gap-4 transition">
                    Explorar → 
                </span>
            </a>
            <a href="{{ url('/rankings?tipo=abandono') }}" class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl border border-primary-100 p-8 transition-all duration-300 text-center card-protegida">
                <div class="p-5 bg-orange-100 rounded-2xl w-20 h-20 mx-auto mb-6 group-hover:bg-orange-500 group-hover:text-white transition">
                    <i class="fas fa-user-times text-3xl text-orange-600 group-hover:text-white"></i>
                </div>
                <h3 class="text-2xl font-bold text-secondary-800 mb-3">Abandono escolar</h3>
                <p class="text-secondary-600 mb-6">Departamentos críticos, comparación urbana/rural y tendencias.</p>
                <span class="inline-flex items-center gap-2 text-primary-600 font-semibold group-hover:gap-4 transition">
                    Ver detalle → 
                </span>
            </a>
            <a href="{{ url('/rankings?tipo=infraestructura') }}" class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl border border-primary-100 p-8 transition-all duration-300 text-center card-protegida">
                <div class="p-5 bg-secondary-100 rounded-2xl w-20 h-20 mx-auto mb-6 group-hover:bg-secondary-500 group-hover:text-white transition">
                    <i class="fas fa-building text-3xl text-secondary-600 group-hover:text-white"></i>
                </div>
                <h3 class="text-2xl font-bold text-secondary-800 mb-3">Infraestructura</h3>
                <p class="text-secondary-600 mb-6">Mejores y peores condiciones de ambientes, servicios y equipamiento.</p>
                <span class="inline-flex items-center gap-2 text-primary-600 font-semibold group-hover:gap-4 transition">
                    Ver ranking → 
                </span>
            </a>
            <a href="{{ url('/mapa') }}" class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl border border-primary-100 p-8 transition-all duration-300 text-center card-protegida">
                <div class="p-5 bg-primary-100 rounded-2xl w-20 h-20 mx-auto mb-6 group-hover:bg-primary-500 group-hover:text-white transition">
                    <i class="fas fa-map-marked-alt text-3xl text-primary-600 group-hover:text-white"></i>
                </div>
                <h3 class="text-2xl font-bold text-secondary-800 mb-3">Mapa interactivo</h3>
                <p class="text-secondary-600 mb-6">Visualiza todos los colegios geolocalizados con filtros por indicadores.</p>
                <span class="inline-flex items-center gap-2 text-primary-600 font-semibold group-hover:gap-4 transition">
                    Abrir mapa → 
                </span>
            </a>
            <a href="{{ url('/municipios-aplazados') }}" class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl border border-primary-100 p-8 transition-all duration-300 text-center card-protegida">
                <div class="p-5 bg-red-100 rounded-2xl w-20 h-20 mx-auto mb-6 group-hover:bg-red-500 group-hover:text-white transition">
                    <i class="fas fa-city text-3xl text-red-600 group-hover:text-white"></i>
                </div>
                <h3 class="text-2xl font-bold text-secondary-800 mb-3">Colegios más aplazados por municipio</h3>
                <p class="text-secondary-600 mb-6">Consulta el colegio con mayor reprobación en cada municipio y explora el detalle de cualquier municipio.</p>
                <span class="inline-flex items-center gap-2 text-primary-600 font-semibold group-hover:gap-4 transition">
                    Ver municipios →
                </span>
            </a>

            <!-- Opciones adicionales solo visuales -->
            <a href="#" class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl border border-primary-100 p-8 transition-all duration-300 text-center card-protegida">
                <div class="p-5 bg-secondary-100 rounded-2xl w-20 h-20 mx-auto mb-6 group-hover:bg-secondary-500 group-hover:text-white transition">
                    <i class="fas fa-balance-scale text-3xl text-secondary-600 group-hover:text-white"></i>
                </div>
                <h3 class="text-2xl font-bold text-secondary-800 mb-3">Distribución por dependencia</h3>
                <p class="text-secondary-600 mb-6">Fiscal / Privada / Convenio</p>
                <span class="inline-flex items-center gap-2 text-primary-600 font-semibold group-hover:gap-4 transition">Ver distribución →</span>
            </a>
            <a href="#" class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl border border-primary-100 p-8 transition-all duration-300 text-center card-protegida">
                <div class="p-5 bg-primary-100 rounded-2xl w-20 h-20 mx-auto mb-6 group-hover:bg-primary-500 group-hover:text-white transition">
                    <i class="fas fa-map text-3xl text-primary-600 group-hover:text-white"></i>
                </div>
                <h3 class="text-2xl font-bold text-secondary-800 mb-3">Distribución por área</h3>
                <p class="text-secondary-600 mb-6">Rural vs Urbana</p>
                <span class="inline-flex items-center gap-2 text-primary-600 font-semibold group-hover:gap-4 transition">Ver áreas →</span>
            </a>
            <a href="#" class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl border border-primary-100 p-8 transition-all duration-300 text-center card-protegida">
                <div class="p-5 bg-pink-100 rounded-2xl w-20 h-20 mx-auto mb-6 group-hover:bg-pink-500 group-hover:text-white transition">
                    <i class="fas fa-venus-mars text-3xl text-pink-600 group-hover:text-white"></i>
                </div>
                <h3 class="text-2xl font-bold text-secondary-800 mb-3">Resultados por género</h3>
                <p class="text-secondary-600 mb-6">Promoción / Reprobación / Abandono</p>
                <span class="inline-flex items-center gap-2 text-primary-600 font-semibold group-hover:gap-4 transition">Ver resultados →</span>
            </a>
            <a href="#" class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl border border-primary-100 p-8 transition-all duration-300 text-center card-protegida">
                <div class="p-5 bg-primary-100 rounded-2xl w-20 h-20 mx-auto mb-6 group-hover:bg-primary-500 group-hover:text-white transition">
                    <i class="fas fa-layer-group text-3xl text-primary-600 group-hover:text-white"></i>
                </div>
                <h3 class="text-2xl font-bold text-secondary-800 mb-3">Número de colegios por departamento</h3>
                <p class="text-secondary-600 mb-6">Cantidad total de colegios por región</p>
                <span class="inline-flex items-center gap-2 text-primary-600 font-semibold group-hover:gap-4 transition">Ver números →</span>
            </a>
            <a href="#" class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl border border-primary-100 p-8 transition-all duration-300 text-center card-protegida">
                <div class="p-5 bg-orange-100 rounded-2xl w-20 h-20 mx-auto mb-6 group-hover:bg-orange-500 group-hover:text-white transition">
                    <i class="fas fa-chart-area text-3xl text-orange-600 group-hover:text-white"></i>
                </div>
                <h3 class="text-2xl font-bold text-secondary-800 mb-3">Evolución de matrícula por departamento</h3>
                <p class="text-secondary-600 mb-6">Crecimiento y variación anual</p>
                <span class="inline-flex items-center gap-2 text-primary-600 font-semibold group-hover:gap-4 transition">Ver evolución →</span>
            </a>
            <a href="#" class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl border border-primary-100 p-8 transition-all duration-300 text-center card-protegida">
                <div class="p-5 bg-green-100 rounded-2xl w-20 h-20 mx-auto mb-6 group-hover:bg-green-500 group-hover:text-white transition">
                    <i class="fas fa-arrow-up text-3xl text-green-600 group-hover:text-white"></i>
                </div>
                <h3 class="text-2xl font-bold text-secondary-800 mb-3">Tasa de promoción por departamento</h3>
                <p class="text-secondary-600 mb-6">Porcentaje de estudiantes promovidos</p>
                <span class="inline-flex items-center gap-2 text-primary-600 font-semibold group-hover:gap-4 transition">Ver tasa →</span>
            </a>
            <a href="#" class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl border border-primary-100 p-8 transition-all duration-300 text-center card-protegida">
                <div class="p-5 bg-red-100 rounded-2xl w-20 h-20 mx-auto mb-6 group-hover:bg-red-500 group-hover:text-white transition">
                    <i class="fas fa-arrow-down text-3xl text-red-600 group-hover:text-white"></i>
                </div>
                <h3 class="text-2xl font-bold text-secondary-800 mb-3">Tasa de reprobación por departamento</h3>
                <p class="text-secondary-600 mb-6">Porcentaje de estudiantes reprobados</p>
                <span class="inline-flex items-center gap-2 text-primary-600 font-semibold group-hover:gap-4 transition">Ver tasa →</span>
            </a>
            <a href="#" class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl border border-primary-100 p-8 transition-all duration-300 text-center card-protegida">
                <div class="p-5 bg-orange-100 rounded-2xl w-20 h-20 mx-auto mb-6 group-hover:bg-orange-500 group-hover:text-white transition">
                    <i class="fas fa-user-slash text-3xl text-orange-600 group-hover:text-white"></i>
                </div>
                <h3 class="text-2xl font-bold text-secondary-800 mb-3">Tasa de abandono por departamento</h3>
                <p class="text-secondary-600 mb-6">Porcentaje de abandono escolar</p>
                <span class="inline-flex items-center gap-2 text-primary-600 font-semibold group-hover:gap-4 transition">Ver tasa →</span>
            </a>
            <a href="#" class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl border border-primary-100 p-8 transition-all duration-300 text-center card-protegida">
                <div class="p-5 bg-red-100 rounded-2xl w-20 h-20 mx-auto mb-6 group-hover:bg-red-500 group-hover:text-white transition">
                    <i class="fas fa-trophy text-3xl text-red-600 group-hover:text-white"></i>
                </div>
                <h3 class="text-2xl font-bold text-secondary-800 mb-3">Colegio con más aplazados (nacional)</h3>
                <p class="text-secondary-600 mb-6">El colegio con mayor cantidad de reprobados en Bolivia</p>
                <span class="inline-flex items-center gap-2 text-primary-600 font-semibold group-hover:gap-4 transition">Ver colegio →</span>
            </a>
            <a href="#" class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl border border-primary-100 p-8 transition-all duration-300 text-center card-protegida">
                <div class="p-5 bg-red-100 rounded-2xl w-20 h-20 mx-auto mb-6 group-hover:bg-red-500 group-hover:text-white transition">
                    <i class="fas fa-trophy text-3xl text-red-600 group-hover:text-white"></i>
                </div>
                <h3 class="text-2xl font-bold text-secondary-800 mb-3">Colegio con más aplazados por departamento</h3>
                <p class="text-secondary-600 mb-6">El colegio con mayor reprobación en cada departamento</p>
                <span class="inline-flex items-center gap-2 text-primary-600 font-semibold group-hover:gap-4 transition">Ver colegios →</span>
            </a>
            <a href="#" class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl border border-primary-100 p-8 transition-all duration-300 text-center card-protegida">
                <div class="p-5 bg-red-100 rounded-2xl w-20 h-20 mx-auto mb-6 group-hover:bg-red-500 group-hover:text-white transition">
                    <i class="fas fa-trophy text-3xl text-red-600 group-hover:text-white"></i>
                </div>
                <h3 class="text-2xl font-bold text-secondary-800 mb-3">Colegio con más aplazados por provincia</h3>
                <p class="text-secondary-600 mb-6">El colegio con mayor reprobación en cada provincia</p>
                <span class="inline-flex items-center gap-2 text-primary-600 font-semibold group-hover:gap-4 transition">Ver colegios →</span>
            </a>
            <a href="#" class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl border border-primary-100 p-8 transition-all duration-300 text-center card-protegida">
                <div class="p-5 bg-red-100 rounded-2xl w-20 h-20 mx-auto mb-6 group-hover:bg-red-500 group-hover:text-white transition">
                    <i class="fas fa-trophy text-3xl text-red-600 group-hover:text-white"></i>
                </div>
                <h3 class="text-2xl font-bold text-secondary-800 mb-3">Colegio con más aplazados por municipio</h3>
                <p class="text-secondary-600 mb-6">El colegio con mayor reprobación en cada municipio</p>
                <span class="inline-flex items-center gap-2 text-primary-600 font-semibold group-hover:gap-4 transition">Ver colegios →</span>
            </a>
            <a href="#" class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl border border-primary-100 p-8 transition-all duration-300 text-center card-protegida">
                <div class="p-5 bg-red-100 rounded-2xl w-20 h-20 mx-auto mb-6 group-hover:bg-red-500 group-hover:text-white transition">
                    <i class="fas fa-trophy text-3xl text-red-600 group-hover:text-white"></i>
                </div>
                <h3 class="text-2xl font-bold text-secondary-800 mb-3">Colegio con más aplazados por distrito</h3>
                <p class="text-secondary-600 mb-6">El colegio con mayor reprobación en cada distrito</p>
                <span class="inline-flex items-center gap-2 text-primary-600 font-semibold group-hover:gap-4 transition">Ver colegios →</span>
            </a>
            @if($isGuest)
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('.card-protegida').forEach(card => {
                    card.addEventListener('click', function(e) {
                        e.preventDefault();
                        window.location.href = '/admin/login';
                    });
                    card.classList.add('cursor-not-allowed','opacity-60','relative');
                    // Agregar candado si no existe
                    if (!card.querySelector('.lock-icon')) {
                        const lock = document.createElement('div');
                        lock.innerHTML = '<i class="fas fa-lock text-xl text-primary-500 drop-shadow-lg"></i>';
                        lock.className = 'lock-icon absolute top-4 right-4 z-20 bg-white/80 rounded-full p-2 shadow-lg';
                        card.appendChild(lock);
                    }
                });
            });
            </script>
            @endif
        </section>

      
    </main>

    <!-- Footer / includes -->
    @include('includes.pie')

    <script src="{{ asset('js/vistas/welcome/menu.js') }}"></script>
    <script>
        // Toggle menú móvil y acordeones (mismo que antes)
        const navToggle = document.getElementById('nav-toggle');
        const navMobile = document.getElementById('nav-mobile');
        if (navToggle) {
            navToggle.addEventListener('click', () => {
                navMobile.classList.toggle('hidden');
            });
        }

        document.querySelectorAll('[data-accordion]').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.getAttribute('data-accordion');
                const panel = document.getElementById(`acc-${id}`);
                if (panel) panel.classList.toggle('hidden');
                btn.querySelector('i')?.classList.toggle('rotate-180');
            });
        });
    </script>
</body>
</html>