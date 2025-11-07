@extends('layouts.intranet')
@section('css')
<title>@lang("models.Fichar.resumenDia")</title>
@endsection
@section('content')
<div id="app">
  <control-resumen-dia-view :departaments='@json($departaments ?? [])'></control-resumen-dia-view>
</div>
@endsection
@section('scripts')
  {{ Html::script('/js/components/app.js') }}
@endsection