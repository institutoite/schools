@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Buscador -->
    <div class="mb-4">
        <form method="GET" action="{{ route('schools.index') }}" class="flex">
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="Buscar colegios..." class="form-control flex-grow">
            <button type="submit" class="btn btn-primary ml-2">Buscar</button>
        </form>
    </div>

    <!-- Tabla de resultados -->
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Código RUE</th>
                    <th>Nombre</th>
                    <th>Departamento</th>
                    <th>Tipo</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($schools as $school)
                    <tr>
                        <td>{{ $school->codigo_rue }}</td>
                        <td>{{ $school->nombre }}</td>
                        <td>{{ $school->ubicacion->departamento ?? 'N/A' }}</td>
                        <td>{{ $school->dependencia }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">No se encontraron colegios</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    <div class="mt-4">
        {{ $schools->appends(request()->query())->links() }}
    </div>
</div>
@endsection