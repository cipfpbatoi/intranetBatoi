@php
   if ($reunion = \Intranet\Entities\Reunion::where('tipo',11)->where('idProfesor',AuthUser()->dni)->first())
        $anterior = $reunion->ordenes->toArray();
   else $anterior = false;
@endphp
@extends('layouts.pdf')
@section('content')
@include('pdf.partials.cabecera')
<br/>
<table class="table table-bordered">
    <tr>
        <th><h2>{{strtoupper($datosInforme->Tipos()['vliteral'])}}</h2></th>
    </tr>
    <tr>
        <th><h4>CICLE FORMATIU DE GRAU SUPERIOR</h4></th>
    </tr>
    <tr>
        <th><h5>{{$datosInforme->Ciclo}}</h5></th>
    </tr>
    <tr>
        <th><h6>Curs {{Curso()}}</h6></th>
    </tr>
</table>
<div class="container" >
    <table class="table table-bordered">
        <tr><th>Alumne</th>
            @if ($anterior)<th>Projecte</th><th>Data i Hora</th>@else <th>Projecte - Data i Hora</th>@endif<th>Lloc</th></tr>
        @foreach ($todos as $index => $elemento)
        <tr><td>{{$elemento->descripcion}}</td>
            @if ($anterior[$index]) <td>@php echo($anterior[$index]['resumen']) @endphp</td> @endif
            <td>{{$elemento->resumen}}</td>
            <td>{{$datosInforme->Espacio->descripcion}}</td>
        @endforeach    
    </table>
</div>
<div class="container">
    <br/>
    <div style="width:60%;float:right">
        <p>SIGNATURA TUTOR: {{$datosInforme->Responsable->nombre}}  {{$datosInforme->Responsable->apellido1}}  {{$datosInforme->Responsable->apellido2}}</p>
        <br/><br/>
        <p>ALCOI A {{$datosInforme->hoy}}</p>
    </div>       
</div>
@endsection
