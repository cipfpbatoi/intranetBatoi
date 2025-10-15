@extends('layouts.pdf')
@php
    $grupo = $datosInforme;
    $todos = $grupo->Alumnos;
@endphp
@if (is_object($grupo->Ciclo))
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
        <h3>Error</h3>
        <h5>Segueix les següents instruccions</h5>
        <p>Per a generar l'acta d'una reunió de FSE cal que:</
        <ul>
            <li>Imprimir-la des de la opció del menú Actes/Acta FSE</li>
            <li>Signar-la</li>
            <li>Adjuntar-la a esta Acta reunió en la que estas</li>
            <li>Baixar-la a Caporalia</li>
        </ul>
    @endsection
@endif
