@extends('layouts.pdf')
@section('content')
@include('pdf.partials.cabecera')
<br/>
<table class="table table-bordered">
    <tr>
        <th>Acta d'elecció de delegats</th>
    </tr>
</table>

<div class="container col-lg-12" >
    <br/>
    <div style="width:55%;float:left"><strong>Tutor:</strong> {{$datosInforme->Responsable->nombre}} {{$datosInforme->Responsable->apellido1}}  {{$datosInforme->Responsable->apellido2}}</div>
    <div style="width:45%;float:right;text-align: right"><strong>Grup:</strong> {{$datosInforme->Xgrupo}}</div>
    <div style="width:55%;float:left;clear:both"><strong>Curs:</strong> {{$datosInforme->curso}}</div>

</div>
<br/><br/><br/>
<div class="container" style="clear: both" >
        <br/><strong> {{$todos[0]->descripcion}}:</strong>{{strip_tags($todos[0]->resumen)}}
        <br/><strong> {{$todos[1]->descripcion}}:</strong>{{strip_tags($todos[1]->resumen)}}
        @if (isset($todos[2]))<br/><strong> {{$todos[2]->descripcion}}:</strong>{{strip_tags($todos[2]->resumen)}}@endif
        @if (isset($todos[3]))<br/><strong> {{$todos[3]->descripcion}}:</strong>{{strip_tags($todos[3]->resumen)}}@endif
        @if (isset($todos[4]))<br/><strong>{{$todos[4]->descripcion}}:</strong> {{strip_tags($todos[4]->resumen)}}@endif
        @if (isset($todos[5]))<br/><strong>{{$todos[5]->descripcion}}:</strong> {{strip_tags($todos[5]->resumen)}}@endif
        @if (isset($todos[6]))<br/><strong>{{$todos[6]->descripcion}}:</strong> {{strip_tags($todos[6]->resumen)}}@endif
        @if (isset($todos[7]))<br/><strong>{{$todos[7]->descripcion}}:</strong> {{strip_tags($todos[7]->resumen)}}@endif
</div>
<div class="container">
    <br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
    <div style="width:55%;float:left">CREAT PER: {{$datosInforme->Responsable->nombre}}&nbsp;&nbsp;&nbsp;</div>
    <div style="width:45%;float:right;text-align: right">{{strtoupper(config('contacto.poblacion'))}} A {{$datosInforme->hoy}}</div>
</div>
@include('pdf.partials.pie',['document'=>'actaDelegat'])
@endsection
