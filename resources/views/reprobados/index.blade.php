<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estadísticas de Reprobados</title>
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" crossorigin="anonymous" />
</head>
<body class="bg-accent min-h-screen font-sans">
    <div class="max-w-5xl mx-auto py-10 px-4">
        <form method="GET" class="mb-8 flex flex-col md:flex-row md:items-end gap-4">
            <div>
                <label for="anio" class="block text-secondary font-semibold mb-1">
                    <i class="fas fa-calendar"></i> Año
                </label>
                <select id="anio" name="anio" class="py-2 px-4 rounded-lg border border-primary/30 bg-primary/5 text-secondary w-full focus:ring-2 focus:ring-primary outline-none transition">
                    @foreach($aniosDisponibles as $a)
                        <option value="{{ $a }}" {{ $anio == $a ? 'selected' : '' }}>{{ $a }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="departamento" class="block text-secondary font-semibold mb-1">
                    <i class="fas fa-map-marker-alt"></i> Departamento
                </label>
                <select id="departamento" name="departamento" class="py-2 px-4 rounded-lg border border-primary/30 bg-primary/5 text-secondary w-full focus:ring-2 focus:ring-primary outline-none transition">
                    <option value="">Todo el país</option>
                    @foreach($departamentos as $dep)
                        <option value="{{ $dep }}" {{ $departamento == $dep ? 'selected' : '' }}>{{ $dep }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <button type="submit" class="bg-primary hover:bg-secondary text-white font-bold py-2 px-6 rounded-lg shadow transition flex items-center gap-2">
                    <i class="fas fa-search"></i> Filtrar
                </button>
            </div>
        </form>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            <div class="bg-white rounded-xl shadow p-6 flex flex-col items-center">
                <span class="text-secondary text-lg font-semibold mb-2">Promedio General</span>
                <span class="text-4xl font-bold text-primary">{{ $promedioNacional }}%</span>
                <span class="text-xs text-gray-500 mt-1">Año: {{ $anio }}</span>
            </div>
            <div class="bg-white rounded-xl shadow p-6 flex flex-col items-center">
                <span class="text-secondary text-lg font-semibold mb-2">Promedio Mujeres</span>
                <span class="text-4xl font-bold text-primary">{{ $promedioMujer }}%</span>
                <span class="text-xs text-gray-500 mt-1">Año: {{ $anio }}</span>
            </div>
            <div class="bg-white rounded-xl shadow p-6 flex flex-col items-center">
                <span class="text-secondary text-lg font-semibold mb-2">Promedio Hombres</span>
                <span class="text-4xl font-bold text-primary">{{ $promedioHombre }}%</span>
                <span class="text-xs text-gray-500 mt-1">Año: {{ $anio }}</span>
            </div>
            <div class="bg-white rounded-xl shadow p-6 flex flex-col items-center">
                <span class="text-secondary text-lg font-semibold mb-2">Reprobación por Área</span>
                <div class="flex flex-col gap-1">
                    <span class="text-primary font-bold">Urbana: {{ $reprobacionPorArea['URBANA'] ?? '—' }}%</span>
                    <span class="text-primary font-bold">Rural: {{ $reprobacionPorArea['RURAL'] ?? '—' }}%</span>
                </div>
                <span class="text-xs text-gray-500 mt-1">Año: {{ $anio }}</span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
            <div class="bg-white rounded-xl shadow p-6">
                <h2 class="text-xl font-bold text-secondary mb-2 flex items-center gap-2"><i class="fas fa-arrow-up"></i> Colegio con más reprobados</h2>
                @if($colegioMax)
                    <div class="text-lg font-semibold text-primary">COLEGIO:{{ $colegioMax->nombre ?? 'N/A' }}</div>
                    <div class="text-secondary">Código: <span class="font-bold">{{ $colegioMax->codigo_rue ?? 'N/A' }}</span></div>
                    <div class="text-secondary">Reprobados: <span class="font-bold">{{ $colegioMax->estadisticas->sum('total') }}</span></div>
                    <div class="text-secondary">Departamento: <span class="font-bold">{{ $colegioMax->ubicacion->departamento }}</span></div>
                    <div class="text-secondary">Provincia: <span class="font-bold">{{ $colegioMax->ubicacion->provincia }}</span></div>
                    <div class="text-secondary">Municipio: <span class="font-bold">{{ $colegioMax->ubicacion->municipio }}</span></div>
                    <div class="text-secondary">Distrito: <span class="font-bold">{{ $colegioMax->ubicacion->distrito }}</span></div>
                    <div class="text-secondary">Area: <span class="font-bold">{{ $colegioMax->ubicacion->area }}</span></div>
                    <div class="text-secondary">Probabilidad: <span class="font-bold">{{ $colegioMax->probabilidad ?? '—' }}%</span></div>
                @else
                    <div class="text-gray-500">No hay datos</div>
                @endif
            </div>
            <div class="bg-white rounded-xl shadow p-6">
                <h2 class="text-xl font-bold text-secondary mb-2 flex items-center gap-2"><i class="fas fa-arrow-down"></i> Colegio con menos reprobados</h2>
                @if($colegioMin)
                    <div class="text-lg font-semibold text-primary">COLEGIO:{{ $colegioMin->nombre ?? 'N/A' }}</div>
                    <div class="text-secondary">Código: <span class="font-bold">{{ $colegioMin->codigo_rue ?? 'N/A' }}</span></div>
                    <div class="text-secondary">Reprobados: <span class="font-bold">{{ $colegioMin->estadisticas->sum('total') }}</span></div>
                    <div class="text-secondary">Departamento: <span class="font-bold">{{ $colegioMax->ubicacion->departamento }}</span></div>
                    <div class="text-secondary">Provincia: <span class="font-bold">{{ $colegioMax->ubicacion->provincia }}</span></div>
                    <div class="text-secondary">Municipio: <span class="font-bold">{{ $colegioMax->ubicacion->municipio }}</span></div>
                    <div class="text-secondary">Distrito: <span class="font-bold">{{ $colegioMax->ubicacion->distrito }}</span></div>
                    <div class="text-secondary">Area: <span class="font-bold">{{ $colegioMax->ubicacion->area }}</span></div>
                    <div class="text-secondary">Probabilidad: <span class="font-bold">{{ $colegioMin->probabilidad ?? '—' }}%</span></div>
                @else
                    <div class="text-gray-500">No hay datos</div>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
            <div class="bg-white rounded-xl shadow p-6 overflow-x-auto">
                <h2 class="text-xl font-bold text-primary mb-4 flex items-center gap-2"><i class="fas fa-list-ol"></i> Top 10 colegios con más reprobados</h2>
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-primary text-white">
                            <th class="py-2 px-2">#</th>
                            <th class="py-2 px-2">Nombre</th>
                            <th class="py-2 px-2">RUE</th>
                            <th class="py-2 px-2">Reprobados</th>
                            <th class="py-2 px-2">Probabilidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($top10 as $i => $school)
                            <tr class="border-b hover:bg-primary/10 transition">
                                <td class="py-1 px-2 font-bold">{{ $loop->index+1 }}</td>
                                <td class="py-1 px-2">{{ $school->nombre }}</td>
                                <td class="py-1 px-2">{{ $school->codigo_rue }}</td>
                                <td class="py-1 px-2">{{ $school->estadisticas->sum('total') }}</td>
                                <td class="py-1 px-2">{{ $school->probabilidad ?? '—' }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="bg-white rounded-xl shadow p-6 overflow-x-auto">
                <h2 class="text-xl font-bold text-primary mb-4 flex items-center gap-2"><i class="fas fa-list-ol"></i> Top 10 colegios con menos reprobados</h2>
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-primary text-white">
                            <th class="py-2 px-2">#</th>
                            <th class="py-2 px-2">Nombre</th>
                            <th class="py-2 px-2">RUE</th>
                            <th class="py-2 px-2">Reprobados</th>
                            <th class="py-2 px-2">Probabilidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bottom10 as $i => $school)
                            <tr class="border-b hover:bg-primary/10 transition">
                                <td class="py-1 px-2 font-bold">{{ $loop->index+1 }}</td>
                                <td class="py-1 px-2">{{ $school->nombre }}</td>
                                <td class="py-1 px-2">{{ $school->codigo_rue }}</td>
                                <td class="py-1 px-2">{{ $school->estadisticas->sum('total') }}</td>
                                <td class="py-1 px-2">{{ $school->probabilidad ?? '—' }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-primary/10 rounded-xl p-6 text-center shadow mb-8">
            <span class="text-secondary text-lg font-semibold">¿Quieres ver más detalles o gráficos?</span>
            <div class="mt-4 flex flex-col md:flex-row gap-4 justify-center">
                <a href="/probabilidad" class="bg-primary hover:bg-secondary text-white font-bold py-2 px-6 rounded-lg shadow transition flex items-center gap-2">
                    <i class="fas fa-chart-line"></i> Ver ranking de colegios
                </a>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow p-6 mt-8">
            <h2 class="text-xl font-bold text-secondary mb-2 flex items-center gap-2"><i class="fas fa-map"></i> Mapa de calor de reprobación</h2>
            <div id="map" style="height: 500px; width: 100%;" class="rounded-xl mb-4"></div>
            <div class="text-gray-500">Los puntos muestran todos los colegios del filtro. El color y tamaño indican la cantidad de reprobados.x</div>
        </div>
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        {{-- Tablas y gráficas de totales --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
            <div class="bg-white rounded-xl shadow p-6 overflow-x-auto">
                <h2 class="text-xl font-bold text-primary mb-4 flex items-center gap-2">
                    <i class="fas fa-table"></i> Totales de reprobados
                </h2>
                <table class="min-w-full text-sm mb-4">
                    <thead>
                        <tr class="bg-primary text-white">
                            <th class="py-2 px-2">{{ !$departamento ? 'Departamento' : 'Provincia' }}</th>
                            <th class="py-2 px-2">Reprobados</th>
                            <th class="py-2 px-2">Matrícula</th>
                            <th class="py-2 px-2">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tablaTotales as $row)
                            <tr class="border-b hover:bg-primary/10 transition">
                                <td class="py-1 px-2">{{ $row['nombre'] }}</td>
                                <td class="py-1 px-2">{{ $row['reprobados'] }}</td>
                                <td class="py-1 px-2">{{ $row['matricula'] }}</td>
                                <td class="py-1 px-2">{{ $row['porcentaje'] }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <canvas id="chartTotales" height="180"></canvas>
            </div>
            <div class="bg-white rounded-xl shadow p-6 overflow-x-auto">
                <h2 class="text-xl font-bold text-primary mb-4 flex items-center gap-2">
                    <i class="fas fa-table"></i> Reprobados por género
                </h2>
                <table class="min-w-full text-sm mb-4">
                    <thead>
                        <tr class="bg-primary text-white">
                            <th class="py-2 px-2">Género</th>
                            <th class="py-2 px-2">Reprobados</th>
                            <th class="py-2 px-2">Matrícula</th>
                            <th class="py-2 px-2">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tablaGenero as $row)
                            <tr class="border-b hover:bg-primary/10 transition">
                                <td class="py-1 px-2">{{ $row['nombre'] }}</td>
                                <td class="py-1 px-2">{{ $row['reprobados'] }}</td>
                                <td class="py-1 px-2">{{ $row['matricula'] }}</td>
                                <td class="py-1 px-2">{{ $row['porcentaje'] }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <canvas id="chartGenero" height="180"></canvas>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
            <div class="bg-white rounded-xl shadow p-6 overflow-x-auto">
                <h2 class="text-xl font-bold text-primary mb-4 flex items-center gap-2">
                    <i class="fas fa-table"></i> Reprobados por área
                </h2>
                <table class="min-w-full text-sm mb-4">
                    <thead>
                        <tr class="bg-primary text-white">
                            <th class="py-2 px-2">Área</th>
                            <th class="py-2 px-2">Reprobados</th>
                            <th class="py-2 px-2">Matrícula</th>
                            <th class="py-2 px-2">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tablaArea as $row)
                            <tr class="border-b hover:bg-primary/10 transition">
                                <td class="py-1 px-2">{{ $row['nombre'] }}</td>
                                <td class="py-1 px-2">{{ $row['reprobados'] }}</td>
                                <td class="py-1 px-2">{{ $row['matricula'] }}</td>
                                <td class="py-1 px-2">{{ $row['porcentaje'] }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <canvas id="chartArea" height="180"></canvas>
            </div>
            @if($departamento && !empty($tablaMunicipios))
            <div class="bg-white rounded-xl shadow p-6 overflow-x-auto">
                <h2 class="text-xl font-bold text-primary mb-4 flex items-center gap-2">
                    <i class="fas fa-table"></i> Reprobados por municipio
                </h2>
                <table class="min-w-full text-sm mb-4">
                    <thead>
                        <tr class="bg-primary text-white">
                            <th class="py-2 px-2">Municipio</th>
                            <th class="py-2 px-2">Reprobados</th>
                            <th class="py-2 px-2">Matrícula</th>
                            <th class="py-2 px-2">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tablaMunicipios as $row)
                            <tr class="border-b hover:bg-primary/10 transition">
                                <td class="py-1 px-2">{{ $row['nombre'] }}</td>
                                <td class="py-1 px-2">{{ $row['reprobados'] }}</td>
                                <td class="py-1 px-2">{{ $row['matricula'] }}</td>
                                <td class="py-1 px-2">{{ $row['porcentaje'] }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <canvas id="chartMunicipios" height="180"></canvas>
            </div>
            @endif
        </div>
        @if($departamento && !empty($tablaDistritos))
        <div class="bg-white rounded-xl shadow p-6 overflow-x-auto mb-10">
            <h2 class="text-xl font-bold text-primary mb-4 flex items-center gap-2">
                <i class="fas fa-table"></i> Reprobados por distrito
            </h2>
            <table class="min-w-full text-sm mb-4">
                <thead>
                    <tr class="bg-primary text-white">
                        <th class="py-2 px-2">Distrito</th>
                        <th class="py-2 px-2">Reprobados</th>
                        <th class="py-2 px-2">Matrícula</th>
                        <th class="py-2 px-2">%</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tablaDistritos as $row)
                        <tr class="border-b hover:bg-primary/10 transition">
                            <td class="py-1 px-2">{{ $row['nombre'] }}</td>
                            <td class="py-1 px-2">{{ $row['reprobados'] }}</td>
                            <td class="py-1 px-2">{{ $row['matricula'] }}</td>
                            <td class="py-1 px-2">{{ $row['porcentaje'] }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <canvas id="chartDistritos" height="180"></canvas>
        </div>
        @endif
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
        // Gráfica de totales (departamento/provincia)
        new Chart(document.getElementById('chartTotales'), {
            type: 'bar',
            data: {
                labels: {!! json_encode(array_column($tablaTotales, 'nombre')) !!},
                datasets: [{
                    label: 'Reprobados',
                    data: {!! json_encode(array_column($tablaTotales, 'reprobados')) !!},
                    backgroundColor: 'rgba(38,186,165,0.7)'
                },{
                    label: 'Porcentaje %',
                    data: {!! json_encode(array_column($tablaTotales, 'porcentaje')) !!},
                    backgroundColor: 'rgba(55,95,122,0.5)'
                }]
            },
            options: {responsive:true, plugins:{legend:{display:true}}}
        });
        // Gráfica de género
        new Chart(document.getElementById('chartGenero'), {
            type: 'bar',
            data: {
                labels: {!! json_encode(array_column($tablaGenero, 'nombre')) !!},
                datasets: [{
                    label: 'Reprobados',
                    data: {!! json_encode(array_column($tablaGenero, 'reprobados')) !!},
                    backgroundColor: 'rgba(38,186,165,0.7)'
                },{
                    label: 'Porcentaje %',
                    data: {!! json_encode(array_column($tablaGenero, 'porcentaje')) !!},
                    backgroundColor: 'rgba(55,95,122,0.5)'
                }]
            },
            options: {responsive:true, plugins:{legend:{display:true}}}
        });
        // Gráfica de área
        new Chart(document.getElementById('chartArea'), {
            type: 'bar',
            data: {
                labels: {!! json_encode(array_column($tablaArea, 'nombre')) !!},
                datasets: [{
                    label: 'Reprobados',
                    data: {!! json_encode(array_column($tablaArea, 'reprobados')) !!},
                    backgroundColor: 'rgba(38,186,165,0.7)'
                },{
                    label: 'Porcentaje %',
                    data: {!! json_encode(array_column($tablaArea, 'porcentaje')) !!},
                    backgroundColor: 'rgba(55,95,122,0.5)'
                }]
            },
            options: {responsive:true, plugins:{legend:{display:true}}}
        });
        // Gráfica de municipios
        @if($departamento && !empty($tablaMunicipios))
        new Chart(document.getElementById('chartMunicipios'), {
            type: 'bar',
            data: {
                labels: {!! json_encode(array_column($tablaMunicipios, 'nombre')) !!},
                datasets: [{
                    label: 'Reprobados',
                    data: {!! json_encode(array_column($tablaMunicipios, 'reprobados')) !!},
                    backgroundColor: 'rgba(38,186,165,0.7)'
                },{
                    label: 'Porcentaje %',
                    data: {!! json_encode(array_column($tablaMunicipios, 'porcentaje')) !!},
                    backgroundColor: 'rgba(55,95,122,0.5)'
                }]
            },
            options: {responsive:true, plugins:{legend:{display:true}}}
        });
        @endif
        // Gráfica de distritos
        @if($departamento && !empty($tablaDistritos))
        new Chart(document.getElementById('chartDistritos'), {
            type: 'bar',
            data: {
                labels: {!! json_encode(array_column($tablaDistritos, 'nombre')) !!},
                datasets: [{
                    label: 'Reprobados',
                    data: {!! json_encode(array_column($tablaDistritos, 'reprobados')) !!},
                    backgroundColor: 'rgba(38,186,165,0.7)'
                },{
                    label: 'Porcentaje %',
                    data: {!! json_encode(array_column($tablaDistritos, 'porcentaje')) !!},
                    backgroundColor: 'rgba(55,95,122,0.5)'
                }]
            },
            options: {responsive:true, plugins:{legend:{display:true}}}
        });
        @endif
        </script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var map = L.map('map').setView([-16.2902, -63.5887], 5.2); // Centro de Bolivia
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 18,
                    attribution: '© OpenStreetMap contributors'
                }).addTo(map);
                var colegios = @json($colegiosMapa);
                function getColor(reprobados) {
                    if (reprobados >= 100) return '#e74c3c';
                    if (reprobados >= 50) return '#f39c12';
                    if (reprobados >= 10) return '#f1c40f';
                    return '#27ae60';
                }
                function getRadius(reprobados) {
                    if (reprobados >= 100) return 10;
                    if (reprobados >= 50) return 8;
                    if (reprobados >= 10) return 6;
                    return 4;
                }
                colegios.forEach(function(school) {
                    if(school && school.lat && school.lng) {
                        var marker = L.circleMarker([school.lat, school.lng], {
                            radius: getRadius(school.reprobados),
                            color: getColor(school.reprobados),
                            fillColor: getColor(school.reprobados),
                            fillOpacity: 0.35
                        }).addTo(map);
                        marker.bindPopup('<b>' + school.nombre + '</b><br>RUE: ' + school.rue + '<br>Reprobados: ' + school.reprobados + '<br>Probabilidad: ' + school.probabilidad + '%<br>Área: ' + school.area);
                    }
                });
            });
        </script>
        
</body>
</html> 