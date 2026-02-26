@extends('layouts.intranet')
@section('css')
<title>{{$panel->getTitulo('list')}}</title>
@endsection
@php $pestana = $panel->getPestanas()[0] @endphp
@section($pestana->getNombre())
<table width='70%'>
    <tr><th colspan="2" style='text-align: center'><h3>Informe Ausencias Dia {{$panel->dia}}</h3></th></tr>
    <tr>
        <td width='50%' style="text-align: right; padding-right:40px;"><h4><a href="{{ route('fichar.list', ['dia' => $panel->anterior]) }}"> <- Dia anterior</a></h4></td>
        <td width='50%'><h4><a href="{{ route('fichar.list', ['dia' => $panel->posterior]) }}">Dia posterior -></a></h4></td>
    </tr>
</table>
@include($pestana->getVista(),$pestana->getFiltro())
@endsection
@section('titulo')
{{$panel->getTitulo('list')}}
@endsection
@section('scripts')
{{ Html::script("/js/list.js") }}
@endsection
