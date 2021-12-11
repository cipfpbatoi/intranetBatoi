@extends('layouts.intranet')
@section('css')
<title></title>
@endsection
@section('content')
<h4 class="centrado">{{trans("models.Actividad.titulo",['actividad'=>$Actividad->name])}}</h4>
@include('extraescolares.partials.value')
@endsection
@section('scripts')
    {{ Html::script('/js/Actividad/img.bo.js') }}
@endsection
