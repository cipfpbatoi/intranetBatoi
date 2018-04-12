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
    @include('includes.tablesjs')
    {{ Html::script('/js/delete.js') }}
@endsection
