@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #mapa-wrapper {
        position: relative;
        border-radius: 0.75rem;
        overflow: hidden;
    }
    #mapa-aplazados { height: 560px; border-radius: 0.75rem; }
    #mapa-wrapper:fullscreen {
        border-radius: 0;
        background: #0b1b27;
    }
    #mapa-wrapper:fullscreen #mapa-aplazados {
        height: 100vh;
        border-radius: 0;
    }
    #mapa-fullscreen-title {
        display: none;
        position: absolute;
        top: 12px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 1000;
        padding: 10px 14px;
        border-radius: 8px;
        background: rgb(55, 95, 122);
        color: #ffffff;
        font-size: 16px;
        font-weight: 700;
        letter-spacing: 0.02em;
        text-align: center;
        pointer-events: none;
    }
    #mapa-wrapper:fullscreen #mapa-fullscreen-title {
        display: block;
    }
    .legend-gradient {
        background: linear-gradient(90deg, #16a34a 0%, #16a34a 8%, #fb923c 20%, #ef4444 65%, #7f1d1d 100%);
        height: 12px;
        border-radius: 9999px;
    }
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="bg-white rounded-xl shadow p-5 mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Panel de Calor de Aplazados por Colegio</h1>
        <p class="text-sm text-gray-600 mt-1">Solo 0 aplazados es verde. Desde 1 aplazado el color va de naranja a rojo oscuro según el máximo del filtro aplicado.</p>
    </div>

    <form id="filtros-form" method="GET" action="{{ route('panel.aplazados') }}" data-opciones-url="{{ route('panel.aplazados.opciones') }}" class="bg-white rounded-xl shadow p-5 mb-6 grid grid-cols-1 md:grid-cols-7 gap-4">
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Año</label>
            <select name="anio" class="w-full border rounded-lg px-3 py-2">
                @foreach($aniosDisponibles as $a)
                    <option value="{{ $a }}" {{ (int)$anio === (int)$a ? 'selected' : '' }}>{{ $a }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Departamento</label>
            <select id="filtro-departamento" name="departamento" class="w-full border rounded-lg px-3 py-2">
                <option value="" {{ $departamento === '' ? 'selected' : '' }}>TODOS</option>
                @foreach($departamentos as $dep)
                    <option value="{{ $dep }}" {{ $departamento === $dep ? 'selected' : '' }}>{{ $dep }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Provincia</label>
            <select id="filtro-provincia" name="provincia" class="w-full border rounded-lg px-3 py-2">
                <option value="" {{ $provincia === '' ? 'selected' : '' }}>TODOS</option>
                @foreach($provincias as $item)
                    <option value="{{ $item }}" {{ $provincia === $item ? 'selected' : '' }}>{{ $item }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Municipio</label>
            <select id="filtro-municipio" name="municipio" class="w-full border rounded-lg px-3 py-2">
                <option value="" {{ $municipio === '' ? 'selected' : '' }}>TODOS</option>
                @foreach($municipios as $item)
                    <option value="{{ $item }}" {{ $municipio === $item ? 'selected' : '' }}>{{ $item }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Distrito</label>
            <select id="filtro-distrito" name="distrito" class="w-full border rounded-lg px-3 py-2">
                <option value="" {{ $distrito === '' ? 'selected' : '' }}>TODOS</option>
                @foreach($distritos as $item)
                    <option value="{{ $item }}" {{ $distrito === $item ? 'selected' : '' }}>{{ $item }}</option>
                @endforeach
            </select>
        </div>
        <div class="md:col-span-2 flex items-end gap-2">
            <button type="submit" class="w-full bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-lg px-4 py-2">Filtrar</button>
            <a href="{{ route('panel.aplazados', ['anio' => $anio]) }}" class="w-full text-center bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold rounded-lg px-4 py-2">Limpiar</a>
        </div>
    </form>

    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow p-4"><div class="text-xs text-gray-500">Colegios visibles</div><div class="text-2xl font-bold">{{ number_format($resumen['total_colegios']) }}</div></div>
        <div class="bg-white rounded-xl shadow p-4"><div class="text-xs text-gray-500">Total aplazados</div><div class="text-2xl font-bold">{{ number_format($resumen['total_aplazados']) }}</div></div>
        <div class="bg-white rounded-xl shadow p-4"><div class="text-xs text-gray-500">Máximo (filtro actual)</div><div class="text-2xl font-bold text-red-700">{{ number_format($resumen['max_aplazados']) }}</div></div>
        <div class="bg-white rounded-xl shadow p-4"><div class="text-xs text-gray-500">Promedio</div><div class="text-2xl font-bold text-orange-500">{{ number_format($resumen['promedio_aplazados'], 2) }}</div></div>
        <div class="bg-white rounded-xl shadow p-4"><div class="text-xs text-gray-500">Mínimo</div><div class="text-2xl font-bold text-green-600">{{ number_format($resumen['min_aplazados']) }}</div></div>
    </div>

    <div class="bg-white rounded-xl shadow p-5 mb-6">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between mb-3">
            <h2 id="mapa-titulo-modo" class="text-lg font-semibold">Mapa de calor de colegios de Bolivia por cantidad de aplazados (año {{ $anio }})</h2>
            <div class="flex flex-wrap items-center gap-3">
                <label class="inline-flex items-center gap-2 text-sm text-gray-700 select-none">
                    <input id="check-mapa-calor" type="checkbox" class="rounded border-gray-300" checked>
                    <span>Mostrar mapa de calor</span>
                </label>
                <button id="btn-fullscreen-mapa" type="button" class="text-sm bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-lg px-3 py-2">
                    Pantalla completa
                </button>
            </div>
        </div>
        <div id="mapa-leyenda">
            <div class="legend-gradient mb-1"></div>
            <div class="flex justify-between text-xs text-gray-600 mb-4">
                <span>0 aplazados (verde)</span>
                <span>Desde 1 (naranja)</span>
                <span>Máximo del filtro (rojo oscuro)</span>
            </div>
        </div>
        <div id="mapa-wrapper">
            <div id="mapa-fullscreen-title">MAPA DE CALOR DE COLEGIOS DE BOLIVIA POR CANTIDAD DE APLAZADOS</div>
            <div id="mapa-aplazados"></div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow p-5 overflow-x-auto">
        <h2 class="text-lg font-semibold mb-3">Top colegios con más aplazados</h2>
        <table class="min-w-full text-sm">
            <thead>
                <tr class="bg-gray-100 text-gray-700">
                    <th class="text-left px-3 py-2">#</th>
                    <th class="text-left px-3 py-2">Colegio</th>
                    <th class="text-left px-3 py-2">RUE</th>
                    <th class="text-left px-3 py-2">Departamento</th>
                    <th class="text-left px-3 py-2">Provincia</th>
                    <th class="text-left px-3 py-2">Municipio</th>
                    <th class="text-left px-3 py-2">Distrito</th>
                    <th class="text-left px-3 py-2">Aplazados</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topEscuelas as $i => $school)
                    <tr class="border-b">
                        <td class="px-3 py-2">{{ $i + 1 }}</td>
                        <td class="px-3 py-2">{{ $school->nombre }}</td>
                        <td class="px-3 py-2">{{ $school->codigo_rue }}</td>
                        <td class="px-3 py-2">{{ $school->departamento }}</td>
                        <td class="px-3 py-2">{{ $school->provincia }}</td>
                        <td class="px-3 py-2">{{ $school->municipio }}</td>
                        <td class="px-3 py-2">{{ $school->distrito }}</td>
                        <td class="px-3 py-2 font-semibold">{{ number_format($school->aplazados) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="px-3 py-6 text-center text-gray-500">No hay datos con esos filtros.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    (function () {
        const schools = @json($schools);
        const maxAplazados = {{ (int) ($resumen['max_aplazados'] ?? 0) }};
        const yearLabel = @json((string)$anio);
        const boliviaFillGeoJsonUrl = @json(asset('storage/geo/distritos_municipales.geojson'));
        const boliviaBorderGeoJsonUrl = @json(asset('geo/bol_limite_nacional_b.geojson'));

        const plainSchoolColor = 'rgb(38,186,165)';
        const boliviaColor = 'rgb(55,95,122)';
        const HEATMAP_TITLE = 'MAPA DE CALOR DE COLEGIOS DE BOLIVIA POR CANTIDAD DE APLAZADOS';
        const SCHOOLS_TITLE = 'COLEGIOS DE BOLIVIA';

        const heatmapCheckbox = document.getElementById('check-mapa-calor');
        const legendContainer = document.getElementById('mapa-leyenda');
        const fullscreenButton = document.getElementById('btn-fullscreen-mapa');
        const mapTitle = document.getElementById('mapa-titulo-modo');
        const fullscreenTitle = document.getElementById('mapa-fullscreen-title');
        const mapWrapper = document.getElementById('mapa-wrapper');

        const map = L.map('mapa-aplazados').setView([-16.5, -64.8], 5.4);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        const markersLayer = L.layerGroup().addTo(map);

        function toNumber(value) {
            const n = Number(value);
            return Number.isFinite(n) ? n : 0;
        }

        function getColor(aplazados) {
            const value = toNumber(aplazados);

            if (value <= 0) {
                return '#16a34a';
            }

            const positiveMax = Math.max(1, toNumber(maxAplazados));
            const ratio = Math.min(1, value / positiveMax);

            const hue = 30 * (1 - ratio);
            const saturation = 95;
            const lightness = 52 - (ratio * 24);

            return `hsl(${hue}, ${saturation}%, ${lightness}%)`;
        }

        function getRadius(aplazados) {
            const value = toNumber(aplazados);
            if (value <= 0) {
                return 4;
            }

            const positiveMax = Math.max(1, toNumber(maxAplazados));
            const ratio = Math.min(1, value / positiveMax);
            return 5 + (ratio * 8);
        }

        function getMarkerStyle(aplazados, useHeatmap) {
            if (!useHeatmap) {
                return {
                    radius: 7,
                    fill: true,
                    stroke: true,
                    color: plainSchoolColor,
                    fillColor: plainSchoolColor,
                    opacity: 1,
                    fillOpacity: 1,
                    weight: 2
                };
            }

            const color = getColor(aplazados);
            return {
                radius: getRadius(aplazados),
                color,
                fillColor: color,
                fillOpacity: 0.62,
                weight: 1
            };
        }

        function renderSchoolMarkers(useHeatmap) {
            markersLayer.clearLayers();

            schools.forEach((school) => {
                const lat = toNumber(school.latitud);
                const lng = toNumber(school.longitud);

                if (!lat || !lng) {
                    return;
                }

                const markerStyle = getMarkerStyle(school.aplazados, useHeatmap);
                L.circleMarker([lat, lng], markerStyle)
                    .bindPopup(
                        `<strong>${school.nombre}</strong><br>` +
                        `RUE: ${school.codigo_rue ?? '-'}<br>` +
                        `Departamento: ${school.departamento ?? '-'}<br>` +
                        `Provincia: ${school.provincia ?? '-'}<br>` +
                        `Municipio: ${school.municipio ?? '-'}<br>` +
                        `Distrito: ${school.distrito ?? '-'}<br>` +
                        `Aplazados: ${toNumber(school.aplazados).toLocaleString()}`
                    )
                    .addTo(markersLayer);
            });

            // Los puntos siempre arriba del relleno de Bolivia.
            markersLayer.eachLayer((layer) => {
                if (layer && typeof layer.bringToFront === 'function') {
                    layer.bringToFront();
                }
            });
        }

        function updateMapMode() {
            const useHeatmap = Boolean(heatmapCheckbox?.checked);
            renderSchoolMarkers(useHeatmap);

            if (legendContainer) {
                legendContainer.style.display = useHeatmap ? '' : 'none';
            }

            if (mapTitle) {
                mapTitle.textContent = useHeatmap
                    ? `Mapa de calor de colegios de Bolivia por cantidad de aplazados (año ${yearLabel})`
                    : `Colegios de Bolivia (año ${yearLabel})`;
            }

            if (fullscreenTitle) {
                fullscreenTitle.textContent = useHeatmap ? HEATMAP_TITLE : SCHOOLS_TITLE;
            }
        }

        async function paintBoliviaOverlay() {
            try {
                const [fillResponse, borderResponse] = await Promise.all([
                    fetch(boliviaFillGeoJsonUrl, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    }),
                    fetch(boliviaBorderGeoJsonUrl, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                ]);

                if (fillResponse.ok) {
                    const fillGeoJson = await fillResponse.json();
                    L.geoJSON(fillGeoJson, {
                        style: {
                            stroke: false,
                            fillColor: boliviaColor,
                            fillOpacity: 0.18
                        },
                        interactive: false
                    }).addTo(map);
                }

                if (borderResponse.ok) {
                    const borderGeoJson = await borderResponse.json();
                    L.geoJSON(borderGeoJson, {
                        style: {
                            color: boliviaColor,
                            opacity: 1,
                            weight: 7,
                            fillOpacity: 0
                        },
                        interactive: false
                    }).addTo(map);
                }

                if (!fillResponse.ok && !borderResponse.ok) {
                    return;
                }

                markersLayer.eachLayer((layer) => {
                    if (layer && typeof layer.bringToFront === 'function') {
                        layer.bringToFront();
                    }
                });
            } catch (e) {
                // noop
            }
        }

        const bounds = [];
        schools.forEach((school) => {
            const lat = toNumber(school.latitud);
            const lng = toNumber(school.longitud);
            if (!lat || !lng) {
                return;
            }
            bounds.push([lat, lng]);
        });

        if (bounds.length > 0) {
            map.fitBounds(bounds, { padding: [30, 30] });
        }

        heatmapCheckbox?.addEventListener('change', updateMapMode);

        fullscreenButton?.addEventListener('click', async () => {
            if (!mapWrapper) {
                return;
            }

            try {
                if (document.fullscreenElement) {
                    await document.exitFullscreen();
                } else {
                    await mapWrapper.requestFullscreen();
                }
                setTimeout(() => map.invalidateSize(), 120);
            } catch (e) {
                // noop
            }
        });

        document.addEventListener('fullscreenchange', () => {
            setTimeout(() => map.invalidateSize(), 120);
        });

        updateMapMode();
        paintBoliviaOverlay();

        const form = document.getElementById('filtros-form');
        const endpoint = form?.dataset?.opcionesUrl;
        const departamentoSelect = document.getElementById('filtro-departamento');
        const provinciaSelect = document.getElementById('filtro-provincia');
        const municipioSelect = document.getElementById('filtro-municipio');
        const distritoSelect = document.getElementById('filtro-distrito');

        async function fetchOptions(departamento = '', provincia = '', municipio = '') {
            if (!endpoint) {
                return { provincias: [], municipios: [], distritos: [] };
            }

            const params = new URLSearchParams({
                departamento,
                provincia,
                municipio
            });

            const response = await fetch(`${endpoint}?${params.toString()}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            if (!response.ok) {
                return { provincias: [], municipios: [], distritos: [] };
            }

            return response.json();
        }

        function fillSelect(select, items, selectedValue = '') {
            if (!select) return;

            const unique = [...new Set((items || []).filter(Boolean))];
            select.innerHTML = '';

            const allOption = document.createElement('option');
            allOption.value = '';
            allOption.textContent = 'TODOS';
            select.appendChild(allOption);

            unique.forEach((item) => {
                const option = document.createElement('option');
                option.value = item;
                option.textContent = item;
                if (selectedValue && item === selectedValue) {
                    option.selected = true;
                }
                select.appendChild(option);
            });

            if (!selectedValue) {
                select.value = '';
            }
        }

        async function refreshCascade(level = 'init') {
            const departamento = departamentoSelect?.value || '';
            let provincia = provinciaSelect?.value || '';
            let municipio = municipioSelect?.value || '';
            let distrito = distritoSelect?.value || '';

            if (level === 'departamento') {
                provincia = '';
                municipio = '';
                distrito = '';
            }

            if (level === 'provincia') {
                municipio = '';
                distrito = '';
            }

            if (level === 'municipio') {
                distrito = '';
            }

            try {
                const dataProv = await fetchOptions(departamento, '', '');
                const provincias = dataProv.provincias || [];
                fillSelect(provinciaSelect, provincias, provincia);

                if (!provincias.includes(provincia)) {
                    provincia = '';
                    if (provinciaSelect) provinciaSelect.value = '';
                }

                const dataMun = await fetchOptions(departamento, provincia, '');
                const municipios = dataMun.municipios || [];
                fillSelect(municipioSelect, municipios, municipio);

                if (!municipios.includes(municipio)) {
                    municipio = '';
                    if (municipioSelect) municipioSelect.value = '';
                }

                const dataDist = await fetchOptions(departamento, provincia, municipio);
                const distritos = dataDist.distritos || [];
                fillSelect(distritoSelect, distritos, distrito);

                if (!distritos.includes(distrito)) {
                    distrito = '';
                    if (distritoSelect) distritoSelect.value = '';
                }
            } catch (e) {
                // noop
            }
        }

        departamentoSelect?.addEventListener('change', async () => {
            await refreshCascade('departamento');
        });

        provinciaSelect?.addEventListener('change', async () => {
            await refreshCascade('provincia');
        });

        municipioSelect?.addEventListener('change', async () => {
            await refreshCascade('municipio');
        });

        refreshCascade('init');
    })();
</script>
@endpush
