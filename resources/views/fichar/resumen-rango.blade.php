@extends('layouts.intranet')

@section('css')
<title>@lang("models.Fichar.resumenDia")</title>
@endsection
@section('skip_legacy_js', '1')
@section('skip_app_js', '1')
@section('js_mode', 'vite')

@section('content')
<div
    id="app"
    data-page="resumenRango"
    data-profes='@json($profes, JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT)'
></div>
@endsection

@push('scripts')
    @vite('resources/assets/js/fichar-app.js')
@endpush
