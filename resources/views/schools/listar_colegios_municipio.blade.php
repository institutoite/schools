{{-- filepath: c:\xampp\htdocs\schools\resources\views\schools\listar_colegios_municipio.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Colegios por municipio</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/vistas/schools/listar-municipios.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Leaflet CSS para el mapa -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <!-- Select2 CSS para el select con búsqueda -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        /* Modal básico para el mapa */
        .modal-mapa {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0; top: 0; width: 100vw; height: 100vh;
            background: rgba(0,0,0,0.5);
            justify-content: center; align-items: center;
        }
        .modal-mapa.active { display: flex; }
        .modal-content-mapa {
            background: #fff;
            border-radius: 12px;
            padding: 16px;
            position: relative;
            width: 90vw; max-width: 500px;
            box-shadow: 0 2px 12px rgba(55,95,122,0.12);
        }
        #mapa-colegio { width: 100%; height: 350px; border-radius: 8px; }
        .close-mapa {
            position: absolute; top: 8px; right: 12px;
            background: none; border: none; font-size: 1.5em; color: #333; cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="titulo">Listado de colegios por municipio</h1>
        <form method="GET" class="form-municipios">
            <div class="campo">
                <label for="municipio">Municipio</label>
                <select name="municipio" id="municipio" style="width:100%">
                    <option value="">Seleccione municipio</option>
                    @foreach($municipios as $m)
                        <option value="{{ $m }}" {{ ($municipio ?? '') == $m ? 'selected' : '' }}>{{ $m }}</option>
                    @endforeach
                </select>
            </div>
            <div class="campo">
                <label for="colegio">Colegio</label>
                <select name="colegio" id="colegio" style="width:100%" disabled>
                    <option value="">Seleccione colegio</option>
                </select>
            </div>
            <input type="hidden" name="orderBy" value="{{ $orderBy }}">
            <input type="hidden" name="orderDir" value="{{ $orderDir }}">
            <div class="campo">
                <button type="submit" class="btn-comparar">
                    Listar colegios
                </button>
            </div>
        </form>

        @if($municipio && count($colegios))
        <div class="tabla-container">
            <table class="tabla-municipios">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>
                            Colegio
                            <a href="?municipio={{ $municipio }}&orderBy=nombre&orderDir={{ $orderBy == 'nombre' && $orderDir == 'desc' ? 'asc' : 'desc' }}">
                                <i class="fa fa-sort{{ $orderBy == 'nombre' ? ($orderDir == 'desc' ? '-down' : '-up') : '' }}"></i>
                            </a>
                        </th>
                        <th>
                            Dependencia
                            <a href="?municipio={{ $municipio }}&orderBy=dependencia&orderDir={{ $orderBy == 'dependencia' && $orderDir == 'desc' ? 'asc' : 'desc' }}">
                                <i class="fa fa-sort{{ $orderBy == 'dependencia' ? ($orderDir == 'desc' ? '-down' : '-up') : '' }}"></i>
                            </a>
                        </th>
                        <th>
                            Matriculados
                            <a href="?municipio={{ $municipio }}&orderBy=matriculados&orderDir={{ $orderBy == 'matriculados' && $orderDir == 'desc' ? 'asc' : 'desc' }}">
                                <i class="fa fa-sort{{ $orderBy == 'matriculados' ? ($orderDir == 'desc' ? '-down' : '-up') : '' }}"></i>
                            </a>
                        </th>
                        <th>
                            Abandono
                            <a href="?municipio={{ $municipio }}&orderBy=abandono&orderDir={{ $orderBy == 'abandono' && $orderDir == 'desc' ? 'asc' : 'desc' }}">
                                <i class="fa fa-sort{{ $orderBy == 'abandono' ? ($orderDir == 'desc' ? '-down' : '-up') : '' }}"></i>
                            </a>
                        </th>
                        <th>
                            Reprobados
                            <a href="?municipio={{ $municipio }}&orderBy=reprobados&orderDir={{ $orderBy == 'reprobados' && $orderDir == 'desc' ? 'asc' : 'desc' }}">
                                <i class="fa fa-sort{{ $orderBy == 'reprobados' ? ($orderDir == 'desc' ? '-down' : '-up') : '' }}"></i>
                            </a>
                        </th>
                        <th>
                            Aprobados
                            <a href="?municipio={{ $municipio }}&orderBy=aprobados&orderDir={{ $orderBy == 'aprobados' && $orderDir == 'desc' ? 'asc' : 'desc' }}">
                                <i class="fa fa-sort{{ $orderBy == 'aprobados' ? ($orderDir == 'desc' ? '-down' : '-up') : '' }}"></i>
                            </a>
                        </th>
                        <th>Ubicación</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($colegios as $i => $c)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $c['nombre'] }}</td>
                            <td>{{ ucfirst($c['dependencia']) }}</td>
                            <td>{{ $c['matriculados'] }}</td>
                            <td>{{ $c['abandono'] }}</td>
                            <td>{{ $c['reprobados'] }}</td>
                            <td>{{ $c['aprobados'] }}</td>
                            <td>
                                @if($c['lat'] && $c['lng'])
                                    <a href="#" class="btn-mapa" data-lat="{{ $c['lat'] }}" data-lng="{{ $c['lng'] }}" data-nombre="{{ $c['nombre'] }}" title="Ver ubicación">
                                        <i class="fa fa-map-marker-alt" style="color:rgb(38,186,165);font-size:1.2em;"></i>
                                    </a>
                                @else
                                    <span style="color:#ccc;"><i class="fa fa-map-marker-alt"></i></span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background:rgb(38,186,165);color:#fff;font-weight:bold">
                        <td colspan="3">Totales</td>
                        <td>{{ $totales['matriculados'] }}</td>
                        <td>{{ $totales['abandono'] }}</td>
                        <td>{{ $totales['reprobados'] }}</td>
                        <td>{{ $totales['aprobados'] }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
            <div style="margin-top:16px;font-weight:bold;">
                Total de colegios: {{ count($colegios) }}
            </div>
        </div>
        @elseif($municipio)
            <p>No se encontraron colegios para el municipio seleccionado.</p>
        @endif
    </div>

    <!-- Modal para mostrar el mapa -->
    <div class="modal-mapa" id="modalMapa">
        <div class="modal-content-mapa">
            <button class="close-mapa" id="cerrarMapa" title="Cerrar">&times;</button>
            <h3 id="nombreColegioMapa" style="margin-bottom:10px;"></h3>
            <div id="mapa-colegio"></div>
        </div>
    </div>

    <!-- Leaflet JS para el mapa -->
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <!-- Select2 JS para el select con búsqueda -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
    let leafletMap = null; // Variable global para el mapa

    document.querySelectorAll('.btn-mapa').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            var lat = btn.getAttribute('data-lat');
            var lng = btn.getAttribute('data-lng');
            var nombre = btn.getAttribute('data-nombre');
            document.getElementById('nombreColegioMapa').textContent = nombre;
            document.getElementById('modalMapa').classList.add('active');

            setTimeout(function() {
                var mapDiv = document.getElementById('mapa-colegio');
                // Si ya existe un mapa, elimínalo
                if (leafletMap) {
                    leafletMap.remove();
                    leafletMap = null;
                }
                mapDiv.innerHTML = ""; // Limpiar el div

                // Crear el nuevo mapa
                leafletMap = L.map('mapa-colegio').setView([lat, lng], 16);

                // Capas de mapa
                var callejero = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '© OpenStreetMap'
                });

                var satelital = L.tileLayer('https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
                    maxZoom: 20,
                    subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
                    attribution: '© Google Maps'
                });

                var hibrido = L.tileLayer('https://{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
                    maxZoom: 20,
                    subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
                    attribution: '© Google Maps'
                });

                // Agregar capa inicial (callejero)
                callejero.addTo(leafletMap);

                // Control de capas
                var baseMaps = {
                    "Callejero": callejero,
                    "Satelital": satelital,
                    "Híbrido": hibrido
                };

                L.control.layers(baseMaps).addTo(leafletMap);

                // Agregar marcador
                L.marker([lat, lng]).addTo(leafletMap)
                    .bindPopup(nombre)
                    .openPopup();
            }, 200);
        });
    });

    document.getElementById('cerrarMapa').addEventListener('click', function() {
        document.getElementById('modalMapa').classList.remove('active');
        // Eliminar el mapa al cerrar el modal
        if (leafletMap) {
            leafletMap.remove();
            leafletMap = null;
        }
    });

    document.getElementById('modalMapa').addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.remove('active');
            if (leafletMap) {
                leafletMap.remove();
                leafletMap = null;
            }
        }
    });

    $(document).ready(function () {
        // Inicializar Select2 en ambos selects
        $('#municipio').select2({
            placeholder: "Seleccione municipio",
            allowClear: true,
            width: 'resolve'
        });

        $('#colegio').select2({
            placeholder: "Seleccione colegio",
            allowClear: true,
            width: 'resolve'
        });

        // Evento para cargar colegios cuando se selecciona un municipio
        $('#municipio').on('change', function () {
            const municipio = $(this).val();
            const colegioSelect = $('#colegio');

            if (municipio) {
                // Hacer la solicitud AJAX para obtener los colegios
                $.ajax({
                    url: '{{ route("colegios-por-municipio") }}',
                    type: 'GET',
                    data: { municipio: municipio },
                    success: function (data) {
                        // Limpiar y habilitar el select de colegios
                        colegioSelect.empty().prop('disabled', false);
                        colegioSelect.append('<option value="">Seleccione colegio</option>');

                        // Agregar las opciones al select
                        $.each(data, function (id, nombre) {
                            colegioSelect.append(new Option(nombre, id));
                        });

                        // Refrescar Select2
                        colegioSelect.trigger('change');
                    },
                    error: function () {
                        alert('Error al cargar los colegios.');
                    }
                });
            } else {
                // Si no hay municipio seleccionado, deshabilitar el select de colegios
                colegioSelect.empty().prop('disabled', true);
                colegioSelect.append('<option value="">Seleccione colegio</option>');
            }
        });
    });
    </script>
</body>
</html>