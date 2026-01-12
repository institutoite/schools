@if(isset($itemsCount))
  <div class="card p-4 mb-4">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-stretch">
      <!-- Columna 1: Colegio y datos generales -->
      <div class="flex flex-col gap-2 justify-between h-full">
        <div>
          <div class="text-xs subtle splashable mb-1">Colegio seleccionado</div>
          <div class="heading font-semibold splashable text-lg md:text-xl">{{ $selectedSchool->nombre ?? ($targetName ?? '—') }}</div>
          <div class="subtle text-xs mb-1">
            @if(!empty($selectedSchool?->turno)) <span class="splashable">Turno: <span class="font-semibold">{{ $selectedSchool->turno }}</span></span>@endif
            @if(!empty($selectedSchool?->dependencia)) <span class="splashable"> • Dependencia: <span class="font-semibold">{{ $selectedSchool->dependencia }}</span></span>@endif
          </div>
          <div class="chips mt-2">
            <span class="chip interactive splashable"><i class="fas fa-globe mr-1"></i>{{ ucfirst($nivel ?? 'nacional') }}</span>
            @if(isset($anio))
              <span class="chip interactive splashable"><i class="fas fa-calendar mr-1"></i>{{ $anio }}</span>
            @endif
          </div>
        </div>
        @if(isset($selectedSchool))
          <div class="mt-2">
            <a href="{{ url('/schools/'.$selectedSchool->id) }}" class="btn btn-primary splashable"><i class="fas fa-school"></i> Ver colegio</a>
          </div>
        @endif
      </div>
      <!-- Columna 2: Reprobados y matrícula destacados -->
      <div class="flex flex-col items-center justify-center h-full">
        <div class="flex flex-col items-center">
          @if(isset($selectedRep))
            <div class="text-xs subtle mb-1">Reprobados</div>
            <div class="font-bold" style="font-size:2.2rem; color:var(--primary-color)">{{ number_format($selectedRep) }}</div>
          @endif
          @if(isset($selectedMat))
            <div class="text-xs subtle mt-2 mb-1">Matrícula</div>
            <div class="font-semibold" style="font-size:1.3rem; color:var(--primary-dark)">{{ number_format($selectedMat) }}</div>
          @endif
          @if(isset($selectedRatio))
            <div class="text-xs subtle mt-2 mb-1">% Reprobación</div>
            <div class="font-semibold" style="font-size:1.1rem; color:#ffb300">{{ number_format($selectedRatio,2) }}%</div>
          @endif
        </div>
      </div>
      <!-- Columna 3: Posición destacada y ubicación -->
      <div class="flex flex-col items-center justify-between h-full">
        <div class="pos-hero w-full h-full flex flex-col items-center justify-center">
          <div class="pos" style="font-size:3.2rem; color:var(--primary-color); font-weight:900; line-height:1;">{{ isset($pos) && $pos ? '#'.$pos : '—' }}</div>
          <div class="label mt-1">Posición @if(isset($posTotal) && $posTotal) de {{ $posTotal }} @endif</div>
        </div>
        @if(isset($autoUbic))
          <div class="chips mt-2">
            @if(!empty($autoUbic->departamento)) <span class="chip splashable"><i class="fas fa-map-marker-alt mr-1"></i>{{ $autoUbic->departamento }}</span> @endif
            @if(!empty($autoUbic->provincia)) <span class="chip splashable">{{ $autoUbic->provincia }}</span> @endif
            @if(!empty($autoUbic->municipio)) <span class="chip splashable">{{ $autoUbic->municipio }}</span> @endif
            @if(!empty($autoUbic->distrito)) <span class="chip splashable">{{ $autoUbic->distrito }}</span> @endif
          </div>
        @endif
      </div>
    </div>
  </div>
@endif
