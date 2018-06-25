@extends('layouts.pdf')
@section('content')
@include('pdf.partials.cabecera')
<div class="container col-lg-12" >
    <table class="table table-bordered">
        <tr>
            <th>Informe Trimestral ({{$datosInforme['trimestre']}}) {{$datosInforme['resultados']->first()->Profesor->Departamento->literal}}</th>
        </tr>
    </table>
</div>
<div class="container col-lg-12" >
    <table class="table table-bordered">
        <tr>
            <th>Resultats de l'avaluació</th>
        </tr>
    </table>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Modul</th><th>Avaluació</th><th>Avaluats</th><th>Aprovats</th><th>%</th>
            </tr>
        </thead>
        <tbody>
            @php
            $agrupados = $datosInforme['resultados']->groupBy('evaluacion')
            @endphp
            @foreach ($agrupados as $grupo)
            @foreach ($grupo as $elemento)
            <tr>
                <td>{!! $elemento->Modulo !!} -  {{$elemento->Profesor->ShortName}} </td>
                <td>{!! config('constants.nombreEval')[$elemento->evaluacion] !!}</td>
                <td>{{$elemento->evaluados}}</td>
                <td>{{$elemento->aprobados }}</td>
                @if ($elemento->evaluados)
                    <td>{{round(100 * ($elemento->aprobados / $elemento->evaluados),1)}}%</td>
                @else
                    <td>0</td>
                @endif    
            </tr>
            @endforeach
            <tr>
                <td colspan="2"><strong>Totals avaluació {!! config('constants.nombreEval')[$elemento->evaluacion] !!}</strong></td>
                <td><strong>{{$grupo->sum('evaluados') }}</strong></td>
                <td><strong>{{$grupo->sum('aprobados') }}</strong></td>
                <td><strong>{{round(100 * ($grupo->sum('aprobados')/ $grupo->sum('evaluados')),1)}}%</td>
            </tr>

            @endforeach
        </tbody>
    </table>
</div>
<div class="container col-lg-12" >
    <table class="table table-bordered">
        <tr>
            <th>Seguiments de la programació</th>
        </tr>
    </table>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Modul</th><th>Avaluació</th><th>Professor</th><th>Programades</th><th>Impartides</th><th>%</th>
            </tr>
        </thead>
        <tbody>
            @php
            $agrupados = $datosInforme['resultados']->groupBy('evaluacion')
            @endphp
            @foreach ($agrupados as $grupo)
            @foreach ($grupo as $elemento)
            <tr>
                <td>{!! $elemento->Modulo !!}</td>
                <td>{!! config('constants.nombreEval')[$elemento->evaluacion] !!}</td>
                <td>{{$elemento->Profesor->ShortName}}</td>
                <td>{{$elemento->udProg}}</td>
                <td>{{$elemento->udImp }}</td>
                <td>{{round(100 * ($elemento->udImp / $elemento->udProg),2)}} %</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="3"><strong>Totals avaluació {!! config('constants.nombreEval')[$elemento->evaluacion] !!}</strong></td>
                <td><strong>{{$grupo->sum('udProg') }}</strong></td>
                <td><strong>{{$grupo->sum('udImp') }}</strong></td>
                <td><strong>{{round(100 * ($grupo->sum('udImp')/ $grupo->sum('udProg')),1)}} %</td>
            </tr>

            @endforeach
        </tbody>
    </table>
</div>
<div class="container col-lg-12" >
    <table class="table table-bordered">
        <tr>
            <th>Activitats extraescolars</th>
        </tr>
    </table>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Activitat</th><th>Grups</th><th>Professors</th><th>Data</th><th>Comentaris</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($todos as $elemento)
            <tr>
                <td>{!! $elemento->name !!}</td>
                <td>@foreach ($elemento->grupos as $grupo)
                    @if ($grupo->Tutor->departamento == $datosInforme['resultados']->first()->Profesor->departamento)
                        - {{$grupo->nombre}} -
                    @endif
                    @endforeach
                </td>
                <td>@foreach ($elemento->profesores as $profe)
                        @if ($profe->departamento == $datosInforme['resultados']->first()->Profesor->departamento)
                        - {{$profe->FullName}} -
                        @endif
                    @endforeach
                </td>
                <td>{{$elemento->desde}}</td>
                <td>{{$elemento->comentaris }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@if (isset($datosInforme['proyectos']))
    <div class="container col-lg-12" >
        <table class="table table-bordered">
            <tr>
                <th>@lang("validation.attributes.proyectos")</th>
            </tr>
        </table>
        <table class="table table-bordered">
            <tr><td style="text-align: justify">{!!$datosInforme['proyectos']!!}</td></tr>
        </table>    
    </div>
@endif
@if (isset($datosInforme['programaciones']))
    <div class="container col-lg-12" >
        <table class="table table-bordered">
            <tr>
                <th>@lang("validation.attributes.propuestas")</th>
            </tr>
        </table>
        <table class="table table-bordered">
            <tr><th>Mòdul</th><th>Proposta</th></tr>
            @foreach ($datosInforme['programaciones'] as $programacion)
                <tr><td>{{$programacion->XModulo}}</td><td style="text-align: justify">{!!$programacion->propuestas!!}</td></tr>
            @endforeach
        </table>    
    </div>
@endif
<div class="container col-lg-12" >
    <table class="table table-bordered">
        <tr>
            <th>@lang("validation.attributes.observaciones")</th>
        </tr>
    </table>
    <table class="table table-bordered">
        <tr><td style="text-align: justify">{!!$datosInforme['observaciones']!!}</td></tr>
    </table>    
</div>
@endsection




