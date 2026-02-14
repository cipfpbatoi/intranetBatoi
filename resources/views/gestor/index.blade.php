@extends('layouts.intranet')
@section('css')
   <title>{{$panel->getTitulo()}}</title>
@endsection
@foreach ($panel->getPestanas() as $pestana)
    @section($pestana->getNombre())
        <x-botones :panel="$panel" tipo="index" :elemento="$elemento ?? null" /><br/>
         @include($pestana->getVista(),$pestana->getFiltro())
    @endsection
@endforeach
@section('titulo')
    {{$panel->getTitulo()}}
@endsection
@section('scripts')
    @include('js.tablesjs')
    {{ HTML::script('/js/Gestor/grid.js') }}
@endsection

