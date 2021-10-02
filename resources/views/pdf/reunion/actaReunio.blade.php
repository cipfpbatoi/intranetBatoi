@extends('layouts.pdf')
@section('content')
@include('pdf.partials.cabecera')
<br/>
<table class="table table-bordered">
    <tr>
        <th>Acta reunió <strong> {{$datosInforme->Tipos()->vliteral}}</strong> "{{$datosInforme->Xgrupo}}"</th>
    </tr>
</table>

<div class="container col-lg-12" >
    Acta número <strong>{{$datosInforme->numero}}</strong> curs <strong>{{$datosInforme->curso}}</strong> del dia 
   <strong>{{$datosInforme->dia}}</strong> a les <strong>{{$datosInforme->hora}}</strong>
</div>
@include('pdf.reunion.partials.punts')
@include('pdf.reunion.partials.asistents')
@include('pdf.reunion.partials.signatura')
@endsection

