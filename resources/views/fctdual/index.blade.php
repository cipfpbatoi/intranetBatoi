@extends('layouts.intranet')
@section('css')
    <title>{{$panel->getTitulo()}}</title>
@endsection
@section('grid')
<div id="_grupo">{{$panel->getElemento()}}</div>
    @include('fctdual.partials.grid')
    @include('intranet.partials.modal.aviso')
@endsection
@section('titulo')
    {{$panel->getTitulo()}}
@endsection

@section('scripts')
@include('js.tablesjs')
{{ Html::script("/js/Fctdual/modal.js") }}
@endsection
