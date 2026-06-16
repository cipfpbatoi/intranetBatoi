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

    .reunion-fe-notes-subpoint {
        margin-left: 2rem;
        border-left: 3px solid #d9edf7;
    }

    .reunion-fe-student-name {
        color: #31708f;
        font-weight: 700;
    }

    .reunion-fe-note-observations {
        margin-top: 6px;
    }

    .reunion-fe-student-panel {
        border: 1px solid #d9edf7;
        border-radius: 4px;
        margin-top: 12px;
        padding: 12px;
    }

    .reunion-fe-module-row {
        border-top: 1px solid #eee;
        display: inline-block;
        margin-top: 10px;
        margin-right: 12px;
        min-width: 180px;
        padding-top: 10px;
        vertical-align: top;
        width: 220px;
    }

    .reunion-fe-module-row label {
        display: block;
        margin-bottom: 4px;
    }

    .reunion-fe-exclude-student {
        display: block;
        margin-top: 8px;
        font-weight: 400;
    }
</style>
@endsection
@section('content')
<div class="accordion" id="accordion" role="tablist" aria-multiselectable="true">
    @include('reunion.partials.editar')
    @include('reunion.partials.ordenes')
    @if (
        $formulario->getElemento()->avaluacioFinal
        && isset($feNotesData)
        && ($feNotesData['fcts']->isNotEmpty() || $formulario->getElemento()->mostraNotesFe)
    )
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
