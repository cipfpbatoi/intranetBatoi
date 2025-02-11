@extends('layouts.intranet')
@section('css')
    <title>Calendari escolar curs  {{curso()}}</title>
    @livewireStyles
@endsection
@section('content')
    @livewire('calendari-component')
@endsection
@section('titulo')
    Calendari Escolar
@endsection
@section('scripts')
    @livewireScripts
@endsection
