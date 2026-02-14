@extends('layouts.intranet')
@section('css')
<title>{{$panel->getTitulo('list')}}</title>
@endsection
@php $pestana = $panel->getPestanas()[0] @endphp
@section($pestana->getNombre())
    <x-botones :panel="$panel" tipo="index" :elemento="$elemento ?? null" /><br/>
    @include($pestana->getVista(),$pestana->getFiltro())
@endsection
@section('titulo')
{{$panel->getTitulo('list')}}
@endsection
@section('scripts')
{{ Html::script('/js/list.js') }}
@endsection

