@props(['id'])

@php
    $ciclo = \Intranet\Entities\Ciclo::find($id);
    $nombreCiclo = $ciclo?->ciclo
        ?? ($id === '' ? 'Sense cicle assignat' : "Cicle no trobat (#{$id})");
@endphp

{{ $nombreCiclo }}
