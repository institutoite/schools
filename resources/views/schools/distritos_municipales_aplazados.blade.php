@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto py-6 px-2 sm:px-4" style="background: linear-gradient(135deg, rgb(38,186,165) 0%, rgb(55,95,122) 100%); border-radius: 18px; box-shadow: 0 4px 24px 0 rgba(55,95,122,0.10);">
    <h1 class="text-2xl sm:text-3xl font-bold mb-4 sm:mb-6 flex items-center gap-2" style="color: #375F7A;">
        <i class="fas fa-map-marked-alt"></i> Colegios más aplazados por distrito municipal (Santa Cruz) ({{ $anio }})
    </h1>

    <form method="GET" action="{{ url('/distritos-municipales-aplazados') }}" class="bg-white rounded-xl p-4 sm:p-6 mb-6 shadow">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-secondary-700 mb-1">Distrito municipal</label>
                <select name="distrito_municipal" class="w-full border border-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-400 focus:outline-none">
                    <option value="">Todos los distritos municipales</option>
                    @foreach($districtLabels as $label)
                        <option value="{{ $label }}" @selected($distritoSeleccionado === $label)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-secondary-700 mb-1">Gestión</label>
                <select name="gestion" class="w-full border border-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-400 focus:outline-none">
                    @foreach($aniosDisponibles as $anioDisp)
                        <option value="{{ $anioDisp }}" @selected((int)$anio === (int)$anioDisp)>{{ $anioDisp }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="mt-4 flex flex-wrap gap-2">
            <button type="submit" class="px-4 py-2 rounded-lg text-white font-semibold" style="background:#26BAA5;">
                Buscar
            </button>
            @if($distritoSeleccionado)
                <a href="{{ url('/distritos-municipales-aplazados') }}" class="px-4 py-2 rounded-lg font-semibold" style="background:#eef2f7;color:#375F7A;">
                    Limpiar
                </a>
            @endif
        </div>
    </form>

    <div class="bg-white rounded-xl shadow p-4 sm:p-6 mb-6">
        <h2 class="text-lg sm:text-xl font-bold mb-3" style="color:#375F7A;">
            Colegio con mayor reprobación en cada distrito municipal
        </h2>
        @if($rankingPorDistrito->isEmpty())
            <div class="bg-yellow-100 text-yellow-800 rounded-lg p-4 text-center">
                No hay datos de reprobados con coordenadas dentro del GeoJSON para esta gestión.
            </div>
        @else
            @php
                $baseQuery = request()->query();
                $toggleReprobados = ($orderBy === 'reprobados' && $orderDir === 'asc') ? 'desc' : 'asc';
                $togglePorcentaje = ($orderBy === 'porcentaje' && $orderDir === 'asc') ? 'desc' : 'asc';
            @endphp
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white rounded-xl shadow text-xs sm:text-base">
                    <thead style="background: #26BAA5; color: #fff;">
                        <tr>
                            <th class="py-2 sm:py-3 px-2 sm:px-4 text-center whitespace-nowrap">#</th>
                            <th class="py-2 sm:py-3 px-2 sm:px-4 text-left whitespace-nowrap">Distrito municipal</th>
                            <th class="py-2 sm:py-3 px-2 sm:px-4 text-left whitespace-nowrap">Colegio</th>
                            <th class="py-2 sm:py-3 px-2 sm:px-4 text-center whitespace-nowrap">
                                <a class="underline" href="{{ url('/distritos-municipales-aplazados?' . http_build_query(array_merge($baseQuery, ['orderBy' => 'reprobados', 'orderDir' => $toggleReprobados]))) }}">Reprobados {{ $orderBy === 'reprobados' ? ($orderDir === 'asc' ? '↑' : '↓') : '' }}</a>
                            </th>
                            <th class="py-2 sm:py-3 px-2 sm:px-4 text-center whitespace-nowrap">Matriculados</th>
                            <th class="py-2 sm:py-3 px-2 sm:px-4 text-center whitespace-nowrap">
                                <a class="underline" href="{{ url('/distritos-municipales-aplazados?' . http_build_query(array_merge($baseQuery, ['orderBy' => 'porcentaje', 'orderDir' => $togglePorcentaje]))) }}">% Reprobación {{ $orderBy === 'porcentaje' ? ($orderDir === 'asc' ? '↑' : '↓') : '' }}</a>
                            </th>
                            <th class="py-2 sm:py-3 px-2 sm:px-4 text-center whitespace-nowrap">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rankingPorDistrito as $item)
                            <tr style="border-bottom: 1px solid #e5e7eb; background: {{ $loop->even ? 'rgba(38,186,165,0.08)' : '#fff' }};">
                                <td class="py-1 sm:py-2 px-2 sm:px-4 text-center">{{ $loop->iteration }}</td>
                                <td class="py-1 sm:py-2 px-2 sm:px-4" style="color:#375F7A; font-weight:600;">{{ $item->distrito }}</td>
                                <td class="py-1 sm:py-2 px-2 sm:px-4">{{ $item->colegio }}</td>
                                <td class="py-1 sm:py-2 px-2 sm:px-4 text-center font-bold" style="color:#26BAA5;">{{ $item->reprobados }}</td>
                                <td class="py-1 sm:py-2 px-2 sm:px-4 text-center">{{ $item->matricula }}</td>
                                <td class="py-1 sm:py-2 px-2 sm:px-4 text-center">{{ number_format($item->porcentaje, 2) }}%</td>
                                <td class="py-1 sm:py-2 px-2 sm:px-4 text-center">
                                    <a href="{{ url('/distritos-municipales-aplazados?distrito_municipal=' . urlencode($item->distrito) . '&gestion=' . $anio) }}"
                                       class="inline-flex items-center gap-2 px-3 sm:px-4 py-2 sm:py-2 text-xs sm:text-base"
                                       style="background:#375F7A;color:#fff;border-radius:8px;transition:background 0.2s;min-width:40px;justify-content:center;">
                                        <span class="block sm:hidden"><i class="fas fa-eye"></i></span>
                                        <span class="hidden sm:inline">Ver distrito <i class="fas fa-arrow-right"></i></span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    @if($distritoSeleccionado)
        <div class="bg-white rounded-xl shadow p-4 sm:p-6">
            <h2 class="text-lg sm:text-xl font-bold mb-3" style="color:#375F7A;">
                Ranking de colegios del distrito municipal: {{ $distritoSeleccionado }}
            </h2>
            @if($colegiosDistrito->isEmpty())
                <div class="bg-yellow-100 text-yellow-800 rounded-lg p-4 text-center">
                    No hay colegios con reprobados en el distrito seleccionado para esta gestión.
                </div>
            @else
                @php
                    $baseQueryDetalle = request()->query();
                    $toggleReprobadosDetalle = ($orderByDetalle === 'reprobados' && $orderDirDetalle === 'asc') ? 'desc' : 'asc';
                    $togglePorcentajeDetalle = ($orderByDetalle === 'porcentaje' && $orderDirDetalle === 'asc') ? 'desc' : 'asc';
                @endphp
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white rounded-xl shadow text-xs sm:text-base">
                        <thead style="background: #26BAA5; color: #fff;">
                            <tr>
                                <th class="py-2 sm:py-3 px-2 sm:px-4 text-center whitespace-nowrap">#</th>
                                <th class="py-2 sm:py-3 px-2 sm:px-4 text-left whitespace-nowrap">Colegio</th>
                                <th class="py-2 sm:py-3 px-2 sm:px-4 text-center whitespace-nowrap">
                                    <a class="underline" href="{{ url('/distritos-municipales-aplazados?' . http_build_query(array_merge($baseQueryDetalle, ['orderByDetalle' => 'reprobados', 'orderDirDetalle' => $toggleReprobadosDetalle]))) }}">Reprobados {{ $orderByDetalle === 'reprobados' ? ($orderDirDetalle === 'asc' ? '↑' : '↓') : '' }}</a>
                                </th>
                                <th class="py-2 sm:py-3 px-2 sm:px-4 text-center whitespace-nowrap">Matriculados</th>
                                <th class="py-2 sm:py-3 px-2 sm:px-4 text-center whitespace-nowrap">
                                    <a class="underline" href="{{ url('/distritos-municipales-aplazados?' . http_build_query(array_merge($baseQueryDetalle, ['orderByDetalle' => 'porcentaje', 'orderDirDetalle' => $togglePorcentajeDetalle]))) }}">% Reprobación {{ $orderByDetalle === 'porcentaje' ? ($orderDirDetalle === 'asc' ? '↑' : '↓') : '' }}</a>
                                </th>
                                <th class="py-2 sm:py-3 px-2 sm:px-4 text-center whitespace-nowrap">Detalle</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($colegiosDistrito as $colegio)
                                <tr style="border-bottom: 1px solid #e5e7eb; background: {{ $loop->even ? 'rgba(38,186,165,0.08)' : '#fff' }};">
                                    <td class="py-1 sm:py-2 px-2 sm:px-4 text-center">{{ $loop->iteration }}</td>
                                    <td class="py-1 sm:py-2 px-2 sm:px-4">{{ $colegio['nombre'] }}</td>
                                    <td class="py-1 sm:py-2 px-2 sm:px-4 text-center font-bold" style="color:#26BAA5;">{{ $colegio['reprobados'] }}</td>
                                    <td class="py-1 sm:py-2 px-2 sm:px-4 text-center">{{ $colegio['matricula'] }}</td>
                                    <td class="py-1 sm:py-2 px-2 sm:px-4 text-center">{{ number_format($colegio['porcentaje'], 2) }}%</td>
                                    <td class="py-1 sm:py-2 px-2 sm:px-4 text-center">
                                        <a href="{{ url('/schools/' . $colegio['id']) }}"
                                           class="inline-flex items-center gap-2 px-3 sm:px-4 py-2 sm:py-2 text-xs sm:text-base"
                                           style="background:#375F7A;color:#fff;border-radius:8px;transition:background 0.2s;min-width:40px;justify-content:center;">
                                            <span class="block sm:hidden"><i class="fas fa-eye"></i></span>
                                            <span class="hidden sm:inline">Ver colegio <i class="fas fa-arrow-right"></i></span>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endif
</div>
@endsection
