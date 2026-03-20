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
    #mapa-fs-controls {
        display: none;
        position: absolute;
        right: 14px;
        bottom: 16px;
        z-index: 1001;
        background: rgba(15, 23, 42, 0.78);
        border: 1px solid rgba(148, 163, 184, 0.45);
        border-radius: 10px;
        padding: 8px;
        color: #f8fafc;
        backdrop-filter: blur(2px);
    }
    #mapa-wrapper:fullscreen #mapa-fs-controls {
        display: block;
    }
    .mapa-fs-grid {
        display: grid;
        grid-template-columns: repeat(3, 42px);
        gap: 6px;
        align-items: center;
        justify-items: center;
    }
    .mapa-fs-btn {
        width: 42px;
        height: 38px;
        border: 1px solid rgba(148, 163, 184, 0.65);
        border-radius: 8px;
        background: rgba(30, 41, 59, 0.9);
        color: #ffffff;
        font-size: 16px;
        line-height: 1;
        cursor: pointer;
    }
    .mapa-fs-btn:hover {
        background: rgba(51, 65, 85, 1);
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
        <p class="text-sm text-gray-600 mt-1">Solo 0 aplazados es verde. Desde 1 aplazado el color va de naranja a rojo oscuro seg�n el m�ximo del filtro aplicado.</p>
    </div>

    <form id="filtros-form" method="GET" action="{{ route('panel.aplazados') }}" data-opciones-url="{{ route('panel.aplazados.opciones') }}" class="bg-white rounded-xl shadow p-5 mb-6 grid grid-cols-1 md:grid-cols-7 gap-4">
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">A�o</label>
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
        <div class="bg-white rounded-xl shadow p-4"><div class="text-xs text-gray-500">M�ximo (filtro actual)</div><div class="text-2xl font-bold text-red-700">{{ number_format($resumen['max_aplazados']) }}</div></div>
        <div class="bg-white rounded-xl shadow p-4"><div class="text-xs text-gray-500">Promedio</div><div class="text-2xl font-bold text-orange-500">{{ number_format($resumen['promedio_aplazados'], 2) }}</div></div>
        <div class="bg-white rounded-xl shadow p-4"><div class="text-xs text-gray-500">M�nimo</div><div class="text-2xl font-bold text-green-600">{{ number_format($resumen['min_aplazados']) }}</div></div>
    </div>

    <div class="bg-white rounded-xl shadow p-5 mb-6">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between mb-3">
            <h2 id="mapa-titulo-modo" class="text-lg font-semibold">Mapa de calor de colegios de Bolivia por cantidad de aplazados (a�o {{ $anio }})</h2>
                        <div class="flex flex-wrap items-center gap-3">
                <label class="inline-flex items-center gap-2 text-sm text-gray-700 select-none">
                    <input id="check-mapa-calor" type="checkbox" class="rounded border-gray-300" checked>
                    <span>Mostrar mapa de calor</span>
                </label>
                <label class="inline-flex items-center gap-2 text-sm text-gray-700 select-none">
                    <input id="check-centros-poblados" type="checkbox" class="rounded border-gray-300">
                    <span>Mostrar centros poblados (por departamento)</span>
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
                <span>M�ximo del filtro (rojo oscuro)</span>
            </div>
        </div>
        <div id="mapa-wrapper">
            <div id="mapa-fullscreen-title">MAPA DE CALOR DE COLEGIOS DE BOLIVIA POR CANTIDAD DE APLAZADOS</div>
            <div id="mapa-aplazados"></div>
            <div id="mapa-fs-controls" aria-label="Controles en pantalla completa">
                <div class="mapa-fs-grid">
                    <button id="btn-zoom-in" type="button" class="mapa-fs-btn" title="Acercar">+</button>
                    <button id="btn-pan-up" type="button" class="mapa-fs-btn" title="Mover arriba">U</button>
                    <button id="btn-zoom-out" type="button" class="mapa-fs-btn" title="Alejar">-</button>
                    <button id="btn-pan-left" type="button" class="mapa-fs-btn" title="Mover izquierda">L</button>
                    <button type="button" class="mapa-fs-btn" disabled style="opacity:.55;cursor:default">�</button>
                    <button id="btn-pan-right" type="button" class="mapa-fs-btn" title="Mover derecha">R</button>
                    <span></span>
                    <button id="btn-pan-down" type="button" class="mapa-fs-btn" title="Mover abajo">D</button>
                    <span></span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow p-5 overflow-x-auto">
        <h2 class="text-lg font-semibold mb-3">Top colegios con m�s aplazados</h2>
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
<script src="https://cdn.jsdelivr.net/npm/@turf/turf@6/turf.min.js"></script>
<script>
    (function () {
        const schools = @json($schools);
        const maxAplazados = {{ (int) ($resumen['max_aplazados'] ?? 0) }};
        const selectedDepartamento = @json($departamento ?? '');
        const selectedProvincia = @json($provincia ?? '');
        const departamentosCatalogo = @json($departamentos ?? []);
        const centrosEndpoint = @json(route('panel.aplazados.centros')); 
        const hasGeoFilter = Boolean(selectedDepartamento || selectedProvincia);

        // Ajusta estos dos valores manualmente para cambiar sensibilidad en fullscreen.
        const FS_ZOOM_STEP = 0.25; // zoom por click
        const FS_PAN_STEP_PX = 120; // pixeles por click

        const BOLIVIA_BOUNDS = L.latLngBounds([[-22.95, -69.75], [-9.55, -57.35]]);

        const map = L.map('mapa-aplazados', { zoomControl: false, zoomSnap: 0.1, zoomDelta: 0.1 });
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        // Vista inicial: Bolivia completa, evita que se vea el mapamundi al cargar.
        map.fitBounds(BOLIVIA_BOUNDS, { padding: [12, 12] });
        map.setMaxBounds(BOLIVIA_BOUNDS.pad(0.35));

        function toNumber(value) {
            const n = Number(value);
            return Number.isFinite(n) ? n : 0;
        }

        function normalizeText(value) {
            return String(value || '')
                .toUpperCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .replace(/[^A-Z]/g, '');
        }

        function sameDepartamento(a, b) {
            const na = normalizeText(a);
            const nb = normalizeText(b);
            if (!na || !nb) return false;
            return na === nb || na.startsWith(nb) || nb.startsWith(na);
        }

        const deptNormSet = new Set((departamentosCatalogo || []).map(normalizeText).filter(Boolean));

        function isDepartamentoValido(name) {
            const norm = normalizeText(name);
            if (!norm) return false;
            if (deptNormSet.has(norm)) return true;
            for (const d of deptNormSet) {
                if (d.startsWith(norm) || norm.startsWith(d)) return true;
            }
            return false;
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

        const schoolsLayer = L.layerGroup().addTo(map);
        const centrosLayer = L.layerGroup().addTo(map);
        let centrosLoaded = false;
        let centrosLoadedDept = '';
        const currentDepartamento = { value: selectedDepartamento || '' };

        function drawSchools() {
            const bounds = [];

            schools.forEach((school) => {
                const lat = toNumber(school.latitud);
                const lng = toNumber(school.longitud);

                if (!lat || !lng) {
                    return;
                }

                bounds.push([lat, lng]);

                const color = getColor(school.aplazados);
                L.circleMarker([lat, lng], {
                    radius: getRadius(school.aplazados),
                    color,
                    fillColor: color,
                    fillOpacity: 0.62,
                    weight: 1
                })
                    .bindPopup(
                        `<strong>${school.nombre}</strong><br>` +
                        `RUE: ${school.codigo_rue ?? '-'}<br>` +
                        `Departamento: ${school.departamento ?? '-'}<br>` +
                        `Provincia: ${school.provincia ?? '-'}<br>` +
                        `Municipio: ${school.municipio ?? '-'}<br>` +
                        `Distrito: ${school.distrito ?? '-'}<br>` +
                        `Aplazados: ${toNumber(school.aplazados).toLocaleString()}`
                    )
                    .addTo(schoolsLayer);
            });

            return bounds;
        }

        async function drawGeoLayers() {
            try {
                const response = await fetch('{{ asset('geo/bol_lim_dpto.json') }}', {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                if (!response.ok) {
                    return null;
                }

                const geo = await response.json();
                const features = (geo.features || []).filter((feature) => {
                    const name = feature?.properties?.name || feature?.properties?.nom_dep || feature?.properties?.NOM_DEP || '';
                    return isDepartamentoValido(name);
                });

                if (!features.length) {
                    return null;
                }

                const fc = {
                    type: 'FeatureCollection',
                    features
                };

                const fillLayer = L.geoJSON(fc, {
                    style: (feature) => {
                        const name = feature?.properties?.name || feature?.properties?.nom_dep || feature?.properties?.NOM_DEP || '';
                        const selected = selectedDepartamento && sameDepartamento(name, selectedDepartamento);
                        const fillOpacity = selectedDepartamento ? (selected ? 0.30 : 0.03) : 0.30;

                        return {
                            color: 'rgb(55,95,122)',
                            weight: 1,
                            fillColor: 'rgb(55,95,122)',
                            fillOpacity
                        };
                    }
                }).addTo(map);

                const departamentalBorders = L.geoJSON(fc, {
                    style: {
                        color: 'rgb(55,95,122)',
                        weight: hasGeoFilter ? 2.6 : 1.9,
                        fillOpacity: 0
                    }
                }).addTo(map);

                try {
                    if (window.turf && typeof turf.dissolve === 'function') {
                        const dissolved = turf.dissolve(fc);
                        if (dissolved) {
                            L.geoJSON(dissolved, {
                                style: {
                                    color: 'rgb(55,95,122)',
                                    weight: hasGeoFilter ? 3.4 : 2.8,
                                    fillOpacity: 0
                                }
                            }).addTo(map);
                        }
                    }
                } catch (e) {
                    L.geoJSON(fc, {
                        style: {
                            color: 'rgb(55,95,122)',
                            weight: hasGeoFilter ? 2.8 : 2.2,
                            fillOpacity: 0
                        }
                    }).addTo(map);
                }

                departamentalBorders.bringToFront();

                return fillLayer.getBounds();
            } catch (e) {
                return null;
            }
        }


        async function drawCentrosPoblados(departamento) {
            if (!departamento) {
                return null;
            }

            try {
                const params = new URLSearchParams({ departamento });
                const response = await fetch(`${centrosEndpoint}?${params.toString()}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                if (!response.ok) {
                    return null;
                }

                const fc = await response.json();
                const features = fc?.features || [];

                if (!features.length) {
                    return null;
                }

                const centrosGeoLayer = L.geoJSON({ type: 'FeatureCollection', features }, {
                    pointToLayer: (feature, latlng) => L.circleMarker(latlng, {
                        radius: 2.2,
                        color: '#1d4ed8',
                        weight: 0.6,
                        fillColor: '#38bdf8',
                        fillOpacity: 0.72,
                        renderer: L.canvas()
                    }),
                    onEachFeature: (feature, layer) => {
                        const props = feature?.properties || {};
                        const nombre = props.etiqueta || 'Centro poblado';
                        const dep = props.nom_dep || '-';
                        layer.bindPopup(`<strong>${nombre}</strong><br>Departamento: ${dep}`);
                    }
                }).addTo(centrosLayer);

                return centrosGeoLayer.getBounds();
            } catch (e) {
                return null;
            }
        }
        async function initMap() {
            const schoolBounds = drawSchools();
            const geoBounds = await drawGeoLayers();
            const centrosBounds = null;

            const hasSchoolBounds = schoolBounds.length > 0;
            const schoolLatLngBounds = hasSchoolBounds ? L.latLngBounds(schoolBounds) : null;

            let finalBounds = null;

            if (geoBounds && geoBounds.isValid()) {
                finalBounds = geoBounds;
            }

            if (centrosBounds && centrosBounds.isValid()) {
                finalBounds = finalBounds ? finalBounds.extend(centrosBounds) : centrosBounds;
            }

            if (schoolLatLngBounds && schoolLatLngBounds.isValid()) {
                finalBounds = finalBounds ? finalBounds.extend(schoolLatLngBounds) : schoolLatLngBounds;
            }

            if (finalBounds && finalBounds.isValid()) {
                map.fitBounds(finalBounds, { padding: [24, 24] });
            } else {
                map.fitBounds(BOLIVIA_BOUNDS, { padding: [12, 12] });
            }
        }

        initMap();

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
            currentDepartamento.value = departamento;
            let provincia = provinciaSelect?.value || '';
            let municipio = municipioSelect?.value || '';
            let distrito = distritoSelect?.value || '';

            if (level === 'departamento') {
                provincia = '';
                municipio = '';
                distrito = '';
                centrosLoaded = false;
                centrosLoadedDept = '';
                centrosLayer.clearLayers();
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
            if (checkCentrosPoblados?.checked) {
                await setCentrosVisible(true);
            }
        });

        provinciaSelect?.addEventListener('change', async () => {
            await refreshCascade('provincia');
        });

        municipioSelect?.addEventListener('change', async () => {
            await refreshCascade('municipio');
        });

        const wrapper = document.getElementById('mapa-wrapper');
        const btnFullscreen = document.getElementById('btn-fullscreen-mapa');
        const checkMapaCalor = document.getElementById('check-mapa-calor');
        const checkCentrosPoblados = document.getElementById('check-centros-poblados');

        const btnZoomIn = document.getElementById('btn-zoom-in');
        const btnZoomOut = document.getElementById('btn-zoom-out');
        const btnPanUp = document.getElementById('btn-pan-up');
        const btnPanDown = document.getElementById('btn-pan-down');
        const btnPanLeft = document.getElementById('btn-pan-left');
        const btnPanRight = document.getElementById('btn-pan-right');

        function setHeatmapVisible(visible) {
            if (visible) {
                if (!map.hasLayer(schoolsLayer)) {
                    map.addLayer(schoolsLayer);
                }
            } else {
                if (map.hasLayer(schoolsLayer)) {
                    map.removeLayer(schoolsLayer);
                }
            }
        }


        async function setCentrosVisible(visible) {
            if (!visible) {
                if (map.hasLayer(centrosLayer)) {
                    map.removeLayer(centrosLayer);
                }
                return;
            }

            const departamento = currentDepartamento.value || '';
            if (!departamento) {
                return;
            }

            if (!centrosLoaded || centrosLoadedDept !== departamento) {
                centrosLayer.clearLayers();
                await drawCentrosPoblados(departamento);
                centrosLoaded = true;
                centrosLoadedDept = departamento;
            }

            if (!map.hasLayer(centrosLayer)) {
                map.addLayer(centrosLayer);
            }
        }
        checkMapaCalor?.addEventListener('change', () => {
            setHeatmapVisible(checkMapaCalor.checked);
        });
        setHeatmapVisible(checkMapaCalor?.checked !== false);

        checkCentrosPoblados?.addEventListener('change', async () => {
            await setCentrosVisible(checkCentrosPoblados.checked);
        });
        setCentrosVisible(checkCentrosPoblados?.checked === true);

        async function toggleFullscreen() {
            if (!wrapper) return;

            if (document.fullscreenElement === wrapper) {
                await document.exitFullscreen();
            } else {
                await wrapper.requestFullscreen();
            }
        }

        btnFullscreen?.addEventListener('click', async () => {
            try {
                await toggleFullscreen();
            } catch (e) {
                // noop
            }
        });

        document.addEventListener('fullscreenchange', () => {
            const isFs = document.fullscreenElement === wrapper;
            if (btnFullscreen) {
                btnFullscreen.textContent = isFs ? 'Salir pantalla completa' : 'Pantalla completa';
            }
            map.invalidateSize();
        });

        btnZoomIn?.addEventListener('click', () => {
            map.setZoom(map.getZoom() + FS_ZOOM_STEP);
        });

        btnZoomOut?.addEventListener('click', () => {
            map.setZoom(map.getZoom() - FS_ZOOM_STEP);
        });

        btnPanUp?.addEventListener('click', () => map.panBy([0, -FS_PAN_STEP_PX], { animate: true, duration: 0.25 }));
        btnPanDown?.addEventListener('click', () => map.panBy([0, FS_PAN_STEP_PX], { animate: true, duration: 0.25 }));
        btnPanLeft?.addEventListener('click', () => map.panBy([-FS_PAN_STEP_PX, 0], { animate: true, duration: 0.25 }));
        btnPanRight?.addEventListener('click', () => map.panBy([FS_PAN_STEP_PX, 0], { animate: true, duration: 0.25 }));

        refreshCascade('init');
    })();
</script>
@endpush
