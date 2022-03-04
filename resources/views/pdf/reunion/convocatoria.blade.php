@extends('layouts.pdf')
@section('content')
@include('pdf.partials.cabecera')
<br/>
<table class="table table-bordered">
    <tr>
        <th>Convocatòria reunió <strong> {{$datosInforme->Tipos()->vliteral}}</strong> "{{$datosInforme->Xgrupo}}"</th>
    </tr>
    @if ($datosInforme->descripcion) <tr><td> {{$datosInforme->descripcion}}</td></tr> @endif
    @if ($datosInforme->objetivos) <tr><td> {{$datosInforme->objetivos}}</td></tr> @endif
</table>

<div class="container col-lg-12" >
   Estimat/da company/a:
</div>
<div class="container" >
    <p>El proper dia <strong>{{$datosInforme->dia}}</strong> a les <strong>{{$datosInforme->hora}}</strong>, et convoque a l/la<strong> {{$datosInforme->Tipos()->vliteral}}</strong>
        que farem a l/la <strong>{{$datosInforme->Espacio->descripcion}}</strong> amb el següent ordre del dia.<p>
    <strong>Punts a tractar:</strong>
    <ul style='list-style:none'>
        @foreach ($todos as $elemento)
            <li>{{$elemento->orden}}.{{$elemento->descripcion}}</li>
        @endforeach    
    </ul>       
</div>
@include('pdf.reunion.partials.signatura')
@endsection
