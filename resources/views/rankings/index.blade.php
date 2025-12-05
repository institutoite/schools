<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <title>Rankings</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-primary/10 min-h-screen">
<div class="max-w-6xl mx-auto py-8 px-4">
  <h1 class="text-2xl font-bold text-secondary mb-4 flex items-center gap-2">
    <i class="fas fa-trophy text-primary"></i> Ranking: {{ ucfirst($tipo) }} {{ isset($anio) ? '(' . $anio . ')' : '' }}
  </h1>

  <!-- Filtros -->
  <form id="rankings-form" method="GET" action="{{ route('rankings.index') }}" class="bg-white rounded-xl shadow p-4 mb-6 grid grid-cols-1 md:grid-cols-6 gap-3">
    <div>
      <label class="text-sm font-semibold text-secondary">Año</label>
      <select name="anio" class="w-full mt-1 border rounded px-3 py-2">
        @if(isset($years) && count($years))
          @foreach($years as $y)
            <option value="{{ $y }}" {{ (string)$y === (string)request('anio', $anio) ? 'selected' : '' }}>{{ $y }}</option>
          @endforeach
        @else
          <option value="{{ $anio }}">{{ $anio }}</option>
        @endif
      </select>
    </div>
    <div>
      <label class="text-sm font-semibold text-secondary">Nivel</label>
      <select name="nivel" class="w-full mt-1 border rounded px-3 py-2">
        <option value="nacional" {{ (request('nivel')=='nacional')?'selected':'' }}>Nacional</option>
        <option value="departamental" {{ (request('nivel')=='departamental')?'selected':'' }}>Departamental</option>
        <option value="provincial" {{ (request('nivel')=='provincial')?'selected':'' }}>Provincial</option>
        <option value="municipal" {{ (request('nivel')=='municipal')?'selected':'' }}>Municipal</option>
        <option value="distrital" {{ (request('nivel')=='distrital')?'selected':'' }}>Distrital</option>
      </select>
    </div>
    <!-- Campos de ubicación removidos: se aplican automáticamente según el colegio seleccionado -->
    <!-- Modo removido: esta vista se enfoca en reprobación por cantidad -->
    <div class="md:col-span-2">
      <label class="text-sm font-semibold text-secondary">Seleccionar colegio</label>
      <div class="relative">
        <input type="text" id="school-search" autocomplete="off" value="{{ request('q') }}" class="w-full mt-1 border rounded px-3 py-2" placeholder="Escribe para buscar...">
        <input type="hidden" name="q" id="school-q" value="{{ request('q') }}">
        <input type="hidden" name="school_id" id="school-id" value="{{ request('school_id') }}">
        <div id="school-results" class="absolute z-50 mt-1 w-full bg-white border rounded shadow max-h-64 overflow-y-auto hidden"></div>
      </div>
      <p class="text-xs text-secondary mt-1">Muestra nombre + turno, nivel y ubicación para diferenciar colegios similares.</p>
    </div>
    <div class="md:col-span-2 flex items-end gap-2">
      <button type="button" id="btn-ver-colegio" class="bg-primary text-white px-3 py-2 rounded disabled:opacity-50">Ver colegio</button>
      <a href="{{ route('home') }}" class="px-3 py-2 rounded border border-primary text-primary hover:bg-primary hover:text-white">Todos los colegios</a>
    </div>
    <div class="md:col-span-4 flex justify-end">
      <button class="bg-primary text-white px-4 py-2 rounded hover:bg-secondary">Aplicar filtros</button>
    </div>
  </form>
  <script>
    (function(){
      const input = document.getElementById('school-search');
      const box = document.getElementById('school-results');
      const qField = document.getElementById('school-q');
      const idField = document.getElementById('school-id');
      const btnVer = document.getElementById('btn-ver-colegio');
      let timer;
      function render(items){
        if(!items || !items.length){ box.classList.add('hidden'); box.innerHTML=''; return; }
        box.innerHTML = items.map(item => {
          const ubic = item.ubicacion || {};
          const turno = item.turno ? ` • Turno: ${item.turno}` : '';
          const nivel = item.nivel ? ` • Nivel: ${item.nivel}` : '';
          const loc = [ubic.departamento, ubic.provincia, ubic.municipio, ubic.distrito].filter(Boolean).join(', ');
          const rude = item.codigo_rue ? ` • RUE: ${item.codigo_rue}` : '';
          const dep = item.dependencia ? ` • ${item.dependencia}` : '';
          return `<button type="button" data-id="${item.id}" data-name="${item.nombre}" class="w-full text-left px-3 py-2 hover:bg-primary/10">
            <div class="text-sm font-semibold text-secondary">${item.nombre}</div>
            <div class="text-xs text-secondary/70">${loc}${turno}${nivel}${rude}${dep}</div>
          </button>`;
        }).join('');
        box.classList.remove('hidden');
        Array.from(box.querySelectorAll('button')).forEach(btn => {
          btn.addEventListener('click', () => {
            const id = btn.getAttribute('data-id');
            const name = btn.getAttribute('data-name');
            input.value = name; qField.value = name; idField.value = id; box.classList.add('hidden');
            btnVer.disabled = !id;
            // Destacar selección visible debajo del input
            input.style.borderColor = 'rgb(38,186,165)';
            // En rankings, enviar el formulario para mostrar posición inmediatamente
            const form = document.getElementById('rankings-form');
            if(form) form.submit();
          });
        });
      }
      async function search(term){
        const url = new URL('{{ route('schools.search') }}', window.location.origin);
        url.searchParams.set('q', term);
        try {
          const res = await fetch(url.toString());
          const data = await res.json();
          render(data);
        } catch(e){ box.classList.add('hidden'); }
      }
      input.addEventListener('input', () => {
        clearTimeout(timer);
        const term = input.value.trim();
        if(term.length < 2){ box.classList.add('hidden'); return; }
        timer = setTimeout(() => search(term), 250);
      });
      document.addEventListener('click', (e) => {
        if(!box.contains(e.target) && e.target !== input){ box.classList.add('hidden'); }
      });
      btnVer.addEventListener('click', () => {
        const id = idField.value;
        if(!id) return;
        const url = `{{ url('/schools') }}/${id}`;
        window.location.href = url;
      });
      // Inicializar botón según estado
      btnVer.disabled = !(idField.value);
    })();
  </script>
  @if(isset($q) && $q !== '')
    <div class="bg-white rounded-xl shadow p-4 mb-6">
      <p class="text-sm text-secondary">Búsqueda: <span class="font-semibold">{{ $targetName ?? $q }}</span> {{ isset($anio) ? '— Año ' . $anio : '' }}</p>
      @if($pos)
        <p class="text-secondary">Se encuentra en la <span class="font-bold">posición {{ $pos }}</span> de {{ $posTotal }} ({{ $posLabel }}).</p>
        @if(isset($posValue) && isset($posMetric))
          <p class="mt-1 text-secondary">
            Valor: 
            @if($posMetric === 'porcentaje')
              <span class="font-semibold">{{ number_format($posValue, 2) }}%</span>
            @else
              <span class="font-semibold">{{ number_format($posValue) }}</span>
            @endif
          </p>
        @endif
      @else
        <p class="text-red-600">No se encontró el colegio en el ranking actual.</p>
      @endif
    </div>
  @endif
  @if(isset($itemsCount))
      @if(isset($nivel) && isset($autoUbic))
        <div class="bg-white rounded-xl shadow p-3 mb-4 flex flex-wrap items-center gap-3 text-sm">
          <div class="flex items-center gap-2">
            <span class="px-2 py-1 rounded bg-primary/10 text-primary font-semibold">Ámbito:</span>
            <span class="text-secondary">{{ ucfirst($nivel) }}</span>
            <span class="text-secondary/70">—
              @if($nivel==='departamental') {{ $autoUbic->departamento }} @endif
              @if($nivel==='provincial') {{ $autoUbic->departamento }}, {{ $autoUbic->provincia }} @endif
              @if($nivel==='municipal') {{ $autoUbic->departamento }}, {{ $autoUbic->municipio }} @endif
              @if($nivel==='distrital') {{ $autoUbic->departamento }}, {{ $autoUbic->municipio }}, {{ $autoUbic->distrito }} @endif
            </span>
          </div>
          @if(isset($targetName) && $targetName)
            <div class="flex items-center gap-2">
              <span class="px-2 py-1 rounded bg-primary/10 text-primary font-semibold">Colegio:</span>
              <span class="text-secondary font-semibold">{{ $targetName }}</span>
            </div>
          @endif
          @if(isset($pos) && $pos)
            <div class="flex items-center gap-2">
              <span class="px-2 py-1 rounded bg-primary/10 text-primary font-semibold">Posición:</span>
              <span class="text-secondary font-semibold">#{{ $pos }}</span>
              @if(isset($posValue))
                <span class="text-secondary/70">— Valor: {{ number_format($posValue) }}</span>
              @endif
            </div>
          @endif
        </div>
      @endif
      <div>
        <h2 class="text-lg font-semibold mb-2">Cantidad de reprobados {{ isset($anio) ? '(' . $anio . ')' : '' }}</h2>
        <div class="bg-white rounded-xl shadow overflow-x-auto">
          <table class="min-w-full text-sm">
            <thead>
              <tr class="bg-primary text-white">
                <th class="py-2 px-3 text-left">#</th>
                <th class="py-2 px-3 text-left">Nombre del colegio</th>
                <th class="py-2 px-3 text-left">Reprobados {{ isset($anio) ? '(' . $anio . ')' : '' }}</th>
                <th class="py-2 px-3 text-left">Matrícula</th>
              </tr>
            </thead>
            <tbody>
            @foreach($itemsCount as $i => $row)
              @php $school = \App\Models\School::find($row->school_id ?? null); @endphp
              <tr class="border-b {{ (request('school_id') && isset($school->id) && (string)request('school_id') === (string)$school->id) ? 'bg-green-200' : '' }}">
                <td class="py-2 px-3">{{ (($itemsCount->currentPage() - 1) * $itemsCount->perPage()) + ($i + 1) }}</td>
                <td class="py-2 px-3">{{ $school->nombre ?? 'Desconocido' }}</td>
                <td class="py-2 px-3">{{ number_format($row->rep ?? 0) }}</td>
                <td class="py-2 px-3">{{ number_format($row->mat ?? 0) }}</td>
              </tr>
            @endforeach
            </tbody>
          </table>
        </div>
        <div class="py-3">{{ $itemsCount->appends(request()->query())->links() }}</div>
        </div>
  @else
    <div class="bg-primary/10 text-primary rounded-xl p-6 text-center shadow">
      No hay datos para mostrar.
    </div>
  @endif
</div>
</body>
</html>
