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
    @livewire('fct-calendar', ['alumnoFct' => $alumnoFct])
@endsection
@section('titulo')
    @lang("models.fctDay.show" ,['quien'=> $alumnoFct->fullName])
@endsection
@section('scripts')
    @livewireScripts
@endsection
