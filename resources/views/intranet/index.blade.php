@extends('layouts.intranet')
@section('css')
    <title>{{ $panel->getTitulo() }}</title>
@endsection
 <x-layouts.pestanas  :panel="$panel"  :elemento="$elemento ?? null" />
@section('titulo')
    {{ $panel->getTitulo() }}
@endsection
@section('scripts')
    @include('intranet.partials.components.loadModals')
    @include('js.js')
@endsection
