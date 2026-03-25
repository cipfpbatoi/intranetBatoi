@extends('layouts.intranet')

@section('css')
<title>@lang("models.Fichar.resumenDia")</title>
@endsection
@section('skip_legacy_js', '1')
@section('skip_app_js', '1')
@section('js_mode', 'vite')

@section('content')
<div id="app">
  <control-resumen-rango-view :profes='@json($profes)'></control-resumen-rango-view>
</div>
@endsection

@push('scripts')
    @vite('resources/assets/js/fichar-app.js')
@endpush
