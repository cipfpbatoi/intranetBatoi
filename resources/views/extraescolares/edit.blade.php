@extends('layouts.intranet')
@section('css')
<title></title>
@endsection
@section('content')
<h4 class="centrado">{{trans("models.Actividad.titulo",['actividad'=>$Actividad->name])}}</h4>
@include('extraescolares.partials.profesoresTabla')
@include('extraescolares.partials.gruposTabla')
<a @if ($Actividad->extraescolar) href="/actividad" @else  href="/actividadOrientacion" @endif class="btn btn-success">@lang("messages.buttons.atras") </a>
@endsection
@section('scripts')
@endsection
