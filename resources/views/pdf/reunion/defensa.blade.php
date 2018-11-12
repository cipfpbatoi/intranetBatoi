@php
   if ($reunion = \Intranet\Entities\Reunion::where('tipo',11)->where('idProfesor',AuthUser()->dni)->orderBy('fecha')->first())
        $anterior = $reunion->ordenes->toArray();
   else $anterior = false;
   $ciclo = \Intranet\Entities\Ciclo::where('ciclo',$datosInforme->Ciclo)->count()?\Intranet\Entities\Ciclo::where('ciclo',$datosInforme->Ciclo)->first()->literal:$datosInforme->Ciclo;
@endphp
@extends('layouts.pdf')
@section('content')
@include('pdf.partials.cabecera')
<br/>
<table class="table table-bordered">
    <tr>
        <th><h3>{{strtoupper($datosInforme->Tipos()['vliteral'])}}</h3></th>
    </tr>
    <tr>
        <th>    
            <h4>CICLE FORMATIU DE GRAU SUPERIOR</h4>
            <h3>{{$ciclo}}</h3>
            <h5>Curs {{Curso()}}</h5>
        </th>
    </tr>
</table>
<div class="container" >
    <table class="table table-bordered" style='font-size: large'>
        <tr><th>Alumne</th>
            @if ($anterior)<th>Projecte</th><th>Data i Hora</th>@else <th colspan='2'>Projecte - Data i Hora</th>@endif<th>Lloc</th></tr>
        @foreach ($todos as $index => $elemento)
        <tr><td style='font-size: large'>{{$elemento->descripcion}}</td>
            @if (isset($anterior[$index])) <td>@php echo($anterior[$index]['resumen']) @endphp</td><td>@php echo($elemento->resumen) @endphp</td>
            @else <td colspan='2'>@php echo($elemento->resumen) @endphp</td>
            @endif
            
            <td style='font-size: 0.8em'>{{$datosInforme->Espacio->descripcion}}</td>
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
