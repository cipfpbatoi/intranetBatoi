@extends('layouts.intranet')
@section('css')
    <title>{{$panel->getTitulo()}}</title>
@endsection
@section('grid')
    @include('fichar.partials.formulario')
    @include('fichar.partials.grid')
@endsection
@section('titulo')
    {{$panel->getTitulo()}}
@endsection
@section('scripts')
    {{ HTML::script('/js/delete.js') }}
@endsection
