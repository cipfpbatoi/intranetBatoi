@extends('layouts.intranet')
@section('css')
    <title>Espai {{$panel->getElemento()}}</title>
@endsection
@section('grid')
    <div class="hidden" id="search">{{$panel->getElemento()}}</div>
    @include('material.partials.grid')
@endsection
@section('titulo')
    Espai {{$panel->getElemento()}}
@endsection
@section('scripts')
@include('js.tablesjs')
@endsection
