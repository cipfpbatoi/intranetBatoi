@extends('layouts.intranet')
@section('css')
    <title>{{$panel->getTitulo()}}</title>
@endsection
@foreach ($panel->getPestanas() as $pestana)
    @section($pestana->getNombre())
        <div class="centrado">@include('intranet.partials.buttons',['tipo' => 'index'])</div><br/>
        @include($pestana->getVista(),$pestana->getFiltro())
    @endsection
@endforeach
@section('titulo')
    {{$panel->getTitulo()}}
@endsection
@section('scripts')
    @include('intranet.partials.modal.explicacion')
    @include('intranet.partials.modal.aviso')
    @include('includes.tablesjs')
    @if (file_exists('js/'.$panel->getModel().'/grid.js'))
        {{ HTML::script('/js/'.$panel->getModel().'/grid.js') }}
    @else
        {{ HTML::script('/js/grid.js') }}
    @endif
    {{ HTML::script('/js/delete.js') }}
@endsection

