@extends('layouts.pdf')
@section('content')
@include('pdf.partials.cabecera')
<br/>
<table class="table table-bordered">
    <tr>
        <th>Acta <strong> {{$datosInforme->Tipos()->vliteral}}</strong> "{{$datosInforme->Xgrupo}}"</th>
    </tr>
</table>
<div class="container col-lg-12" >
    Acta número <strong>{{$datosInforme->numero}}</strong> curs <strong>{{$datosInforme->curso}}</strong> del dia 
   <strong>{{$datosInforme->dia}}</strong> a les <strong>{{$datosInforme->hora}}</strong>
</div>
@include('pdf.reunion.partials.punts')
@if ($datosInforme->avaluacioFinal && $datosInforme->normativa === 'LFP' && $datosInforme->grupoClase && (int) $datosInforme->grupoClase->curso === 2)
    @php
        $grupo = $datosInforme->grupoClase;
        $fcts = \Intranet\Entities\AlumnoFct::query()
            ->esAval()
            ->Grupo($grupo)
            ->get()
            ->keyBy('idAlumno');
    @endphp
    <div class="container">
        <br/>
        <strong>Qualificació FCT (LFP)</strong>
        <ul style='list-style:none'>
            @foreach ($grupo->Alumnos->sortBy('nameFull') as $alumno)
                @php $fct = $fcts->get($alumno->nia); @endphp
                <li><strong>{{ $alumno->nameFull }}</strong> - {{ $fct?->qualificacio ?? 'No Avaluat' }}</li>
            @endforeach
        </ul>
    </div>
    @php
        $renuncies = $fcts->where('calificacion', 3);
    @endphp
    @if ($renuncies->isNotEmpty())
        @php
            $modulos = \Intranet\Entities\Modulo_grupo::query()
                ->where('idGrupo', $grupo->codigo)
                ->get();
            $resultats = \Intranet\Entities\AlumnoResultado::query()
                ->whereIn('idAlumno', $renuncies->pluck('idAlumno'))
                ->whereIn('idModuloGrupo', $modulos->pluck('id'))
                ->get()
                ->groupBy('idAlumno');
        @endphp
        <div class="container">
            <br/>
            <strong>Alumnat amb renúncia: notes de mòduls</strong>
            <ul style='list-style:none'>
                @foreach ($renuncies as $fct)
                    @php
                        $alumno = $fct->Alumno;
                        $resultatsAlumne = $resultats->get($fct->idAlumno, collect())->keyBy('idModuloGrupo');
                    @endphp
                    <li>
                        <strong>{{ $alumno?->nameFull ?? $fct->Nombre }}</strong><br/>
                        @foreach ($modulos as $modulo)
                            @php $nota = (int) ($resultatsAlumne->get($modulo->id)?->nota ?? 0); @endphp
                            <strong>{{ $modulo->Xmodulo }}</strong>: {{ config('auxiliares.notas')[$nota] ?? $nota }}<br/>
                        @endforeach
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
@endif
{{--
@if ($datosInforme->informe)
    <div class="container">
        <br/>
        <strong>Continguts NO impartits per mòdul</strong>
        <ul style='list-style:none'>
            @foreach ($datosInforme->grupoClase->Modulos as $modulo)
                @foreach ($modulo->resultados->where('evaluacion',3) as $res)
                    <li><strong>{{$modulo->Xmodulo}}</strong>: {{$res->adquiridosNO}}</li>
                @endforeach
             @endforeach
        </ul>
    </div>
@endif
--}}
@if ($datosInforme->informe)
    <div class="container">
        <br/>
        <strong>Promoció de l'alumnat</strong>
        <ul style='list-style:none'>
            @foreach ($datosInforme->alumnos()->orderBy('apellido1')->orderBy('apellido2')->get() as $alumno)
                <strong><li>{{ $alumno->nameFull }} - @if ($alumno->pivot->capacitats == 3) NO @else SI @endif</strong> - {{config('auxiliares.promociona')[$alumno->pivot->capacitats]}}</li>
            @endforeach
        </ul>
    </div>
@endif
@include('pdf.reunion.partials.asistents')
@include('pdf.reunion.partials.signatura')
{{--
@if ($datosInforme->informe)
    <div class="container" style="page-break-before: always">
        <br/>
        <strong>Annex: Valoració tasca de l'alumnat</strong><br/>
        @foreach ($datosInforme->alumnos()->orderBy('apellido1')->orderBy('apellido2')->get() as $alumno)
            <p style="text-indent: 2em"><strong>{{$alumno->nameFull}}</strong>
            @foreach ($alumno->AlumnoResultado as $resultado)
                <strong>{{ $resultado->modulo }}</strong> - {{$resultado->valoracion}}.<br/>
                @if ($resultado->observaciones) - {{$resultado->observaciones}} <br/> @endif
            @endforeach
            </p>
        @endforeach
    </div>
@endif
--}}
@endsection
