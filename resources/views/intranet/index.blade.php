@extends('layouts.intranet')

 <x-layouts.pestanas  :panel="$panel"  :elemento="$elemento ?? null" />
@section('titulo',$panel->getTitulo())
@section('scripts')
    @include('intranet.partials.components.loadModals')
    @include('js.js')
@endsection
