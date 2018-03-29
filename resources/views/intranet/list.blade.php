@extends('layouts.intranet')
@section('css')
<title>{{$panel->getTitulo('list')}}</title>
@endsection
@php $pestana = $panel->getPestanas()[0] @endphp
@section($pestana->getNombre())
<div class="centrado">@include('intranet.partials.buttons',['tipo' => 'index'])</div><br/>        
@include($pestana->getVista(),$pestana->getFiltro())
@endsection
@section('titulo')
{{$panel->getTitulo('list')}}
@endsection
@section('scripts')
{{ Html::script('/js/list.js') }}
@endsection

