@extends('layouts.pdf')
@section('content')
@include('pdf.partials.cabecera')
<br/>
<table class="table table-bordered">
    <tr>
        <th>Acta reunió <strong> {{$datosInforme->Tipos()['vliteral']}}</strong> "{{$datosInforme->Xgrupo}}"</th>
    </tr>
</table>

<div class="container col-lg-12" >
    Acta número <strong>{{$datosInforme->numero}}</strong> curs <strong>{{$datosInforme->curso}}</strong> del dia 
   <strong>{{$datosInforme->dia}}</strong> a les <strong>{{$datosInforme->hora}}</strong>
</div>
<div class="container" >
    <br/>
    <strong>Punts tractats:</strong>
    <ul style='list-style:none'>
        @foreach ($todos as $elemento)
        <li>{{$elemento->orden}}. <strong>{{$elemento->descripcion}}</strong>:</li>
        <li class="ident">@php echo($elemento->resumen) @endphp</li>
        @endforeach
    </ul>
</div>
@if ($datosInforme->informe)
    <div class="container">
        <br/>
        <strong>Continguts NO impartits per mòdul</strong>
        <ul style='list-style:none'>
            @foreach ($datosInforme->grupoClase->Modulos as $modulo)
                @foreach ($modulo->resultados->where('evaluacion',3) as $res)
                    <li><strong>{{$modulo->Xmodulo}}</strong>: {{$res->adquiridosNO}}</li>
                @endforeach
             @endforeach
        </ul>
    </div>
@endif
@if ($datosInforme->informe)
    <div class="container">
        <br/>
        <strong>Promoció de l'alumnat</strong>
        <ul style='list-style:none'>
            @foreach ($datosInforme->alumnos()->orderBy('apellido1')->orderBy('apellido2')->get() as $alumno)
                <strong><li>{{ $alumno->nameFull }} - @if ($alumno->pivot->capacitats == 3) NO @else SI @endif</strong> - {{config('auxiliares.promociona')[$alumno->pivot->capacitats]}}</li>
            @endforeach
        </ul>
    </div>
@endif
<div class="container">
    <br/>
    <div style="width:50%;float:left">
    <strong>Assistents:</strong>
    <ul style='list-style:none'>
        @foreach ($datosInforme->profesores as $profesor)
            @if ($profesor->pivot->asiste == 1)
                <li>{{$profesor->nombre}} {{$profesor->apellido1}} {{$profesor->apellido2}}</li>
            @endif
        @endforeach    
    </ul>
    </div>
    <div style="width:50%;float:right">
    <strong>Absents:</strong>
    <ul style='list-style:none'>
        @foreach ($datosInforme->profesores as $profesor)
            @if ($profesor->pivot->asiste == 0)
                <li>{{$profesor->nombre}} {{$profesor->apellido1}} {{$profesor->apellido2}}</li>
            @endif
        @endforeach    
    </ul>
    </div>
</div>
<div class="container" style="clear: both">
    <br/>
    <div style="width:50%;float:left">SIGNAT: {{$datosInforme->Responsable->nombre}}  {{$datosInforme->Responsable->apellido1}}  {{$datosInforme->Responsable->apellido2}}</div>
    <div style="width:50%;float:right;text-align: right">{{strtoupper(config('contacto.poblacion'))}} A {{$datosInforme->hoy}}</div>
</div>
@if ($datosInforme->informe)
    <div class="container" style="page-break-before: always">
        <br/>
        <strong>Annex: Valoració tasca de l'alumnat</strong><br/>
        @foreach ($datosInforme->alumnos()->orderBy('apellido1')->orderBy('apellido2')->get() as $alumno)
            <p style="text-indent: 2em"><strong>{{$alumno->nameFull}}</strong>
            @foreach ($alumno->AlumnoResultado as $resultado)
                <strong>{{ $resultado->modulo }}</strong> - {{$resultado->valoracion}}.<br/>
                @if ($resultado->observaciones) - {{$resultado->observaciones}} <br/> @endif
            @endforeach
            </p>
        @endforeach
    </div>
@endif
@endsection

