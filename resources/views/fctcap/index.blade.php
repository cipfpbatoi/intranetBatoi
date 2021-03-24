@extends('layouts.intranet')
@section('css')
    <title>{{$panel->getTitulo()}}</title>
@endsection
@section('grid')
<div id="_grupo">{{$panel->getElemento()}}</div>
    @include('fctcap.partials.grid')
@endsection
@section('titulo')
    {{$panel->getTitulo()}}
@endsection
@section('scripts')
@include('js.tablesjs')
@endsection
