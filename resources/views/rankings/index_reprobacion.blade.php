@extends('layouts.app')

<style>
    
</style>
@section('content')
    <div class="filters-container py-3 mb-4" id="css-js-test">
        <form method="get" class="filtros-form mb-0" autocomplete="off">
            <input type="hidden" name="tipo" value="reprobacion">
            <div class="filtros-grid">
                <div class="filtro-item">
                    <label for="anio" class="form-label mb-1 fw-medium">Año</label>
                    <select name="anio" id="anio" class="form-select">
                        @foreach ($years ?? [] as $year)
                            <option value="{{ $year }}" {{ request('anio', $anio) == $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="filtro-item">
                    <label for="nivel" class="form-label mb-1 fw-medium">Nivel</label>
                    <select name="nivel" id="nivel" class="form-select">
                        @php $niveles = ['nacional'=>'Nacional','departamental'=>'Departamental','provincial'=>'Provincial','municipal'=>'Municipal','distrital'=>'Distrital']; @endphp
                        @foreach ($niveles as $k => $v)
                            <option value="{{ $k }}" {{ request('nivel', $nivel) == $k ? 'selected' : '' }}>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="filtro-item position-relative">
                    <label for="q" class="form-label mb-1 fw-medium">Colegio</label>
                    <input type="text" name="q" id="q" class="form-control" value="{{ old('q', (request()->has('q') ? request('q') : '')) }}" placeholder="Buscar colegio..." autocomplete="off">
                    <input type="hidden" name="school_id" id="school_id" value="{{ old('school_id', (request()->has('school_id') ? request('school_id') : '')) }}">
                    <div id="autocomplete-list" class="autocomplete-items"></div>
                </div>
                <!-- Eliminar select de orden -->
                <!-- Botón Buscar eliminado, interacción automática -->
            </div>
        </form>
    <!-- CSS movido a corporativo.css -->

        {{-- Card con toda la información del colegio seleccionado y posición --}}
        @include('rankings._context', [
            'itemsCount' => $itemsCount ?? null,
            'autoUbic' => $autoUbic ?? null,
            'nivel' => $nivel ?? null,
            'targetName' => $targetName ?? null,
            'pos' => $pos ?? null,
            'posValue' => $posValue ?? null,
            'posTotal' => $posTotal ?? null,
            'selectedSchool' => $selectedSchool ?? null,
            'selectedRep' => $selectedRep ?? null,
            'selectedMat' => $selectedMat ?? null,
            'selectedRatio' => $selectedRatio ?? null,
            'anio' => $anio ?? null,
        ])

    <!-- JS movido a rankings.js y CSS de autocompletado a corporativo.css -->
    @push('scripts')
    <script src="{{ asset('js/rankings.js') }}"></script>
    <script>
    // Prueba visual de CSS
    
    </script>
    @endpush

    {{-- Tabla con resaltado del colegio seleccionado --}}
    @include('rankings._table', [
        'itemsCount' => $itemsCount ?? null,
        'anio' => $anio ?? null,
        'selectedId' => $school_id ?? null,
    ])
</div>
@endsection