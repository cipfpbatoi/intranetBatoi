@extends('layouts.intranet')
@section('css')
    <title>{{$panel->getTitulo()}}</title>
@endsection
@foreach ($panel->getPestanas() as $pestana)
    @php ($nombre = $pestana->getNombre())
    @section($nombre)
        @include('intranet.partials.components.loadBefores')
        <div class="centrado">
            <x-botones :panel="$panel" tipo="index" :elemento="$elemento ?? null"/>
            @if ($nombre !== 'grid' && $nombre !== 'profile')
                <x-botones :panel="$panel" :tipo="$nombre" :elemento="$elemento ?? null"/>
            @endif
        </div><br/>
        @include($pestana->getVista(),$pestana->getFiltro())
    @endsection
@endforeach
@section('titulo')
    {{$panel->getTitulo()}}
@endsection
@section('scripts')
    <!-- Modal Nou -->
    <x-modal name="dialogo" title=''
             message='Guardar' clase='modal-lg'>
    </x-modal>
    <x-modal name="materiales" title=''
             message='Guardar'>
    </x-modal>
    {{ Html::script("/js/barcode.js") }}
@endsection
