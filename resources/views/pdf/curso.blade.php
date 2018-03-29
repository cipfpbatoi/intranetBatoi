@extends('layouts.pdf')
@section('content')
@include('pdf.partials.cabecera')
@php $espera = 0  @endphp
<br/>
<table class="table table-bordered">
    <tr>
        <th colspan="2">Full signatures Curs <strong> {{$todos->titulo}}</strong> ({{$todos->fecha_inicio}} 
        @if ($todos->fecha_inicio != $todos->fecha_fin) - {{ $todos->fecha_fin}}  @endif
        )</th>
    </tr>
    <tr ><td style="text-align: left">@if ($todos->profesorado) Docentes: {{$todos->profesorado}}@endif</td><td style="text-align: left">Data:</td></tr> 
</table>
<div class="container" >
    @foreach ($todos->Alumnos->sortBy('apellido1') as $elemento)
        @if ($elemento->pivot->registrado == 'S')
        <div style='float:left;width:23%;margin-left:1%;margin-bottom: 20px;padding:2px;height:120px;border: 1px solid black;'>{{$elemento->apellido1}} {{$elemento->apellido2}}, {{$elemento->nombre}}</div>
        @else
            @php $espera = 1 @endphp
        @endif
    @endforeach    
</div> 
@if ($espera)
    <table class="table table-bordered">
        <tr><th>Llista d'espera </th></tr>
    </table>
    <div class="container">
        @foreach ($todos->Alumnos->sortBy('id') as $elemento)
             @if ($elemento->pivot->registrado == 'R')
             <div style='float:left;width:20%;height:100px;border: 1px solid black;'>{{$elemento->apellido1}} {{$elemento->apellido2}}, {{$elemento->nombre}}</div>
             @endif
        @endforeach    
    </div>
@endif
<br/><br/> Alcoi a {{$datosInforme}}
@endsection
