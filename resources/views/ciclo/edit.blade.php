@extends('layouts.intranet')
@section('css')
    <title>{{trans("models.ciclo.edit")}}</title>
@endsection
@section('content')
    {{ $formulario->render('put') }}
@endsection
@section('titulo')
    {{trans("models.ciclo.edit")}}
@endsection
@section('scripts')
    {{ Html::script("/js/datepicker.js") }}
@endsection
