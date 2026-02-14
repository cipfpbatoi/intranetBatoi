@extends('layouts.intranet')

@section('css')
<title>@lang("models.Fichar.resumenDia")</title>
@endsection

@section('content')
<div id="app">
  <control-resumen-rango-view :profes='@json($profes)'></control-resumen-rango-view>
</div>
@endsection

@section('scripts')
  {{ Html::script('/js/components/app.js') }}
@endsection
