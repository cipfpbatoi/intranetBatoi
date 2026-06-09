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

    .reunion-fe-notes-table-wrapper {
        overflow-x: auto;
    }

    .reunion-fe-notes-table {
        border-collapse: separate;
        border-spacing: 0;
    }

    .reunion-fe-notes-table th,
    .reunion-fe-notes-table td {
        min-width: 180px;
        vertical-align: top;
    }

    .reunion-fe-notes-table th:first-child,
    .reunion-fe-notes-table td:first-child {
        background: #fff;
        box-shadow: 6px 0 8px -8px rgba(0, 0, 0, 0.35);
        left: 0;
        min-width: 220px;
        position: sticky;
        z-index: 2;
    }

    .reunion-fe-notes-table th:first-child {
        z-index: 3;
    }

    .reunion-fe-notes-table.table-striped tr:nth-child(odd) td:first-child {
        background: #f9f9f9;
    }

    .reunion-fe-student-row.is-collapsed td:not(:first-child) {
        display: none;
    }

    .reunion-fe-row-toggle {
        margin-bottom: 6px;
        padding: 1px 6px;
    }

    .reunion-fe-note-observations {
        margin-top: 6px;
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
