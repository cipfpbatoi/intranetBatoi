@extends('layouts.intranet')

@section('css')
<title>@lang("models.Fichar.resumenDia")</title>
@endsection
@section('skip_legacy_js', '1')

@section('content')
<div id="app">
  <control-resumen-rango-view :profes='@json($profes)'></control-resumen-rango-view>
</div>
@endsection
