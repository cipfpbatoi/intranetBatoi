@extends('layouts.intranet')
@section('css')
<title>Extraescolars</title>
@endsection
@section('content')
<h4 class="centrado">{{trans("models.Actividad.titulo",['actividad'=>$Actividad->name])}}</h4>
@include('extraescolares.partials.profesoresTabla')
@include('extraescolares.partials.gruposTabla')
@endsection
@section('scripts')
@endsection
