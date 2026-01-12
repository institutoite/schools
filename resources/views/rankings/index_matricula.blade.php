@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/corporativo.css') }}">
@endpush

@section('content')
<div class="container">
    <h1>Ranking por Matrícula</h1>
    <form method="get" class="mb-3">
        <input type="hidden" name="tipo" value="matricula">
        <div class="row g-2 align-items-end">
            <div class="col-auto">
                <label for="orderDir" class="form-label">Orden:</label>
                <select name="orderDir" id="orderDir" class="form-select">
                    <option value="desc" {{ request('orderDir', 'desc') == 'desc' ? 'selected' : '' }}>Descendente</option>
                    <option value="asc" {{ request('orderDir') == 'asc' ? 'selected' : '' }}>Ascendente</option>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Ordenar</button>
            </div>
        </div>
    </form>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Colegio</th>
                <th>Matrícula</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($itemsCount as $i => $item)
                <tr>
                    <td>{{ ($itemsCount->currentPage() - 1) * $itemsCount->perPage() + $i + 1 }}</td>
                    <td>{{ \App\Models\School::find($item->school_id)?->nombre }}</td>
                    <td>{{ $item->mat }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $itemsCount->links() }}
</div>
@endsection
