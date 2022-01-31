@extends('layouts.intranet')
@section('css')
<title></title>
@endsection
@section('content')
<h4 class="centrado">{{trans("models.Expediente.titulo",['expediente'=>$expediente->Alumno->fullName])}}</h4>
@include('expediente.partials.value')
@endsection
@section('scripts')
    {{ Html::script('/js/Expediente/link.js') }}
@endsection
