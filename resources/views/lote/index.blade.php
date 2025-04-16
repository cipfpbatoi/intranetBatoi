@extends('layouts.intranet')
@section('css')
    <title>{{$panel->getTitulo()}}</title>
@endsection
@section('grid')
    <x-botones :panel="$panel" tipo="index" :elemento="$elemento ?? null" /><br/>
    <x-grid.table :panel="$panel" :mostrarBody="false" />
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
