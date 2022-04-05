@extends('layouts.intranet')
@section('css')
    <title>Article {{$panel->getElemento()}}</title>
@endsection
@section('grid')
    <div class="hidden" id="article">{{$panel->getElemento()}}</div>
    @include('material.partials.grid')
@endsection
@section('titulo')
    Article {{$panel->getElemento()}}
@endsection
@section('scripts')
@include('js.tablesjs')
@endsection
