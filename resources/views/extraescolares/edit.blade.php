@extends('layouts.intranet')
@section('content')
<div class="gruposContainer col-lg-8 col-md-6 col-sm-10 col-xs-10 col-lg-offset-2 col-md-offset-2 col-sm-offset-1">
    <div class="x_panel" style="margin-bottom: 15px;">
        <div class="x_title">
            <h2>{{ trans("models.Actividad.titulo", ['actividad' => $Actividad->name]) }}</h2>
            <a href="{{ route('actividad.edit', ['actividad' => $Actividad->id]) }}" class="btn btn-primary btn-sm pull-right">
                Editar
            </a>
            <div class="clearfix"></div>
        </div>
        <div class="x_content">
            <p class="text-muted" style="margin-bottom: 6px;">
                <strong>{{ trans('validation.attributes.desde') }}:</strong> {{ $desdeVal }}
            </p>
            <p class="text-muted" style="margin-bottom: 6px;">
                <strong>{{ trans('validation.attributes.hasta') }}:</strong> {{ $hastaVal }}
            </p>
            <p class="text-muted" style="margin-bottom: 6px;">
                <strong>Coordinador:</strong> {{ $coordinadorNom }}
            </p>
            <p class="text-muted" style="margin-bottom: 0;">
                <strong>Tipus d'activitat:</strong> {{ $tipoActividad }}
            </p>
        </div>
    </div>
    <p>
        <span class="label label-primary">Professors: {{ $sProfesores->count() }}</span>
        <span class="label label-info" style="margin-left: 8px;">Grups: {{ $sGrupos->count() }}</span>
    </p>
    <div class="clearfix"></div>
</div>

@include('extraescolares.partials.profesoresTabla')
@include('extraescolares.partials.gruposTabla')
@endsection
@section('scripts')
@endsection
