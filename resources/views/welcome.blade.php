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
                            Matrícula <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        <div class="absolute left-0 mt-4 w-64 bg-white rounded-xl shadow-lg border border-primary-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                            <div class="py-2">
                                <a href="#" class="block px-5 py-3 hover:bg-primary-50 transition">Top 10 más poblados</a>
                                <a href="#" class="block px-5 py-3 hover:bg-primary-50 transition">Top 10 menos poblados</a>
                                <a href="#" class="block px-5 py-3 hover:bg-primary-50 transition">Crecimiento anual</a>
                            </div>
                        </div>
                    </li>
                    <li class="relative group">
                        <a href="/reprobados" class="font-medium text-secondary-700 hover:text-primary-600 flex items-center gap-1 transition">
                            Reprobados <i class="fas fa-chevron-down text-xs"></i>
                        </a>
                        <div class="absolute left-0 mt-4 w-80 bg-white rounded-xl shadow-lg border border-primary-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                            <div class="grid grid-cols-2 gap-1 p-3">
                                <a href="#" class="px-4 py-2 rounded-lg hover:bg-primary-50 transition">Por departamento</a>
                                <a href="#" class="px-4 py-2 rounded-lg hover:bg-primary-50 transition">Bolivia</a>
                                <a href="#" class="px-4 py-2 rounded-lg hover:bg-primary-50 transition">Mayor tasa</a>
                                <a href="#" class="px-4 py-2 rounded-lg hover:bg-primary-50 transition">Menor tasa</a>
                                <a href="#" class="px-4 py-2 rounded-lg hover:bg-primary-50 transition">Urbana vs Rural</a>
                                <a href="#" class="px-4 py-2 rounded-lg hover:bg-primary-50 transition">Por género</a>
                                <a href="#" class="px-4 py-2 rounded-lg hover:bg-primary-50 transition">Cero reprobación</a>
                                <a href="#" class="px-4 py-2 rounded-lg hover:bg-primary-50 transition">Tendencia</a>
                            </div>
                        </div>
                    </li>
                    <li class="relative group">
                        <button class="font-medium text-secondary-700 hover:text-primary-600 flex items-center gap-1 transition">
                            Abandono <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        <div class="absolute left-0 mt-4 w-64 bg-white rounded-xl shadow-lg border border-primary-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                            <div class="py-2">
                                <a href="#" class="block px-5 py-3 hover:bg-primary-50 transition">Departamentos críticos</a>
                                <a href="#" class="block px-5 py-3 hover:bg-primary-50 transition">Urbana vs Rural</a>
                            </div>
                        </div>
                    </li>
                    <li><a href="#" class="font-medium text-secondary-700 hover:text-primary-600 transition">Infraestructura</a></li>
                    <li><a href="#" class="font-medium text-secondary-700 hover:text-primary-600 transition">Mapa</a></li>
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
                <!-- Acordeones móviles aquí (igual que en la versión anterior) -->
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Hero -->
        <div class="text-center mb-16">
            <h1 class="text-4xl sm:text-5xl font-bold text-secondary-800 mb-6">
                Directorio Nacional de Colegios de Bolivia
            </h1>
            <p class="text-xl text-secondary-600 max-w-4xl mx-auto">
                Accede a datos actualizados sobre matrícula, reprobación, abandono, infraestructura y conectividad de todas las instituciones educativas del país.
            </p>
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
                <div class="flex items-center gap-4">
                    <div class="p-4 bg-primary-100 rounded-xl text-primary-600">
                        <i class="fas fa-wifi text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-secondary-600">Con acceso a Internet</p>
                        <p class="text-3xl font-bold text-secondary-800">{{ $kpis['pct_internet'] ?? '—' }}<span class="text-xl">%</span></p>
                    </div>
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
        <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-16">
            <a href="{{ route('home') }}" class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl border border-primary-100 p-8 transition-all duration-300 text-center">
                <div class="p-5 bg-primary-100 rounded-2xl w-20 h-20 mx-auto mb-6 group-hover:bg-primary-500 group-hover:text-white transition">
                    <i class="fas fa-search text-3xl text-primary-600 group-hover:text-white"></i>
                </div>
                <h3 class="text-2xl font-bold text-secondary-800 mb-3">Buscar un colegio</h3>
                <p class="text-secondary-600 mb-6">Encuentra cualquier institución por nombre, código RUE, distrito o ubicación.</p>
                <span class="inline-flex items-center gap-2 text-primary-600 font-semibold group-hover:gap-4 transition">
                    Ir al buscador → 
                </span>
            </a>

            <a href="{{ url('/rankings?tipo=reprobacion') }}" class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl border border-primary-100 p-8 transition-all duration-300 text-center">
                <div class="p-5 bg-red-100 rounded-2xl w-20 h-20 mx-auto mb-6 group-hover:bg-red-500 group-hover:text-white transition">
                    <i class="fas fa-chart-line text-3xl text-red-600 group-hover:text-white"></i>
                </div>
                <h3 class="text-2xl font-bold text-secondary-800 mb-3">Rankings de reprobados</h3>
                <p class="text-secondary-600 mb-6">Colegios con mayor/menor tasa de reprobación, por departamento, urbana vs rural, etc.</p>
                <span class="inline-flex items-center gap-2 text-primary-600 font-semibold group-hover:gap-4 transition">
                    Ver rankings → 
                </span>
            </a>

            <a href="{{ url('/rankings?tipo=matricula') }}" class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl border border-primary-100 p-8 transition-all duration-300 text-center">
                <div class="p-5 bg-primary-100 rounded-2xl w-20 h-20 mx-auto mb-6 group-hover:bg-primary-500 group-hover:text-white transition">
                    <i class="fas fa-users text-3xl text-primary-600 group-hover:text-white"></i>
                </div>
                <h3 class="text-2xl font-bold text-secondary-800 mb-3">Rankings de matrícula</h3>
                <p class="text-secondary-600 mb-6">Colegios más y menos poblados, crecimiento anual y comparativas departamentales.</p>
                <span class="inline-flex items-center gap-2 text-primary-600 font-semibold group-hover:gap-4 transition">
                    Explorar → 
                </span>
            </a>

            <a href="{{ url('/rankings?tipo=abandono') }}" class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl border border-primary-100 p-8 transition-all duration-300 text-center">
                <div class="p-5 bg-orange-100 rounded-2xl w-20 h-20 mx-auto mb-6 group-hover:bg-orange-500 group-hover:text-white transition">
                    <i class="fas fa-user-times text-3xl text-orange-600 group-hover:text-white"></i>
                </div>
                <h3 class="text-2xl font-bold text-secondary-800 mb-3">Abandono escolar</h3>
                <p class="text-secondary-600 mb-6">Departamentos críticos, comparación urbana/rural y tendencias.</p>
                <span class="inline-flex items-center gap-2 text-primary-600 font-semibold group-hover:gap-4 transition">
                    Ver detalle → 
                </span>
            </a>

            <a href="{{ url('/rankings?tipo=infraestructura') }}" class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl border border-primary-100 p-8 transition-all duration-300 text-center">
                <div class="p-5 bg-secondary-100 rounded-2xl w-20 h-20 mx-auto mb-6 group-hover:bg-secondary-500 group-hover:text-white transition">
                    <i class="fas fa-building text-3xl text-secondary-600 group-hover:text-white"></i>
                </div>
                <h3 class="text-2xl font-bold text-secondary-800 mb-3">Infraestructura</h3>
                <p class="text-secondary-600 mb-6">Mejores y peores condiciones de ambientes, servicios y equipamiento.</p>
                <span class="inline-flex items-center gap-2 text-primary-600 font-semibold group-hover:gap-4 transition">
                    Ver ranking → 
                </span>
            </a>

            <a href="{{ url('/mapa') }}" class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl border border-primary-100 p-8 transition-all duration-300 text-center">
                <div class="p-5 bg-primary-100 rounded-2xl w-20 h-20 mx-auto mb-6 group-hover:bg-primary-500 group-hover:text-white transition">
                    <i class="fas fa-map-marked-alt text-3xl text-primary-600 group-hover:text-white"></i>
                </div>
                <h3 class="text-2xl font-bold text-secondary-800 mb-3">Mapa interactivo</h3>
                <p class="text-secondary-600 mb-6">Visualiza todos los colegios geolocalizados con filtros por indicadores.</p>
                <span class="inline-flex items-center gap-2 text-primary-600 font-semibold group-hover:gap-4 transition">
                    Abrir mapa → 
                </span>
            </a>

            <a href="{{ url('/municipios-aplazados') }}" class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl border border-primary-100 p-8 transition-all duration-300 text-center">
                <div class="p-5 bg-red-100 rounded-2xl w-20 h-20 mx-auto mb-6 group-hover:bg-red-500 group-hover:text-white transition">
                    <i class="fas fa-city text-3xl text-red-600 group-hover:text-white"></i>
                </div>
                <h3 class="text-2xl font-bold text-secondary-800 mb-3">Colegios más aplazados por municipio</h3>
                <p class="text-secondary-600 mb-6">Consulta el colegio con mayor reprobación en cada municipio y explora el detalle de cualquier municipio.</p>
                <span class="inline-flex items-center gap-2 text-primary-600 font-semibold group-hover:gap-4 transition">
                    Ver municipios →
                </span>
            </a>
        </section>

        <!-- Datos destacados (opcional, mantiene el impacto visual) -->
        <section class="mb-16">
            <h2 class="text-2xl font-bold text-secondary-800 mb-8 text-center">
                <i class="fas fa-star text-primary-600 mr-3"></i> Datos sobresalientes del último año
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Puedes mantener algunas tarjetas destacadas aquí si lo deseas -->
                <!-- Ejemplo de una: -->
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
                <!-- Agrega más si quieres, o quítalas completamente -->
            </div>
        </section>
    </main>

    <!-- Footer / includes -->
    @include('includes.unete')
    @include('includes.servicios')
    @include('includes.redes')
    @include('includes.escribenos')   
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