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

    {{-- %%%%%%%%%%%%%%%%%%%%%  menú responsivo mejorado %%%%%%%%%%%%%%%%%%%%%%% --}}
    <nav class="sticky top-0 z-50 bg-white/90 backdrop-blur border-b border-primary/20">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex items-center justify-between h-14">
                <!-- Logo -->
                <a href="{{ url('/') }}" class="flex items-center gap-2 text-secondary font-bold">
                    <i class="fas fa-school text-primary"></i>
                    <span>Colegios Bolivia</span>
                </a>

                <!-- Botón móvil -->
                <button id="nav-toggle" class="md:hidden inline-flex items-center justify-center p-2 rounded text-secondary hover:text-primary focus:outline-none">
                    <span class="sr-only">Abrir menú</span>
                    <i class="fas fa-bars text-xl"></i>
                </button>

                <!-- Menú desktop -->
                <ul class="hidden md:flex items-center gap-6 text-sm">
                    <li><a href="{{ url('/') }}" class="hover:text-primary font-semibold">Inicio</a></li>

                    <li class="relative group">
                        <button class="inline-flex items-center gap-1 font-semibold hover:text-primary">
                            Matrícula <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        <div class="absolute left-0 top-full z-50 hidden group-hover:block bg-white border border-primary/20 rounded-lg shadow-lg w-64">
                            <a class="block px-4 py-2 hover:bg-primary/10" href="#">Top 10 más poblados</a>
                            <a class="block px-4 py-2 hover:bg-primary/10" href="#">Top 10 menos poblados</a>
                            <a class="block px-4 py-2 hover:bg-primary/10" href="#">Crecimiento anual</a>
                        </div>
                    </li>

                    <li class="relative group">
                        <a href="/reprobados" class="inline-flex items-center gap-1 font-semibold hover:text-primary">
                            Reprobados <i class="fas fa-chevron-down text-xs"></i>
                        </a>
                        <div class="absolute left-0 top-full z-50 hidden group-hover:block bg-white border border-primary/20 rounded-lg shadow-lg w-80">
                            <div class="grid grid-cols-2 gap-2 p-2">
                                <a class="px-3 py-2 rounded hover:bg-primary/10" href="#">Por departamento</a>
                                <a class="px-3 py-2 rounded hover:bg-primary/10" href="#">Bolivia</a>
                                <a class="px-3 py-2 rounded hover:bg-primary/10" href="#">Mayor tasa</a>
                                <a class="px-3 py-2 rounded hover:bg-primary/10" href="#">Menor tasa</a>
                                <a class="px-3 py-2 rounded hover:bg-primary/10" href="#">Urbana vs Rural</a>
                                <a class="px-3 py-2 rounded hover:bg-primary/10" href="#">Por género</a>
                                <a class="px-3 py-2 rounded hover:bg-primary/10" href="#">Cero reprobación</a>
                                <a class="px-3 py-2 rounded hover:bg-primary/10" href="#">Tendencia</a>
                            </div>
                        </div>
                    </li>

                    <li class="relative group">
                        <button class="inline-flex items-center gap-1 font-semibold hover:text-primary">
                            Abandono <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        <div class="absolute left-0 top-full z-50 hidden group-hover:block bg-white border border-primary/20 rounded-lg shadow-lg w-64">
                            <a class="block px-4 py-2 hover:bg-primary/10" href="#">Departamentos críticos</a>
                            <a class="block px-4 py-2 hover:bg-primary/10" href="#">Urbana vs Rural</a>
                        </div>
                    </li>

                    <li><a href="#" class="hover:text-primary font-semibold">Infraestructura</a></li>
                    <li><a href="#" class="hover:text-primary font-semibold">Mapa</a></li>
                </ul>
            </div>

            <!-- Menú móvil -->
            <div id="nav-mobile" class="md:hidden hidden border-t border-primary/20 py-2">
                <ul class="space-y-1">
                    <li><a href="{{ url('/') }}" class="block px-3 py-2 rounded hover:bg-primary/10">Inicio</a></li>

                    <li>
                        <button class="w-full flex items-center justify-between px-3 py-2" data-accordion="matricula">
                            <span>Matrícula</span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <ul id="acc-matricula" class="hidden pl-3 space-y-1">
                            <li><a class="block px-3 py-2 rounded hover:bg-primary/10" href="#">Top 10 más poblados</a></li>
                            <li><a class="block px-3 py-2 rounded hover:bg-primary/10" href="#">Top 10 menos poblados</a></li>
                            <li><a class="block px-3 py-2 rounded hover:bg-primary/10" href="#">Crecimiento anual</a></li>
                        </ul>
                    </li>

                    <li>
                        <button class="w-full flex items-center justify-between px-3 py-2" data-accordion="reprobados">
                            <span>Reprobados</span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <ul id="acc-reprobados" class="hidden pl-3 space-y-1">
                            <li><a class="block px-3 py-2 rounded hover:bg-primary/10" href="/reprobados">General</a></li>
                            <li><a class="block px-3 py-2 rounded hover:bg-primary/10" href="#">Por departamento</a></li>
                            <li><a class="block px-3 py-2 rounded hover:bg-primary/10" href="#">Urbana vs Rural</a></li>
                            <li><a class="block px-3 py-2 rounded hover:bg-primary/10" href="#">Por género</a></li>
                        </ul>
                    </li>

                    <li>
                        <button class="w-full flex items-center justify-between px-3 py-2" data-accordion="abandono">
                            <span>Abandono</span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <ul id="acc-abandono" class="hidden pl-3 space-y-1">
                            <li><a class="block px-3 py-2 rounded hover:bg-primary/10" href="#">Departamentos críticos</a></li>
                            <li><a class="block px-3 py-2 rounded hover:bg-primary/10" href="#">Urbana vs Rural</a></li>
                        </ul>
                    </li>

                    <li><a href="#" class="block px-3 py-2 rounded hover:bg-primary/10">Infraestructura</a></li>
                    <li><a href="#" class="block px-3 py-2 rounded hover:bg-primary/10">Mapa</a></li>
                </ul>
            </div>
        </div>
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

        <!-- KPIs principales -->
        <section aria-label="Indicadores principales" class="max-w-7xl mx-auto grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-xl shadow p-4 flex items-center gap-3">
                <div class="p-3 rounded-lg bg-primary/10 text-primary"><i class="fas fa-school"></i></div>
                <div>
                    <p class="text-xs text-secondary/70">Total de colegios</p>
                    <p class="text-2xl font-bold">{{ $kpis['total'] ?? '—' }}</p>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow p-4 flex items-center gap-3">
                <div class="p-3 rounded-lg bg-primary/10 text-primary"><i class="fas fa-wifi"></i></div>
                <div>
                    <p class="text-xs text-secondary/70">Colegios con Internet</p>
                    <p class="text-2xl font-bold">{{ $kpis['pct_internet'] ?? '—' }}<span class="text-sm">%</span></p>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow p-4 flex items-center gap-3">
                <div class="p-3 rounded-lg bg-primary/10 text-primary"><i class="fas fa-arrow-trend-down"></i></div>
                <div>
                    <p class="text-xs text-secondary/70">Reprobación promedio</p>
                    <p class="text-2xl font-bold">{{ $kpis['rep_prom'] ?? '—' }}<span class="text-sm">%</span></p>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow p-4 flex items-center gap-3">
                <div class="p-3 rounded-lg bg-primary/10 text-primary"><i class="fas fa-right-from-bracket"></i></div>
                <div>
                    <p class="text-xs text-secondary/70">Abandono promedio</p>
                    <p class="text-2xl font-bold">{{ $kpis['aban_prom'] ?? '—' }}<span class="text-sm">%</span></p>
                </div>
            </div>
        </section>
        
        <!-- Destacados -->
        <section aria-label="Destacados" class="max-w-7xl mx-auto mb-8">
            <h2 class="text-xl font-bold text-secondary mb-3 flex items-center gap-2">
                <i class="fas fa-star text-primary"></i> Datos sobresalientes
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <a href="{{ url('/rankings?tipo=matricula') }}" class="group bg-white rounded-xl shadow p-4 hover:shadow-md transition">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs text-secondary/70">Más matriculados</p>
                            <p class="font-semibold">{{ $highlights['top_matricula']['nombre'] ?? '—' }}</p>
                        </div>
                        <span class="px-2 py-1 text-xs rounded bg-primary/10 text-primary">{{ $highlights['top_matricula']['valor'] ?? '—' }}</span>
                    </div>
                    <p class="mt-2 text-secondary/70 text-sm">{{ $highlights['top_matricula']['detalle'] ?? 'Colegios con mayor matrícula (último año).' }}</p>
                    <div class="mt-3 inline-flex items-center gap-2 text-primary group-hover:gap-3 transition">
                        Ver ranking <i class="fas fa-arrow-right"></i>
                    </div>
                </a>

                <a href="{{ url('/rankings?tipo=reprobacion&modo=porcentaje') }}" class="group bg-white rounded-xl shadow p-4 hover:shadow-md transition">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs text-secondary/70">Mayor reprobación (porcentaje)</p>
                            <p class="font-semibold">{{ $highlights['top_reprobacion']['nombre'] ?? '—' }}</p>
                        </div>
                        <span class="px-2 py-1 text-xs rounded bg-red-50 text-red-600">{{ $highlights['top_reprobacion']['valor'] ?? '—%' }}</span>
                    </div>
                    <p class="mt-2 text-secondary/70 text-sm">{{ $highlights['top_reprobacion']['detalle'] ?? 'Colegios con mayor tasa de reprobación (último año).' }}</p>
                    <div class="mt-3 inline-flex items-center gap-2 text-primary group-hover:gap-3 transition">
                        Ver ranking <i class="fas fa-arrow-right"></i>
                    </div>
                </a>

                <a href="{{ url('/rankings?tipo=reprobacion&modo=cantidad') }}" class="group bg-white rounded-xl shadow p-4 hover:shadow-md transition">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs text-secondary/70">Mayor reprobación (cantidad)</p>
                            <p class="font-semibold">{{ $highlights['top_reprobacion_abs']['nombre'] ?? '—' }}</p>
                        </div>
                        <span class="px-2 py-1 text-xs rounded bg-red-50 text-red-600">{{ $highlights['top_reprobacion_abs']['valor'] ?? '—' }}</span>
                    </div>
                    <p class="mt-2 text-secondary/70 text-sm">Colegios con más reprobados en números absolutos.</p>
                    <div class="mt-3 inline-flex items-center gap-2 text-primary group-hover:gap-3 transition">
                        Ver ranking <i class="fas fa-arrow-right"></i>
                    </div>
                </a>

                <a href="{{ url('/rankings?tipo=abandono') }}" class="group bg-white rounded-xl shadow p-4 hover:shadow-md transition">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs text-secondary/70">Mayor abandono</p>
                            <p class="font-semibold">{{ $highlights['top_abandono']['nombre'] ?? '—' }}</p>
                        </div>
                        <span class="px-2 py-1 text-xs rounded bg-orange-50 text-orange-600">{{ $highlights['top_abandono']['valor'] ?? '—%' }}</span>
                    </div>
                    <p class="mt-2 text-secondary/70 text-sm">{{ $highlights['top_abandono']['detalle'] ?? 'Colegios con mayor abandono escolar.' }}</p>
                    <div class="mt-3 inline-flex items-center gap-2 text-primary group-hover:gap-3 transition">
                        Ver ranking <i class="fas fa-arrow-right"></i>
                    </div>
                </a>

                <a href="{{ url('/rankings?tipo=cero-reprobacion') }}" class="group bg-white rounded-xl shadow p-4 hover:shadow-md transition">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs text-secondary/70">Reprobación cero</p>
                            <p class="font-semibold">{{ $highlights['cero_reprobacion']['nombre'] ?? '—' }}</p>
                        </div>
                        <span class="px-2 py-1 text-xs rounded bg-emerald-50 text-emerald-600">{{ $highlights['cero_reprobacion']['valor'] ?? '—' }}</span>
                    </div>
                    <p class="mt-2 text-secondary/70 text-sm">{{ $highlights['cero_reprobacion']['detalle'] ?? 'Colegios sin reprobados.' }}</p>
                    <div class="mt-3 inline-flex items-center gap-2 text-primary group-hover:gap-3 transition">
                        Ver lista <i class="fas fa-arrow-right"></i>
                    </div>
                </a>

                <a href="{{ url('/rankings?tipo=infraestructura') }}" class="group bg-white rounded-xl shadow p-4 hover:shadow-md transition">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs text-secondary/70">Mejor infraestructura</p>
                            <p class="font-semibold">{{ $highlights['infra_mejor']['nombre'] ?? '—' }}</p>
                        </div>
                        <span class="px-2 py-1 text-xs rounded bg-sky-50 text-sky-600">{{ $highlights['infra_mejor']['valor'] ?? '—' }}</span>
                    </div>
                    <p class="mt-2 text-secondary/70 text-sm">{{ $highlights['infra_mejor']['detalle'] ?? 'Relación ambientes y servicios.' }}</p>
                    <div class="mt-3 inline-flex items-center gap-2 text-primary group-hover:gap-3 transition">
                        Ver ranking <i class="fas fa-arrow-right"></i>
                    </div>
                </a>

                <a href="{{ url('/rankings?tipo=internet') }}" class="group bg-white rounded-xl shadow p-4 hover:shadow-md transition">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs text-secondary/70">Con Internet</p>
                            <p class="font-semibold">{{ $highlights['con_internet']['nombre'] ?? '—' }}</p>
                        </div>
                        <span class="px-2 py-1 text-xs rounded bg-indigo-50 text-indigo-600">{{ $highlights['con_internet']['valor'] ?? '—%' }}</span>
                    </div>
                    <p class="mt-2 text-secondary/70 text-sm">{{ $highlights['con_internet']['detalle'] ?? 'Cobertura de conectividad.' }}</p>
                    <div class="mt-3 inline-flex items-center gap-2 text-primary group-hover:gap-3 transition">
                        Ver detalle <i class="fas fa-arrow-right"></i>
                    </div>
                </a>
            </div>
        </section>
        <div class="bg-white rounded-xl shadow-lg max-w-2xl mx-auto p-6 mb-8">
            <div class="flex flex-col gap-3">
                <label class="block text-secondary font-semibold">
                    <i class="fas fa-search"></i> Buscar colegio
                </label>
                <div class="relative">
                    <input type="text" id="home-school-search" autocomplete="off"
                           class="pl-10 pr-4 py-3 rounded-lg border border-primary/30 bg-primary/5 text-secondary w-full focus:ring-2 focus:ring-primary outline-none transition"
                           placeholder="Escribe el nombre del colegio...">
                    <span class="absolute left-3 top-3.5 text-primary pointer-events-none">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="hidden" id="home-school-id">
                    <div id="home-school-results" class="absolute z-50 mt-1 w-full bg-white border rounded shadow max-h-64 overflow-y-auto hidden"></div>
                </div>
                <div class="flex gap-2">
                    <button id="home-btn-ver-colegio" class="bg-primary hover:bg-secondary text-white font-bold py-2 px-4 rounded-lg shadow transition disabled:opacity-50" disabled>
                        <i class="fas fa-eye"></i> Ver colegio
                    </button>
                    <a href="{{ route('home') }}" class="px-4 py-2 rounded-lg border border-primary text-primary hover:bg-primary hover:text-white">
                        Todos los colegios
                    </a>
                </div>
                <p class="text-xs text-secondary/70">Las sugerencias muestran ubicación, turno, nivel, RUE y dependencia para distinguir nombres similares.</p>
            </div>
        </div>

        <script>
        (function(){
            const input = document.getElementById('home-school-search');
            const box = document.getElementById('home-school-results');
            const idField = document.getElementById('home-school-id');
            const btnVer = document.getElementById('home-btn-ver-colegio');
            let timer;
            function render(items){
                    if(!items || !items.length){ box.classList.add('hidden'); box.innerHTML=''; return; }
                box.innerHTML = items.map(item => {
                    const ubic = item.ubicacion || {};
                    const turno = item.turno ? ` • Turno: ${item.turno}` : '';
                    const nivel = item.nivel ? ` • Nivel: ${item.nivel}` : '';
                    const rude = item.codigo_rue ? ` • RUE: ${item.codigo_rue}` : '';
                    const dep = item.dependencia ? ` • ${item.dependencia}` : '';
                    const loc = [ubic.departamento, ubic.provincia, ubic.municipio, ubic.distrito].filter(Boolean).join(', ');
                    return `<button type="button" data-id="${item.id}" data-name="${item.nombre}" class="w-full text-left px-3 py-2 hover:bg-primary/10">
                        <div class="text-sm font-semibold text-secondary">${item.nombre}</div>
                        <div class="text-xs text-secondary/70">${loc}${turno}${nivel}${rude}${dep}</div>
                    </button>`;
                }).join('');
                box.classList.remove('hidden');
                Array.from(box.querySelectorAll('button')).forEach(btn => {
                    btn.addEventListener('click', () => {
                        const id = btn.getAttribute('data-id');
                        const name = btn.getAttribute('data-name');
                        input.value = name; idField.value = id; box.classList.add('hidden');
                        btnVer.disabled = !id;
                        input.style.borderColor = 'rgb(38,186,165)';
                    });
                });
            }
            async function search(term){
                const url = new URL('{{ route('schools.search') }}', window.location.origin);
                url.searchParams.set('q', term);
                try {
                    const res = await fetch(url.toString());
                    const data = await res.json();
                    render(data);
                } catch(e){ box.classList.add('hidden'); }
            }
            input.addEventListener('input', () => {
                clearTimeout(timer);
                const term = input.value.trim();
                if(term.length < 2){ box.classList.add('hidden'); return; }
                timer = setTimeout(() => search(term), 250);
            });
            document.addEventListener('click', (e) => {
                if(!box.contains(e.target) && e.target !== input){ box.classList.add('hidden'); }
            });
            btnVer.addEventListener('click', () => {
                const id = idField.value; if(!id) return;
                window.location.href = `{{ url('/schools') }}/${id}`;
            });
                results.addEventListener('click', (e) => {
                    const btn = e.target.closest('button[data-id]');
                    if (!btn) return;
                    const id = btn.getAttribute('data-id');
                    const name = btn.getAttribute('data-name');
                    const rue = btn.getAttribute('data-rue');
                    const turno = btn.getAttribute('data-turno');
                    const nivel = btn.getAttribute('data-nivel');
                    const dep = btn.getAttribute('data-dep');
                    const muni = btn.getAttribute('data-muni');
                    const prov = btn.getAttribute('data-prov');
                    const dist = btn.getAttribute('data-dist');
                    // Habilitar botones
                    verBtn.disabled = false;
                    allBtn.disabled = false;
                    // Navegar automáticamente al ranking por cantidad con el colegio seleccionado
                    const params = new URLSearchParams({
                        tipo: 'reprobacion',
                        year: yearSelect.value,
                        nivel: scopeSelect.value || 'departamental',
                        school_id: id,
                        modo: 'cantidad'
                    });
                    window.location.href = `{{ route('rankings.index') }}?${params.toString()}`;
                });
        })();
        </script>

        <!-- Hoy en Bolivia -->
        <section aria-label="Hoy en Bolivia" class="max-w-7xl mx-auto mb-8">
            <h2 class="text-xl font-bold text-secondary mb-3 flex items-center gap-2"><i class="fas fa-bolt text-primary"></i> Hoy en Bolivia</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="#" class="group bg-white rounded-xl shadow p-4 hover:shadow-md transition">
                    <p class="text-xs text-secondary/70">Ranking</p>
                    <p class="font-semibold">Top reprobación (país)</p>
                    <p class="text-secondary/70 text-sm">Último año reportado</p>
                    <div class="mt-3 inline-flex items-center gap-2 text-primary group-hover:gap-3 transition">
                        Ver lista <i class="fas fa-arrow-right"></i>
                    </div>
                </a>
                <a href="#" class="group bg-white rounded-xl shadow p-4 hover:shadow-md transition">
                    <p class="text-xs text-secondary/70">Ranking</p>
                    <p class="font-semibold">Top matrícula (departamentos)</p>
                    <p class="text-secondary/70 text-sm">Colegios más poblados</p>
                    <div class="mt-3 inline-flex items-center gap-2 text-primary group-hover:gap-3 transition">
                        Ver lista <i class="fas fa-arrow-right"></i>
                    </div>
                </a>
                <a href="#" class="group bg-white rounded-xl shadow p-4 hover:shadow-md transition">
                    <p class="text-xs text-secondary/70">Comparativa</p>
                    <p class="font-semibold">Urbana vs Rural (reprobación)</p>
                    <p class="text-secondary/70 text-sm">Departamentos críticos</p>
                    <div class="mt-3 inline-flex items-center gap-2 text-primary group-hover:gap-3 transition">
                        Ver detalle <i class="fas fa-arrow-right"></i>
                    </div>
                </a>
                <a href="#" class="group bg-white rounded-xl shadow p-4 hover:shadow-md transition">
                    <p class="text-xs text-secondary/70">Buenas noticias</p>
                    <p class="font-semibold">Colegios con reprobación cero</p>
                    <p class="text-secondary/70 text-sm">Por municipio</p>
                    <div class="mt-3 inline-flex items-center gap-2 text-primary group-hover:gap-3 transition">
                        Ver lista <i class="fas fa-arrow-right"></i>
                    </div>
                </a>
            </div>
        </section>
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
        
        <!-- Leyenda de indicadores -->
        <div class="max-w-7xl mx-auto mb-8 text-xs text-secondary/70">
            <p class="flex items-center gap-2"><span class="inline-block w-3 h-3 rounded-full bg-red-400"></span> Alto: valores elevados (ej. reprobación/abandono)</p>
            <p class="flex items-center gap-2"><span class="inline-block w-3 h-3 rounded-full bg-emerald-400"></span> Bueno: resultados destacados (ej. reprobación cero)</p>
            <p class="flex items-center gap-2"><span class="inline-block w-3 h-3 rounded-full bg-sky-400"></span> Infraestructura favorable o conectividad</p>
        </div>

        

        @include('includes.unete')
        @include('includes.servicios')
        @include('includes.redes')
        @include('includes.escribenos')   
        @include('includes.pie')
        
        {{-- %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%  footer  %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% --}}

    </div>

    
    <script src="{{ asset('js/vistas/welcome/menu.js') }}"></script>
    <script>
        // Toggle menú móvil
        const navToggle = document.getElementById('nav-toggle');
        const navMobile = document.getElementById('nav-mobile');
        if (navToggle) {
            navToggle.addEventListener('click', () => {
                navMobile.classList.toggle('hidden');
            });
        }

        // Acordeones móviles
        document.querySelectorAll('[data-accordion]').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.getAttribute('data-accordion');
                const panel = document.getElementById(`acc-${id}`);
                if (panel) panel.classList.toggle('hidden');
                btn.querySelector('i')?.classList.toggle('rotate-180');
            });
        });

        // (Sección de "Listo para publicar" eliminada)
    </script>
</body>
</html>





