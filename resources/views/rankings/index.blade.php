<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <title>Rankings</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<style>
  :root{
    --brand-primary: rgb(38,186,165); /* primario */
    --brand-secondary: rgb(55,95,122); /* secundario */
    --brand-accent:#F59E0B; /* Ámbar */
    --brand-bg:#EEF6F9; /* fondo más suave, menos blanco */
  }
  .brand-gradient{background:linear-gradient(135deg,var(--brand-primary),#11998e);} 
  .card{background:#fff;border-radius:14px;box-shadow:0 8px 24px rgba(31,41,55,.08);} 
  .tag{font-weight:600;border-radius:10px;padding:.25rem .5rem;background:rgba(38,186,165,.12);color:var(--brand-primary);} 
  .table-head{background:var(--brand-secondary);color:#fff;} 
  .table-row{transition:background .2s ease;} 
  .table-row:hover{background:rgba(38,186,165,.08); box-shadow:inset 0 0 0 9999px rgba(38,186,165,.05);} 
  .row-selected{background:rgb(38,186,165) !important;color:#fff !important;}
  .row-selected td{color:#fff !important; text-shadow:0 1px 2px rgba(0,0,0,.25); font-size:1.05rem; font-weight:800;}
  .row-selected .btn-outline{border-color:#fff;color:#fff;background:rgba(255,255,255,.12);} 
  .row-selected .btn-outline:hover{background:#fff;color:rgb(38,186,165);} 
  .btn{display:inline-flex;align-items:center;gap:.5rem;border-radius:10px;padding:.6rem 1rem;font-weight:600;}
  .btn-primary{background:var(--brand-primary);color:#fff;}
  .btn-primary:hover{filter:brightness(1.05);} 
  .btn-outline{border:1px solid var(--brand-primary);color:var(--brand-primary);} 
  .btn-outline:hover{background:var(--brand-primary);color:#fff;} 
  .heading{color:var(--brand-secondary);} 
  .subtle{color:#6B7280;} 
  .sticky-head{position:sticky;top:0;z-index:10;}
  .kbd{font-family:ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace; background:#F3F4F6;border:1px solid #E5E7EB;border-bottom-width:3px;border-radius:8px;padding:.15rem .35rem;color:#374151;}
  .toast-container{position:fixed;right:16px;bottom:16px;display:flex;flex-direction:column;gap:.5rem;z-index:1000}
  .toast{background:#fff;border-left:4px solid var(--brand-primary);box-shadow:0 6px 18px rgba(31,41,55,.16);border-radius:10px;padding:.6rem .9rem;min-width:220px;color:var(--brand-secondary)}
  .toast.error{border-left-color:#EF4444;color:#991B1B}
  .toast.info{border-left-color:var(--brand-primary)}
  .chips{display:flex;flex-wrap:wrap;gap:.5rem}
  .chip{background:rgba(55,95,122,.08);color:var(--brand-secondary);border:1px solid rgba(55,95,122,.18);border-radius:999px;padding:.25rem .6rem;font-size:.75rem;font-weight:600; transition: all .15s ease}
  .chip.interactive{cursor:pointer;}
  .chip.interactive:hover{background:rgba(38,186,165,.12); border-color:rgba(38,186,165,.4); color:var(--brand-secondary); transform:translateY(-1px)}
  .chip.interactive:active{transform:scale(.98)}
  /* Ripple + splash effect */
  .chip.interactive{position:relative; overflow:hidden}
  .ripple{position:absolute; border-radius:50%; transform:translate(-50%, -50%); pointer-events:none; animation:ripple .6s ease-out forwards; background:rgba(38,186,165,.35)}
  @keyframes ripple{0%{width:0;height:0;opacity:.9} 80%{width:220px;height:220px;opacity:.25} 100%{width:260px;height:260px;opacity:0}}
  .splash{position:absolute; width:3px; height:3px; background:rgba(38,186,165,.5); border-radius:50%; animation:splash .8s ease-out forwards}
  @keyframes splash{0%{transform:translate(0,0); opacity:.9} 100%{transform:translate(var(--dx), var(--dy)); opacity:0}}
  /* Global splash particles (outside element) */
  .splash-global{position:fixed; width:6px; height:6px; background:rgba(38,186,165,.7); border-radius:50%; box-shadow:0 2px 6px rgba(0,0,0,.15); animation:splash-global 1.1s ease-out forwards; z-index:9999; pointer-events:none}
  @keyframes splash-global{0%{transform:translate(0,0) scale(1); opacity:.95} 100%{transform:translate(var(--dx), var(--dy)) scale(.7); opacity:0}}
  .ring{position:absolute; border:2px solid currentColor; border-radius:50%; transform:translate(-50%, -50%); pointer-events:none; animation:ring .8s ease-out forwards;}
  @keyframes ring{0%{width:0;height:0;opacity:.6} 100%{width:260px;height:260px;opacity:0}}
  .pos-hero{background:linear-gradient(135deg,var(--brand-secondary), #22394a);color:#fff;border-radius:14px;padding:0.9rem 1.1rem;display:flex;flex-direction:column;justify-content:center;align-items:center;min-height:92px}
  .pos-hero .pos{font-size:2.25rem;line-height:1;font-weight:800}
  .pos-hero .label{font-size:.75rem;opacity:.9;margin-top:.25rem}
  .pos-hero.pulse{animation:pulse 1.2s ease-in-out}
  @keyframes pulse{0%{transform:scale(1)} 50%{transform:scale(1.04)} 100%{transform:scale(1)}}
  /* Splashable generic */
  .splashable{position:relative; overflow:hidden; cursor:pointer}
  .splashable:hover{filter:brightness(1.02)}
  .pos-hero{overflow:hidden}
  .pop{animation:pop .18s ease-out}
  @keyframes pop{0%{transform:scale(1)}50%{transform:scale(1.06)}100%{transform:scale(1)}}
</style>
<body class="min-h-screen" style="background:var(--brand-bg)">
<div class="max-w-6xl mx-auto py-6 px-4">
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold heading flex items-center gap-2">
      <i class="fas fa-trophy" style="color:var(--brand-primary)"></i>
      Ranking: {{ ucfirst($tipo) }} {{ isset($anio) ? '(' . $anio . ')' : '' }}
    </h1>
    <span class="tag">Cantidad de reprobados</span>
  </div>
  <div class="mb-4 flex justify-end">
    <a href="{{ route('home') }}" class="btn btn-outline"><i class="fas fa-home"></i> Inicio</a>
  </div>
  <div id="toast-container" class="toast-container" aria-live="polite" aria-atomic="true"></div>

  <!-- Filtros -->
  <form id="rankings-form" method="GET" action="{{ route('rankings.index') }}" class="card p-4 mb-6 grid grid-cols-1 md:grid-cols-6 gap-3">
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
      
    </div>
    <div class="md:col-span-2 flex items-end gap-2">
      
    </div>
    <div class="md:col-span-4 flex items-end justify-end"></div>
  </form>
  <script>
    (function(){
            // Animación ripple/splash para chips interactivos y pulso para posición
            function attachChipEffects(){
              const getEffectColors = () => {
                const lvl = (nivelSelect && nivelSelect.value ? String(nivelSelect.value).toLowerCase() : 'nacional');
                switch(lvl){
                  case 'departamental': return { ripple:'rgba(55,95,122,.35)', particle:'rgba(55,95,122,.6)', ring:'#375f7a' };
                  case 'provincial': return { ripple:'rgba(245,158,11,.35)', particle:'rgba(245,158,11,.6)', ring:'#F59E0B' };
                  case 'municipal': return { ripple:'rgba(124,58,237,.28)', particle:'rgba(124,58,237,.55)', ring:'#7C3AED' };
                  case 'distrital': return { ripple:'rgba(219,39,119,.28)', particle:'rgba(219,39,119,.55)', ring:'#DB2777' };
                  default: return { ripple:'rgba(38,186,165,.35)', particle:'rgba(38,186,165,.6)', ring:'rgb(38,186,165)' };
                }
              };
              document.querySelectorAll('.chip.interactive, .splashable, .pos-hero').forEach(chip => {
                chip.addEventListener('click', (e) => {
                  chip.classList.add('pop'); setTimeout(()=>chip.classList.remove('pop'), 180);
                  const colors = getEffectColors();
                  const rect = chip.getBoundingClientRect();
                  const x = e.clientX - rect.left; const y = e.clientY - rect.top;
                  const ripple = document.createElement('span');
                  ripple.className = 'ripple'; ripple.style.left = x+'px'; ripple.style.top = y+'px';
                  ripple.style.background = colors.ripple;
                  chip.appendChild(ripple);
                  // Splash particles in 8 directions
                  const n = 12;
                  for(let i=0;i<n;i++){ const sp = document.createElement('span'); sp.className='splash'; sp.style.background = colors.particle; const a = (Math.PI*2*i)/n; const dist = 28 + Math.random()*26; sp.style.setProperty('--dx', Math.cos(a)*dist+'px'); sp.style.setProperty('--dy', Math.sin(a)*dist+'px'); sp.style.left = x+'px'; sp.style.top = y+'px'; chip.appendChild(sp); setTimeout(()=>sp.remove(), 800); }
                  // Concentric rings for ondas
                  for(let r=0; r<2; r++){ const ring = document.createElement('span'); ring.className='ring'; ring.style.left=x+'px'; ring.style.top=y+'px'; ring.style.color = colors.ring; ring.style.animationDelay = (r*80)+'ms'; chip.appendChild(ring); setTimeout(()=>ring.remove(), 900+(r*100)); }
                  // Global splashes (outside element) from click center
                  const gx = e.clientX; const gy = e.clientY;
                  const m = 18; // number of global particles (bigger set)
                  for(let i=0;i<m;i++){
                    const g = document.createElement('span'); g.className='splash-global';
                    // random size
                    const size = 6 + Math.random()*16; // 6px to 22px
                    g.style.width = size+'px'; g.style.height = size+'px';
                    // gradient color
                    const c1 = colors.particle;
                    const c2 = colors.ripple;
                    g.style.background = `radial-gradient(circle at 30% 30%, ${c1} 0%, ${c2} 70%)`;
                    g.style.left = gx+'px'; g.style.top = gy+'px';
                    const angle = Math.random()*Math.PI*2;
                    const distance = 160 + Math.random()*280; // travel farther outside
                    g.style.setProperty('--dx', Math.cos(angle)*distance+'px');
                    g.style.setProperty('--dy', Math.sin(angle)*distance+'px');
                    document.body.appendChild(g);
                    setTimeout(()=>g.remove(), 1200);
                  }
                  setTimeout(()=>ripple.remove(), 600);
                });
              });
              const hero = document.querySelector('.pos-hero');
              if(hero){ hero.classList.add('pulse'); setTimeout(()=>hero.classList.remove('pulse'), 800); }
            }
      const toasts = document.getElementById('toast-container');
      function showToast(message, type='info', timeout=3000){
        if(!toasts) return; const el = document.createElement('div');
        el.className = `toast ${type}`; el.textContent = message;
        toasts.appendChild(el);
        setTimeout(() => { el.style.opacity='0'; el.style.transition='opacity .3s'; setTimeout(()=>el.remove(), 300); }, timeout);
      }
      function getBoxes(){
        return {
          contextBox: document.getElementById('rankings-context'),
          tableBox: document.getElementById('rankings-table')
        };
      }
      const input = document.getElementById('school-search');
      const box = document.getElementById('school-results');
      const qField = document.getElementById('school-q');
      const idField = document.getElementById('school-id');
      const yearSelect = document.querySelector('select[name="anio"]');
      const nivelSelect = document.querySelector('select[name="nivel"]');
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
            console.log('[rankings] seleccionado:', { id, name });
            input.value = name; qField.value = name; idField.value = id; box.classList.add('hidden');
            if(btnVer) btnVer.disabled = !id;
            // Destacar selección visible debajo del input
            input.style.borderColor = 'rgb(38,186,165)';
            // Actualizar dinámicamente
            ajaxUpdate();
            showToast('Seleccionado: ' + name, 'info', 1500);
          });
        });
      }
      async function search(term){
        console.log('[rankings] búsqueda:', term);
        const url = new URL('{{ route('schools.search') }}', window.location.origin);
        url.searchParams.set('q', term);
        try {
          const res = await fetch(url.toString());
          const data = await res.json();
          if(!data || !data.length) showToast('Sin resultados de búsqueda', 'info');
          render(data);
        } catch(e){ box.classList.add('hidden'); console.error(e); showToast('Error buscando colegios', 'error'); }
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
      if(btnVer){
        btnVer.addEventListener('click', () => {
          const id = idField.value;
          if(!id) return;
          const url = `{{ url('/schools') }}/${id}`;
          window.location.href = url;
        });
        btnVer.disabled = !(idField.value);
      }

      if (yearSelect) {
        yearSelect.addEventListener('change', () => {
          showToast('Año cambiado a ' + yearSelect.value, 'info', 1200);
          ajaxUpdate();
        });
      }
      if (nivelSelect) {
        nivelSelect.addEventListener('change', () => {
          const label = nivelSelect.options[nivelSelect.selectedIndex]?.text || nivelSelect.value;
          showToast('Nivel cambiado a ' + label, 'info', 1200);
          ajaxUpdate();
        });
      }

      async function ajaxUpdate(urlOverride){
        console.log('[rankings] ajaxUpdate start');
        const url = new URL(urlOverride || '{{ route('rankings.index') }}', window.location.origin);
        const params = new URLSearchParams();
        params.set('tipo', 'reprobacion');
        params.set('modo', 'cantidad');
        const yearSel = document.querySelector('select[name="anio"]');
        const nivelSel = document.querySelector('select[name="nivel"]');
        if(yearSel && yearSel.value) params.set('anio', yearSel.value);
        if(nivelSel && nivelSel.value) params.set('nivel', nivelSel.value);
        if(input && input.value) params.set('q', input.value);
        if(idField && idField.value) params.set('school_id', idField.value);
        params.set('ajax', '1');
        url.search = params.toString();
        try{
          const res = await fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
          const data = await res.json();
          console.log('[rankings] ajax data:', data);
          const { contextBox, tableBox } = getBoxes();
          if(contextBox && data.contextHtml !== undefined){ contextBox.innerHTML = data.contextHtml; attachChipEffects(); }
          if(tableBox && data.tableHtml !== undefined){ tableBox.innerHTML = data.tableHtml; attachPaginationAjax(); highlightSelectedRow(); scrollToSelectedRow(); }
          if(!data.tableHtml){ showToast('Sin datos en el ranking para el filtro actual', 'info', 2000); }
          else { showToast('Tabla actualizada', 'info', 1200); }
          console.log('[rankings] ajaxUpdate done');
        }catch(e){ console.error(e); showToast('Error al actualizar ranking', 'error'); }
      }

      function attachPaginationAjax(){
        const { tableBox } = getBoxes();
        if(!tableBox) return;
        const links = tableBox.querySelectorAll('a[href]');
        links.forEach(a => {
          a.addEventListener('click', (ev) => {
            ev.preventDefault();
            const href = a.getAttribute('href');
            if(!href) return;
            const u = new URL(href, window.location.origin);
            ajaxUpdate(u.toString());
            showToast('Página cambiada', 'info', 1200);
          });
        });
      }

      function highlightSelectedRow(){
        const { tableBox } = getBoxes();
        if(!tableBox || !idField || !idField.value) return;
        const id = String(idField.value);
        const rows = tableBox.querySelectorAll('tr[data-school-id]');
        rows.forEach(r => r.classList.remove('row-selected'));
        const match = Array.from(rows).find(r => String(r.getAttribute('data-school-id')) === id);
        if(match) match.classList.add('row-selected');
      }

      function scrollToSelectedRow(){
        const { tableBox } = getBoxes();
        if(!tableBox) return;
        const selected = tableBox.querySelector('.row-selected');
        if(selected){ selected.scrollIntoView({ behavior: 'smooth', block: 'center' }); }
        else { showToast('Colegio no está en esta página', 'info', 1200); }
      }

      // Ejecutar al finalizar carga para asegurar que existan los contenedores
      window.addEventListener('DOMContentLoaded', () => {
        attachPaginationAjax();
        scrollToSelectedRow();
        ajaxUpdate();
        attachChipEffects();
      });
    })();
  </script>
  @if(isset($q) && $q !== '')
    <div class="card p-4 mb-6">
      <p class="text-sm heading">Búsqueda: <span class="font-semibold">{{ $targetName ?? $q }}</span> {{ isset($anio) ? '— Año ' . $anio : '' }}</p>
      @if($pos)
        <p class="subtle">Se encuentra en la <span class="font-bold">posición {{ $pos }}</span> de {{ $posTotal }} ({{ $posLabel }}).</p>
        @if(isset($posValue) && isset($posMetric))
          <p class="mt-1 subtle">
            Valor: 
            @if($posMetric === 'porcentaje')
              <span class="font-semibold">{{ number_format($posValue, 2) }}%</span>
            @else
              <span class="font-semibold">{{ number_format($posValue) }}</span>
            @endif
          </p>
          <div class="mt-3">
            <button type="button" id="btn-ver-colegio" class="btn btn-primary disabled:opacity-50"><i class="fas fa-school"></i> Ver colegio</button>
          </div>
        @endif
      @else
        <p class="text-red-600">No se encontró el colegio en el ranking actual.</p>
      @endif
    </div>
  @endif
  <div id="rankings-context">
    @include('rankings._context')
  </div>
  <div>
    <h2 class="text-lg font-semibold mb-2">Cantidad de reprobados {{ isset($anio) ? '(' . $anio . ')' : '' }}</h2>
    <div id="rankings-table">
      @if(isset($itemsCount))
        <div class="card overflow-x-auto">
          <table class="min-w-full text-sm">
            <thead>
              <tr class="table-head sticky-head">
                <th class="py-2 px-3 text-left">#</th>
                <th class="py-2 px-3 text-left">Nombre del colegio</th>
                <th class="py-2 px-3 text-left">Reprobados {{ isset($anio) ? '(' . $anio . ')' : '' }}</th>
                <th class="py-2 px-3 text-left">Matrícula</th>
                <th class="py-2 px-3 text-left">Acciones</th>
              </tr>
            </thead>
            <tbody>
            @foreach($itemsCount as $i => $row)
              @php $school = \App\Models\School::find($row->school_id ?? null); @endphp
              <tr data-school-id="{{ $row->school_id ?? '' }}" class="border-b table-row {{ (request('school_id') && (string)request('school_id') === (string)($row->school_id ?? '')) ? 'row-selected' : '' }}">
                <td class="py-2 px-3">{{ (($itemsCount->currentPage() - 1) * $itemsCount->perPage()) + ($i + 1) }}</td>
                <td class="py-2 px-3">{{ $school->nombre ?? 'Desconocido' }}</td>
                <td class="py-2 px-3">{{ number_format($row->rep ?? 0) }}</td>
                <td class="py-2 px-3">{{ number_format($row->mat ?? 0) }}</td>
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
    </div>
  </div>
</div>
</body>
</html>
