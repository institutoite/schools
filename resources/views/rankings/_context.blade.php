@if(isset($itemsCount))
  <div class="card p-4 mb-4">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-stretch">
      <div class="flex flex-col gap-2">
        <div class="text-xs subtle splashable">Colegio seleccionado</div>
        <div class="heading font-semibold splashable">{{ $selectedSchool->nombre ?? ($targetName ?? '—') }}</div>
        <div class="subtle text-xs">
          @if(!empty($selectedSchool?->turno)) <span class="splashable">Turno: <span class="font-semibold">{{ $selectedSchool->turno }}</span></span>@endif
          @if(!empty($selectedSchool?->dependencia)) <span class="splashable"> • Dependencia: <span class="font-semibold">{{ $selectedSchool->dependencia }}</span></span>@endif
        </div>
        <div class="chips mt-2">
          <span class="chip interactive splashable"><i class="fas fa-globe mr-1"></i>{{ ucfirst($nivel ?? 'nacional') }}</span>
          @if(isset($anio))
            <span class="chip interactive splashable"><i class="fas fa-calendar mr-1"></i>{{ $anio }}</span>
          @endif
        </div>
        <div class="flex flex-wrap gap-4 text-sm mt-2">
          @if(isset($selectedRep))
            <div class="splashable"><span class="tag">Reprobados</span> <span class="heading font-semibold">{{ number_format($selectedRep) }}</span></div>
          @endif
          @if(isset($selectedMat))
            <div class="splashable"><span class="tag">Matrícula</span> <span class="heading font-semibold">{{ number_format($selectedMat) }}</span></div>
          @endif
          @if(isset($selectedRatio))
            <div class="splashable"><span class="tag">% Reprobación</span> <span class="heading font-semibold">{{ number_format($selectedRatio,2) }}%</span></div>
          @endif
        </div>
        @if(isset($selectedSchool))
          <div class="mt-2">
            <a href="{{ url('/schools/'.$selectedSchool->id) }}" class="btn btn-primary splashable"><i class="fas fa-school"></i> Ver colegio</a>
          </div>
        @endif
      </div>
      <div>
        @if(isset($autoUbic))
          <div class="text-xs subtle mb-1">Ubicación</div>
          <div class="chips">
            @if(!empty($autoUbic->departamento)) <span class="chip splashable"><i class="fas fa-map-marker-alt mr-1"></i>{{ $autoUbic->departamento }}</span> @endif
            @if(!empty($autoUbic->provincia)) <span class="chip splashable">{{ $autoUbic->provincia }}</span> @endif
            @if(!empty($autoUbic->municipio)) <span class="chip splashable">{{ $autoUbic->municipio }}</span> @endif
            @if(!empty($autoUbic->distrito)) <span class="chip splashable">{{ $autoUbic->distrito }}</span> @endif
          </div>
        @endif
      </div>
      <div>
        <div class="pos-hero w-full h-full">
          <div class="pos">{{ isset($pos) && $pos ? '#'.$pos : '—' }}</div>
          <div class="label">Posición @if(isset($posTotal) && $posTotal) de {{ $posTotal }} @endif</div>
        </div>
      </div>
    </div>
  </div>
@endif
