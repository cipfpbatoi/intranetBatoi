@extends('layouts.intranet')
@section('css')
<title> @lang("models.Reunion.detalle")</title>
@endsection
@section('content')
<div class="accordion" id="accordion" role="tablist" aria-multiselectable="true">
    @include('reunion.partials.editar')
    @include('reunion.partials.ordenes')
    @include('reunion.partials.profesores')
</div>
<a href="/reunion" class="btn btn-success">@lang("messages.buttons.atras") </a>
@endsection
@section('titulo')
    @lang("models.Reunion.detalle")
@endsection
@section('scripts')
<script src="/js/Reunion/checkAsistencia.js"></script>
<script src="/js/tabledit.js"></script>
@endsection
