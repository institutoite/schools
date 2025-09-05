{{-- filepath: c:\xampp\htdocs\schools\resources\views\schools\ranking_aplazados.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-10 px-4">
    <h1 class="text-2xl font-bold mb-6 text-primary">Ranking de colegios con más aplazados</h1>
    <ul class="list-disc pl-6">
        @foreach($schools as $school)
            <li class="mb-2 text-secondary">
                <strong>{{ $school->nombre }}</strong>
                @if($school->ubicacion)
                    <span>({{ $school->ubicacion->departamento }})</span>
                @endif
            </li>
        @endforeach
    </ul>
    <div class="py-4 flex justify-center">
        {{ $schools->links() }}
    </div>
</div>