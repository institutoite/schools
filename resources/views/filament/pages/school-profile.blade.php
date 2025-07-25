
<x-filament::page>
    <!-- Buscador -->
    <x-filament::card>
        <div class="flex space-x-4 items-end">
            <x-filament::input.wrapper class="flex-1">
                <x-filament::input 
                    wire:model.live.debounce.500ms="search" 
                    placeholder="Buscar por nombre o código RUE"
                />
            </x-filament::input.wrapper>
            
            <x-filament::button 
                wire:click="searchSchool" 
                icon="heroicon-o-magnifying-glass"
            >
                Buscar
            </x-filament::button>
            
            @if($school)
                <x-filament::button 
                    wire:click="clearSearch" 
                    color="gray" 
                    icon="heroicon-o-x-mark"
                >
                    Limpiar
                </x-filament::button>
            @endif
        </div>
    </x-filament::card>

    <!-- Resultado -->
    @if($school)
        <x-filament::card>
            <!-- Encabezado -->
            <div class="flex items-start justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $school->nombre }}</h1>
                    <p class="text-gray-600">{{ $school->codigo_rue }}</p>
                </div>
                <div class="bg-gray-100 px-3 py-1 rounded-full text-sm">
                    {{ $school->dependencia }}
                </div>
            </div>

            <!-- Secciones en pestañas -->
            <x-filament::tabs>
                <!-- Información Básica -->
                <x-filament::tabs.item label="Información">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-4">
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold">Datos Generales</h3>
                            <div class="space-y-2">
                                <p><strong>Director:</strong> {{ $school->director ?? 'No especificado' }}</p>
                                <p><strong>Teléfonos:</strong> {{ $school->telefonos ?? 'No especificado' }}</p>
                                <p><strong>Dirección:</strong> {{ $school->direccion ?? 'No especificado' }}</p>
                                <p><strong>Niveles:</strong> {{ $school->niveles ?? 'No especificado' }}</p>
                                <p><strong>Turnos:</strong> {{ $school->turnos ?? 'No especificado' }}</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold">Ubicación</h3>
                            @if($school->ubicacion)
                                <div class="space-y-2">
                                    <p><strong>Departamento:</strong> {{ $school->ubicacion->departamento }}</p>
                                    <p><strong>Provincia:</strong> {{ $school->ubicacion->provincia }}</p>
                                    <p><strong>Municipio:</strong> {{ $school->ubicacion->municipio }}</p>
                                    <p><strong>Distrito:</strong> {{ $school->ubicacion->distrito }}</p>
                                    <p><strong>Área:</strong> {{ $school->ubicacion->area }}</p>
                                    @if($school->ubicacion->latitud && $school->ubicacion->longitud)
                                        <div class="mt-4 h-48 bg-gray-100 rounded-lg flex items-center justify-center">
                                            <!-- Aquí puedes integrar un mapa si lo deseas -->
                                            <p class="text-gray-500">Mapa: {{ $school->ubicacion->latitud }}, {{ $school->ubicacion->longitud }}</p>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <p class="text-gray-500">No hay información de ubicación</p>
                            @endif
                        </div>
                    </div>
                </x-filament::tabs.item>

                <!-- Infraestructura -->
                <x-filament::tabs.item label="Infraestructura">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-4">
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold">Servicios Básicos</h3>
                            @if($school->servicios)
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="flex items-center space-x-2">
                                        <x-filament::icon 
                                            icon="heroicon-o-check-circle" 
                                            class="h-5 w-5 {{ $school->servicios->agua ? 'text-success-500' : 'text-gray-300' }}" 
                                        />
                                        <span>Agua</span>
                                    </div>
                                    <!-- Repetir para otros servicios -->
                                </div>
                            @else
                                <p class="text-gray-500">No hay información de servicios</p>
                            @endif
                        </div>

                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold">Ambientes</h3>
                            @if($school->ambientes)
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p><strong>Aulas:</strong> {{ $school->ambientes->aulas ?? 0 }}</p>
                                    </div>
                                    <div>
                                        <p><strong>Laboratorios:</strong> {{ $school->ambientes->laboratorios ?? 0 }}</p>
                                    </div>
                                    <!-- Repetir para otros ambientes -->
                                </div>
                            @else
                                <p class="text-gray-500">No hay información de ambientes</p>
                            @endif
                        </div>
                    </div>
                </x-filament::tabs.item>

                <!-- Estadísticas -->
                <x-filament::tabs.item label="Estadísticas">
                    <div class="p-4">
                        @if($school->estadisticas->isNotEmpty())
                            <div class="space-y-6">
                                @foreach($school->estadisticas->groupBy('anio') as $anio => $estadisticas)
                                    <x-filament::card>
                                        <h3 class="px-4 pt-4 text-lg font-semibold">Año {{ $anio }}</h3>
                                        <div class="p-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                                            @foreach($estadisticas as $estadistica)
                                                <div class="border rounded-lg p-4">
                                                    <h4 class="font-medium">{{ $estadistica->categoria }}</h4>
                                                    <div class="grid grid-cols-3 gap-4 mt-2">
                                                        <div>
                                                            <p class="text-sm text-gray-500">Total</p>
                                                            <p>{{ $estadistica->total }}</p>
                                                        </div>
                                                        <div>
                                                            <p class="text-sm text-gray-500">Mujeres</p>
                                                            <p>{{ $estadistica->mujer }}</p>
                                                        </div>
                                                        <div>
                                                            <p class="text-sm text-gray-500">Hombres</p>
                                                            <p>{{ $estadistica->hombre }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </x-filament::card>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500">No hay estadísticas disponibles</p>
                        @endif
                    </div>
                </x-filament::tabs.item>
            </x-filament::tabs>
        </x-filament::card>
    @elseif($search)
        <x-filament::card>
            <div class="py-12 text-center">
                <p class="text-gray-500">No se encontraron colegios con ese criterio de búsqueda</p>
            </div>
        </x-filament::card>
    @else
        <x-filament::card>
            <div class="py-12 text-center">
                <p class="text-gray-500">Ingresa un nombre o código RUE para buscar un colegio</p>
            </div>
        </x-filament::card>
    @endif
</x-filament::page>

