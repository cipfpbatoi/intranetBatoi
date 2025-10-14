@extends('layouts.pdf')
@php
    $grupo = $datosInforme;
    $todos = $grupo->Alumnos;
@endphp
@if ($grupo)
    @section('content')
        @include('pdf.partials.cabeceraFSE')
        @if ($grupo->Ciclo->tipo < 3)
            @include('pdf.reunion.partials.MiS_FSE')
        @else
            @include('pdf.reunion.partials.BAS_FSE')
       @endif
    @endsection
@else
    @section('content')
        <p>Esta acta s'ha d'imprimir des de les opcions del menú i després s'ha de crear l'acta corresponent i adjuntar l'arxiu imprés. També s'ha de baixar una còpia a Caporalia signada pel tutor.</p>
    @endsection
@endif
