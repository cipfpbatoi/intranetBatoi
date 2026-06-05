@extends('layouts.intranet')
@section('css')
<title> @lang("models.Reunion.detalle")</title>
<style>
    .reunion-richtext-wrapper {
        width: 100%;
    }

    .reunion-richtext-toolbar {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-bottom: 8px;
    }

    .reunion-richtext-editor {
        min-height: 140px;
        height: auto;
        overflow: auto;
    }
</style>
@endsection
@section('content')
<div class="accordion" id="accordion" role="tablist" aria-multiselectable="true">
    @include('reunion.partials.editar')
    @include('reunion.partials.ordenes')
    @if ($formulario->getElemento()->avaluacioFinal && isset($feNotesData) && $feNotesData['fcts']->isNotEmpty())
        @include('reunion.partials.fe-notes')
    @endif
    @include('reunion.partials.profesores')
    @if ($formulario->getElemento()->informe)
        @include('reunion.partials.alumnos')
    @endif
</div>
<a href="{{ route('reunion.index') }}" class="btn btn-success">@lang("messages.buttons.atras") </a>
@endsection
@section('titulo')
    @lang("models.Reunion.detalle")
@endsection
@section('scripts')
    @if ($formulario->getElemento()->informe )
        <script src="/js/Reunion/valoracioAlumnat.js"></script>
    @endif
<script src="/js/Reunion/checkAsistencia.js"></script>
<script src="/js/tabledit.js"></script>
<script src="/js/Reunion/richText.js"></script>
@endsection
