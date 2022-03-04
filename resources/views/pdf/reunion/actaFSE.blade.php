@extends('layouts.pdf')
@php
    $grupo = $datosInforme;
    $todos = $grupo->Alumnos;
@endphp
@section('content')
    @include('pdf.partials.cabeceraFSE')
    @if ($grupo->Ciclo->tipo < 3)
        @include('pdf.reunion.partials.MiS_FSE')
    @else
        @include('pdf.reunion.partials.BAS_FSE')
    @endif
@endsection

