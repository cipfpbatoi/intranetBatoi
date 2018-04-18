@extends('layouts.intranet')
@section('css')
<title>{{trans("models.Guardia.control")}}</title>
@endsection
@section('content')
<div id="app">
    <control-guardia-view 
      :horas="{{ json_encode($horas) }}"
      :profes-guardia="{{ json_encode($arrayG) }}"
      :dias="{{ json_encode($dias) }}"
    ></control-guardia-view>
</div>
@endsection
@section('titulo')
{{trans("models.Guardia.control")}}
@endsection
@section('scripts')
    {{ Html::script('/js/components/app.js') }}
    {{ Html::script('/js/Guardia/control.js') }}
@endsection