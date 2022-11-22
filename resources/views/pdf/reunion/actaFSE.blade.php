@extends('layouts.pdf')
@php
    $grupo = $datosInforme;
    $todos = $grupo->Alumnos;
@endphp
@section('content')
    @if (isset($grupo->Ciclo))
        <h2>Si has arribat ací és que <strong>NO</strong> estas fent el procediment correctament.</h2>
        <h3>Has d'imprimir l'acta en Actes/Convocatòries - Imprimir Acta FSE.</h3>
        <h4>Després en esta reunió has de pujar una còpia i no tocar res mes.</h4>
        Finalment ja podràs arxivar-la un dia després de la data de la reunió.<br/>
        Salutacions !!!
    @else
        @include('pdf.partials.cabeceraFSE')
        @if ($grupo->Ciclo->tipo < 3)
            @include('pdf.reunion.partials.MiS_FSE')
        @else
            @include('pdf.reunion.partials.BAS_FSE')
       @endif
    @endif
@endsection

