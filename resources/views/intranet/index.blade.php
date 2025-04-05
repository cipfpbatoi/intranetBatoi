@extends('layouts.intranet')
@section('css')
    <title>{{$panel->getTitulo()}}</title>
@endsection
@foreach ($panel->getPestanas() as $pestana)
    @section($pestana->getNombre())
        @include('intranet.partials.components.loadBefores')
        <div class="centrado">
            <x-botones :panel="$panel" tipo="index" :elemento="$elemento ?? null" />
            @if ($pestana->getNombre() <> 'grid' && $pestana->getNombre() <> 'profile')
                <x-botones :panel="$panel" tipo="{{$pestana->getNombre()}}" :elemento="$elemento ?? null" /><br/>
            @endif
        </div><br/>
        @include($pestana->getVista(),$pestana->getFiltro())
    @endsection
@endforeach
@section('titulo') 
    {{$panel->getTitulo()}}
@endsection
@section('scripts')
    @include('intranet.partials.components.loadModals')
    @include('js.js')
@endsection
