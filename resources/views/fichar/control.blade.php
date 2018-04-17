@extends('layouts.intranet')
@section('css')
<title>{{trans("models.Fichar.control")}}</title>
@endsection
@section('content')
<div id="app">
    <control-semana-view 
      :profes="{{ json_encode($profes) }}"
    ></control-semana-view>
</div>
@endsection
@section('titulo')
{{trans("models.Fichar.control")}}
@endsection
@section('scripts')
    {{ Html::script('/js/components/app.js') }}
    {{ Html::script('/js/Fichar/control.js') }}
@endsection