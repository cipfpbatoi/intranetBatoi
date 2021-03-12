@extends('layouts.intranet')
@section('css')
    <title>Documentos</title>
@endsection
@if ($panel->countPestana() > 0)
    @foreach ($panel->getPestanas() as $pestana)
        @section($pestana->getNombre())
            @include($pestana->getVista(),$pestana->getFiltro())
        @endsection
    @endforeach
@endif
@section('titulo')
    Documentos
@endsection
@section('scripts')
    @include('js.tablesjs')
    {{ HTML::script('/js/grid.js') }}
@endsection