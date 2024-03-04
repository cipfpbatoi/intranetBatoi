@extends('layouts.intranet')
@section('css')
    <title>{{$panel->getTitulo()}}</title>
@endsection
@foreach ($panel->getPestanas() as $pestana)
    @section($pestana->getNombre())
        <div class="centrado">@include('intranet.partials.components.buttons',['tipo' => 'index'])</div><br/>
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

