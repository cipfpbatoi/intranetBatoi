@extends('layouts.pdf')
@section('content')
@include('pdf.partials.cabecera')
<br/>
<table class="table table-bordered">
    <tr>
        <th>Acta reunió <strong> {{$datosInforme->Tipos()->vliteral}}</strong> "{{$datosInforme->Xgrupo}}"</th>
    </tr>
</table>

@include('pdf.reunion.partials.dades-acta')
@include('pdf.reunion.partials.punts')
@include('pdf.reunion.partials.asistents')
@include('pdf.reunion.partials.signatura')
@endsection
