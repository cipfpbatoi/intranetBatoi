@php
   if ($reunion = \Intranet\Entities\Reunion::where('tipo',11)->where('idProfesor',AuthUser()->dni)->orderBy('fecha','desc')->first())
        $anterior = $reunion->ordenes->sortBy('orden');
   else
        $anterior = false;
   $ciclo = \Intranet\Entities\Ciclo::where('ciclo',$datosInforme->Ciclo)->count()?\Intranet\Entities\Ciclo::where('ciclo',$datosInforme->Ciclo)->first()->literal:$datosInforme->Ciclo;
   $all = $todos->sortBy('resumen');
@endphp
@extends('layouts.pdf')
@section('content')
@include('pdf.partials.cabecera')
<div class="container" >
<table class="table table-bordered" style='font-size: large;width: 90%'>
    <tr>
        <th><h4>{{strtoupper($datosInforme->Tipos()->vliteral)}}</h4></th>
    </tr>
    <tr>
        <th>    
            <h5>CICLE FORMATIU DE GRAU SUPERIOR {{$ciclo}}</h5>
            <h5>Curs {{Curso()}}</h5>
        </th>
    </tr>
</table>
</div>
<div class="container" >
    <table class="table table-bordered" style='font-size: large'>
        <tr><th>Alumne</th>
        <th>Projecte - Data i Hora</th>
        <th>Lloc</th></tr>

        @foreach ($all as $index => $elemento)
        <tr><td style='font-size: large'>{{$elemento->descripcion}}</td>
            <td style='font-size: 0.8em'>{!! $elemento->resumen !!}</td>
            <td style='font-size: 0.8em'>{{ $datosInforme->Espacio->descripcion }}</td>
        </tr>
        @endforeach    
    </table>
</div>
<div class="container">
    <br/>
    <div style="width:60%;float:right">
        <p>SIGNATURA TUTOR: {{$datosInforme->Responsable->nombre}}  {{$datosInforme->Responsable->apellido1}}  {{$datosInforme->Responsable->apellido2}}</p>
        <br/><br/>
        <p>{{strtoupper(config('contacto.poblacion'))}} A {{$datosInforme->hoy}}</p>
    </div>       
</div>
@endsection
