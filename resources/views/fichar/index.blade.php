@extends('layouts.intranet')
@section('css')
    <title>{{$panel->getTitulo()}}</title>
@endsection
@section('grid')
    @include('fichar.partials.formulario')
    @foreach ($panel->getPestanas() as $pestana)
       @include('intranet.partials.grid.stnoOp')
    @endforeach
@endsection
@section('titulo')
    {{$panel->getTitulo()}}
@endsection
@section('scripts')
    @include('includes.tablesjs')
    {{ Html::script('/js/delete.js') }}
@endsection
