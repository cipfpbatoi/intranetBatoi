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
    @include('intranet.partials.modal.index')
    @include('intranet.partials.components.loadModals')
    @include('js.modaljs')
@endsection

