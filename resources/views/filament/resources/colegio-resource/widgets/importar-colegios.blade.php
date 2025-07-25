<x-filament::card>
    <x-filament::card.heading>
        Importar Colegios desde JSON
    </x-filament::card.heading>

    <x-filament::card.content>
        {{ $this->form }}

        <x-filament::button 
            wire:click="submit"
            type="button"
            class="mt-4"
        >
            Procesar Importación
        </x-filament::button>
    </x-filament::card.content>
</x-filament::card>