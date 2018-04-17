@extends('layouts.intranet')
@section('css')
<title>BBB-{{trans("models.Fichar.control")}}</title>
@endsection
@section('content')
<div id="app">
    <control-dia-view 
      :profes="{{ json_encode($profes) }}"
      :horario-inicial="{{ json_encode($horarios) }}"
    ></control-dia-view>
</div>
@endsection
@section('titulo')
{{trans("models.Fichar.control")}}
@endsection
@section('scripts')
    {{ Html::script('/js/components/app.js') }}
    {{ Html::script('/js/Fichar/controlDia.js') }}
@endsection
