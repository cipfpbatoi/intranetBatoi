@extends('layouts.intranet')
@section('css')
    <link rel="stylesheet" href="{{ asset('plugins/dropzone/dropzone.css') }}">
@endsection
@section('content')
<h4 class="centrado">{{__("models.".ucfirst($modelo).".titulo",['quien'=>$quien])}}</h4>
@include('dropzone.partials.value')
@endsection
@section('scripts')
    {{ Html::script('/plugins/dropzone/dropzone.min.js') }}
    {{ Html::script('/js/dropzone/link.js') }}
@endsection
