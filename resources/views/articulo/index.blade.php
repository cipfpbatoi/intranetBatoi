@extends('layouts.intranet')
@php($article = \Intranet\Entities\Articulo::find($panel->getElemento())->descripcion)
@section('css')
    <title>Article {{$article}}</title>
@endsection
@section('grid')
    <div class="hidden" id="article">{{$panel->getElemento()}}</div>
    @include('material.partials.grid')
@endsection
@section('titulo')
    Article {{$article}}
@endsection
@section('scripts')
@include('js.tablesjs')
@endsection
