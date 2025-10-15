@extends('layouts.intranet')
 @section('css')
    <title>Administrador Busties</title>
    <style>
        table, th, td {
            border: 1px solid;
        }
    </style>
    @livewireStyles
@endsection
@section('content')
    @livewire('bustia-violeta.admin-list')
@endsection
@section('titulo')
Administrador Busties
@endsection
@section('scripts')
    @livewireScripts
@endsection

