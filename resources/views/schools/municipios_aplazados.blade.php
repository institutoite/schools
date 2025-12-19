@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto py-6 px-2 sm:px-4" style="background: linear-gradient(135deg, rgb(38,186,165) 0%, rgb(55,95,122) 100%); border-radius: 18px; box-shadow: 0 4px 24px 0 rgba(55,95,122,0.10);">
    <h1 class="text-2xl sm:text-3xl font-bold mb-4 sm:mb-6 flex items-center gap-2" style="color: #375F7A;">
        <i class="fas fa-city"></i> Colegios más aplazados por municipio ({{ $ultimoAnio }})
    </h1>
    @if($resultados->isEmpty())
        <div class="bg-yellow-100 text-yellow-800 rounded-lg p-6 text-center mb-8">
            No hay datos de reprobados para mostrar en los municipios seleccionados.
        </div>
    @else
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white rounded-xl shadow text-xs sm:text-base">
            <thead style="background: #26BAA5; color: #fff;">
                <tr>
                    <th class="py-2 sm:py-3 px-2 sm:px-4 text-left whitespace-nowrap">Municipio</th>
                    <th class="py-2 sm:py-3 px-2 sm:px-4 text-left whitespace-nowrap">Colegio</th>
                    <th class="py-2 sm:py-3 px-2 sm:px-4 text-center whitespace-nowrap">Reprobados</th>
                    <th class="py-2 sm:py-3 px-2 sm:px-4 text-center whitespace-nowrap">Detalle</th>
                </tr>
            </thead>
            <tbody>
                @foreach($resultados as $item)
                <tr style="border-bottom: 1px solid #e5e7eb; background: {{ $loop->even ? 'rgba(38,186,165,0.08)' : '#fff' }};">
                    <td class="py-1 sm:py-2 px-2 sm:px-4" style="color:#375F7A; font-weight:600;">{{ $item->municipio }}</td>
                    <td class="py-1 sm:py-2 px-2 sm:px-4">{{ $item->colegio }}</td>
                    <td class="py-1 sm:py-2 px-2 sm:px-4 text-center font-bold" style="color:#26BAA5;">{{ $item->reprobados }}</td>
                    <td class="py-1 sm:py-2 px-2 sm:px-4 text-center">
                        <a href="{{ url('/listar-colegios-municipio?municipio=' . urlencode($item->municipio)) }}"
                           class="inline-flex items-center gap-2 px-3 sm:px-4 py-2 sm:py-2 text-xs sm:text-base"
                           style="background:#375F7A;color:#fff;border-radius:8px;transition:background 0.2s;min-width:40px;justify-content:center;">
                            <span class="block sm:hidden"><i class="fas fa-eye"></i></span>
                            <span class="hidden sm:inline">Ver municipio <i class="fas fa-arrow-right"></i></span>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($resultados instanceof \Illuminate\Pagination\LengthAwarePaginator)
    <div class="mt-6 flex justify-center">
        {{ $resultados->links() }}
    </div>
    @endif
    @endif
</div>
@endsection
