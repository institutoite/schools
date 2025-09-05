<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comparar Municipios</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- CSS personalizado -->
    <link rel="stylesheet" href="{{ asset('css/comparar-municipios.css') }}">
</head>
<body>
    <div class="container">
        <h1 class="titulo">Comparar dos municipios</h1>
        <form method="GET" class="form-municipios">
            <div class="campo">
                <label for="municipio1">Municipio 1</label>
                <select name="municipio1" id="municipio1">
                    <option value="">Seleccione municipio</option>
                    @foreach($municipios as $m)
                        <option value="{{ $m }}" {{ ($municipio1 ?? '') == $m ? 'selected' : '' }}>{{ $m }}</option>
                    @endforeach
                </select>
            </div>
            <div class="campo">
                <label for="municipio2">Municipio 2</label>
                <select name="municipio2" id="municipio2">
                    <option value="">Seleccione municipio</option>
                    @foreach($municipios as $m)
                        <option value="{{ $m }}" {{ ($municipio2 ?? '') == $m ? 'selected' : '' }}>{{ $m }}</option>
                    @endforeach
                </select>
            </div>
            <div class="campo">
                <button type="submit" class="btn-comparar">
                    Comparar
                </button>
            </div>
        </form>

        @if($municipio1 && $municipio2 && isset($datos[$municipio1]) && isset($datos[$municipio2]))
        <div class="tabla-container">
            <table class="tabla-municipios">
                <thead>
                    <tr>
                        <th>Municipio</th>
                        <th>Cantidad colegios</th>
                        <th>Privados</th>
                        <th>Fiscales</th>
                        <th>Total estudiantes</th>
                        <th>Densidad estudiantes/colegio</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach([$municipio1, $municipio2] as $m)
                        <tr>
                            <td>{{ $m }}</td>
                            <td>{{ $datos[$m]['cantidadColegios'] }}</td>
                            <td>{{ $datos[$m]['cantidadPrivados'] }}</td>
                            <td>{{ $datos[$m]['cantidadFiscales'] }}</td>
                            <td>{{ $datos[$m]['totalEstudiantes'] }}</td>
                            <td>{{ $datos[$m]['densidad'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
    <script src="{{ asset('js/comparar-municipios.js') }}"></script>
</body>
</html>