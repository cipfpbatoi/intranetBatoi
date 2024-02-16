@extends('layouts.intranet')
@section('css')
<title></title>
@endsection
@section('content')
<h4 class="centrado">{{trans("models.Actividad.titulo",['actividad'=>$Actividad->name])}}</h4>
@include('extraescolares.partials.profesoresTabla')
@include('extraescolares.partials.gruposTabla')
@if (esRol(AuthUser()->rol,config('roles.rol.direccion')) && $Actividad->comentarios)
    <div class="centrado">
        <p>{{$Actividad->comentarios}}</p>
    </div>
@endif
@endsection
@section('scripts')
@endsection
