@extends('layouts.intranet')
 @section('css')
    <title>Bustia Violeta</title>
    <style>
        table, th, td {
            border: 1px solid;
        }
    </style>
    @livewireStyles
@endsection
@section('content')
    @livewire('bustia-violeta.form')
@endsection
@section('titulo')
Bustia Violeta
@endsection
@section('scripts')
    @livewireScripts
@endsection

