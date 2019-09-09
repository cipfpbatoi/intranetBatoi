@extends('layouts.intranet')
@section('css')
    <title>{{$panel->getTitulo()}}</title>
@endsection
@foreach ($panel->getPestanas() as $pestana)
    @section($pestana->getNombre())

        @include('intranet.partials.before')
        <div class="centrado">
            @include('intranet.partials.buttons',['tipo' => 'index'])
            @include('intranet.partials.buttons',['tipo' => $pestana->getNombre()])
        </div><br/>
        @include($pestana->getVista(),$pestana->getFiltro())
    @endsection
@endforeach
@section('titulo') 
    {{$panel->getTitulo()}}
@endsection
@section('scripts')
    @include('intranet.partials.modal')
    @include('intranet.partials.js') 
@endsection

