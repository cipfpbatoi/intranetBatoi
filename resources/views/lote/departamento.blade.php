@extends('layouts.intranet')
@section('css')
    <title>{{$panel->getTitulo()}}</title>
@endsection
@foreach ($panel->getPestanas() as $pestana)
    @section($pestana->getNombre())
        @include('intranet.partials.components.loadBefores')
        <div class="centrado">
            @include('intranet.partials.components.buttons',['tipo' => 'index'])
            @if ($pestana->getNombre() <> 'grid' && $pestana->getNombre() <> 'profile')
                @include('intranet.partials.components.buttons',['tipo' => $pestana->getNombre()])
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
             message='Guardar' >
    </x-modal>
    {{ Html::script("/js/barcode.js") }}
@endsection
