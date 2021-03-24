@extends('layouts.intranet')
@section('css')
    <title>{{$panel->getTitulo()}}</title>
@endsection
@section('grid')
    <div class='centrado'>@include('intranet.partials.components.buttons',['tipo' => 'index'])</div><br/>
    @include('intranet.partials.grid.vacia')
@endsection
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
@include('intranet.partials.modal.index')
@include('js.tablesjs')
@endsection
