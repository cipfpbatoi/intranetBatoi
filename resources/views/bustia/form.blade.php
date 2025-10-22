@extends('layouts.intranet')
 @section('css')
    <title>Bustia</title>
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
Bustia
@endsection
@section('scripts')
    @livewireScripts
@endsection

