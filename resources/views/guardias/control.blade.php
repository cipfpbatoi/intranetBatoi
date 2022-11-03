@extends('layouts.intranet')
@section('css')
    <title>@lang("models.Guardia.control")</title>
    <style>
        table, th, td {
            border: 1px solid;
        }
    </style>
    @livewireStyles
@endsection
@section('content')
    @livewire('controlguardia')
@endsection
@section('titulo')
@lang("models.Guardia.control")
@endsection
@section('scripts')
    @livewireScripts
@endsection
