@extends('layouts.intranet')
@section('css')
    <title>{{ trans('models.Resultado.informe')}}</title>
@endsection
@foreach ($panel->getPestanas() as $pestana)
    @section($pestana->getNombre())
        @include($pestana->getVista(),$pestana->getFiltro())
    @endsection
@endforeach
@section('titulo')
    {{ trans('models.Resultado.informe')}}
@endsection
@section('scripts')
    @include('resultado.partials.modal')
    @include('includes.tablesjs')
    {{ Html::script("/js/Resultado/informe.js") }}
@endsection
