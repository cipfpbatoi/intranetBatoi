@extends('layouts.intranet')
@php($title=ucfirst($modelo))
@section('css')
<title></title>
@endsection
@section('content')
<h4 class="centrado">{{trans("models.$title.titulo",[$modelo=>$registre->quien])}}</h4>
@include('dropzone.partials.value')
@endsection
@section('scripts')
    {{ Html::script('/js/dropzone/link.js') }}
@endsection
