

@if(isset($itemsCount))
	<div class="card overflow-x-auto">
		<table class="min-w-full text-sm">
			<thead>
				<tr class="table-head sticky-head">
					<th class="py-2 px-3 text-left">#</th>
					<th class="py-2 px-3 text-left">Nombre del colegio</th>
					@if(request('tipo') === 'matricula')
						<th class="py-2 px-3 text-left">Matriculados</th>
					@elseif(request('tipo') === 'reprobacion')
						<th class="py-2 px-3 text-left">Reprobados {{ isset($anio) ? '(' . $anio . ')' : '' }}</th>
						<th class="py-2 px-3 text-left">Matrícula</th>
					@endif
					<th class="py-2 px-3 text-left">Acciones</th>
				</tr>
			</thead>
			<tbody>
			@foreach($itemsCount as $i => $row)
				@php $school = \App\Models\School::find($row->school_id ?? null); @endphp
				@php $sel = isset($selectedId) && (string)$selectedId !== '' && (string)$selectedId === (string)($row->school_id ?? ''); @endphp
				<tr data-school-id="{{ $row->school_id ?? '' }}" class="border-b table-row {{ $sel ? 'row-selected' : '' }}">
					<td class="py-2 px-3">{{ (($itemsCount->currentPage() - 1) * $itemsCount->perPage()) + ($i + 1) }}</td>
					<td class="py-2 px-3">{{ $school->nombre ?? 'Desconocido' }}</td>
					@if(request('tipo') === 'matricula')
						<td class="py-2 px-3">{{ number_format($row->total ?? 0) }}</td>
					@elseif(request('tipo') === 'reprobacion')
						<td class="py-2 px-3">{{ number_format($row->rep ?? 0) }}</td>
						<td class="py-2 px-3">{{ number_format($row->mat ?? 0) }}</td>
					@endif
					<td class="py-2 px-3">
						@if(isset($row->school_id))
							<a href="{{ url('/schools/'.$row->school_id) }}" class="btn btn-outline"><i class="fas fa-eye"></i> Ver</a>
						@endif
					</td>
				</tr>
			@endforeach
			</tbody>
		</table>
	</div>
	<div class="py-3">{{ $itemsCount->appends(request()->query())->links() }}</div>
@else
	<div class="card p-6 text-center">
		<p class="heading">No hay datos para mostrar.</p>
		<p class="subtle text-sm mt-1">Intenta cambiar el año o nivel.</p>
	</div>
@endif

