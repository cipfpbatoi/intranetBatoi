@extends('layouts.intranet')
@section('css')
    <title>@lang("models.fctDay.show")</title>
    <style>
        table, th, td {
            border: 1px solid;
        }
    </style>
    @livewireStyles
@endsection
@section('content')
    @livewire('fct-calendar', ['alumno' => $alumno])
@endsection
@section('titulo')
    @lang("models.fctDay.show" ,['quien'=> $alumno->fullName])
@endsection
@section('scripts')
    @livewireScripts
@endsection
