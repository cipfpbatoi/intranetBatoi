@extends('layouts.pdf')
@section('content')
@include('pdf.partials.cabecera')
@php($totales = $datosInforme['totales'])
@php($agrupados = $datosInforme['resultados']->groupBy('evaluacion'))
<div class="container col-lg-12" >
    <table style="width: 95%" class="table">
        <tr >
            <th colspan="2">
                @if ($datosInforme['trimestre'] == 3) <h2>MEMORIA FINAL DEPARTAMENT @else MEMORIA TRIMESTRAL ({{$datosInforme['trimestre']}}) DEPARTAMENT @endif</h2> <br/>
            </th>
        </tr>
        <tr>
            <th style="text-align: left">  Departament:  {{$datosInforme['resultados']->first()->Profesor->Departamento->literal}} </th>
            <th style="text-align: right">  DATA: {{$datosInforme['fecha']}} </th>
        </tr>
    </table>
</div>
<br/>
<div class="container col-lg-12" >
    <table style="width: 95%" class="table">
        <tr >
            <th style="text-align: left"><h3>DESENVOLUPAMENT DEL PROCÉS D'ENSENYAMENT</h3></th>
        </tr>
    </table>
    <br/>
    <table style="width: 95%;" class="table table-bordered">
        <thead>
            <tr>
                <th style="width: 40% ;text-align: left; font-size: medium">MÓDUL</th><th style="width: 20% ;text-align: left; font-size: medium">DOCENT</th><th style="width: 15%">ALUMNAT AVALUAT</th><th style="width: 15%">ALUMNAT APROVAT</th><th style="width: 15%">% APROVATS</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($agrupados as $grupo)
                @foreach ($grupo as $elemento)
                <tr>
                    <td style="text-align: left; font-size: medium">{!! $elemento->Modulo !!}</td>
                    <td style="text-align: left; font-size: medium">{{$elemento->Profesor->ShortName}}</td>
                    <td style=" font-size: medium">{{$elemento->evaluados}}</td>
                    <td style=" font-size: medium">{{$elemento->aprobados }}</td>
                    @if ($elemento->evaluados)
                        <td style=" font-size: medium">{{round(100 * ($elemento->aprobados / $elemento->evaluados),1)}}%</td>
                    @else
                        <td style=" font-size: medium">0</td>
                    @endif
                </tr>
                @endforeach
            <tr>
                <td style=" font-size: medium"><strong>TOTAS AVALUACIÓ {!! config('auxiliares.nombreEval')[$elemento->evaluacion] !!}</strong></td>
                <td style=" font-size: medium"><strong>{{$grupo->sum('evaluados') }}</strong></td>
                <td style=" font-size: medium"><strong>{{$grupo->sum('aprobados') }}</strong></td>
                <td style=" font-size: medium"><strong>{{round(100 * ($grupo->sum('aprobados')/ $grupo->sum('evaluados')),1)}}%</strong></td>
            </tr>

            @endforeach
        </tbody>
    </table>
</div>
<br>
<div class="container col-lg-12" >
    <table class="table" style="width: 95%">
        <tr>
            <th style="text-align: left"><h3>DESENVOLUPAMENT DE LES PROGRAMACIONS DIDÀCTIQUES</h3></th>
        </tr>
    </table>
    <table class="table table-bordered">
        <thead>
            <tr style="width: 95%">
                <th style="width: 40%;text-align: left; font-size: medium">MÒDUL</th><th style="width: 20%;text-align: left; font-size: medium">DOCENT</th><th style="width: 10%">UNITATS PROGRAMADES</th><th style="width: 10%">UNITATS IMPARTIDES</th><th style="width: 10%">% COMPLIMENT</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($agrupados as $grupo)
                @foreach ($grupo as $elemento)
                <tr>
                    <td style="text-align: left; font-size: medium">{!! $elemento->Modulo !!}</td>
                    <td style="text-align: left; font-size: medium">{{$elemento->Profesor->ShortName}}</td>
                    <td style="font-size: medium">{{$elemento->udProg}}</td>
                    <td style="font-size: medium">{{$elemento->udImp }}</td>
                    @if ($elemento->udProg)
                        <td style="font-size: medium">{{round(100 * ($elemento->udImp / $elemento->udProg),2)}} %</td>
                    @else
                        <td style="font-size: medium">0 %</td>
                    @endif
                </tr>
                @endforeach
            <tr>
                <td colspan="2" style="font-size: medium"><strong>Totals avaluació {!! config('auxiliares.nombreEval')[$elemento->evaluacion] !!}</strong></td>
                <td><strong style="font-size: medium">{{$grupo->sum('udProg') }}</strong></td>
                <td><strong style="font-size: medium">{{$grupo->sum('udImp') }}</strong></td>
                <td><strong style="font-size: medium">{{round(100 * ($grupo->sum('udImp')/ $grupo->sum('udProg')),1)}} %</strong></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<br/>
<div class="container col-lg-12" >
    <table style="width: 95%" class="table">
        <tr >
            <th style="text-align: left"><h3>ACTIVITATS EXTRAESCOLARS</h3></th>
        </tr>
    </table>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th style="width:25%;text-align: left; font-size: medium">ACTIVITAT</th><th  style="width:15%;text-align: left; font-size: medium">GRUPS</th><th  style="width:15%;text-align: left; font-size: medium">DOCENTS</th><th  style="width:5%;text-align: left; font-size: medium">DATA</th><th  style="width:40%; font-size: medium">COMENTARIS</th>
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
                <td>{{substr($elemento->desde,0,5)}}</td>
                <td>{{$elemento->comentaris }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<br/>
@isset($totales)
    <div class="container col-lg-12" >
        <table style="width: 95%" class="table">
            <tr >
                <th style="width:25%;text-align: left; font-size: medium"><h3>RESULTATS OBTINGUTS</h3></th>
            </tr>
        </table>
    </div>
    <div class="container col-lg-12" >
        <table class="table">
            @foreach ($totales as $cicle => $totalXcicle)
                <tr><th colspan="3">{{$cicle}}</th></tr>
                @isset($totalXcicle['matriculas'])<tr><td style="width: 3%;text-align: right">o</td><td style="width: 40%;text-align: left;font-size: medium">Alumnat Matriculat</td><td style="width: 15%;text-align: left;font-size: medium "><strong>{{ $totalXcicle['matriculas']??'-' }}</strong></td></tr>@endisset
                @isset($totalXcicle['sumSatis'])<tr><td style="width: 3%;text-align: right">o</td><td style="width: 40%;text-align: left;font-size: medium">Grau de satisfacció amb el Professorat</td><td style="width: 15% ;text-align: left;font-size: medium"><strong>{{  $totalXcicle['sumSatis']?number_format($totalXcicle['votesSatis']/$totalXcicle['sumSatis'],1):'-'}}</strong></td></tr>@endisset
                @isset($totalXcicle['fct'])<tr><td style="width: 3%;text-align: right">o</td><td style="width: 40%;text-align: left;font-size: medium">Alumnat en Pràtiques FCT</td><td style="width: 15%;text-align: left;font-size: medium "><strong>{{ $totalXcicle['fct']??'-' }}</strong></td></tr>@endisset
                @isset($totalXcicle['insercio'])<tr><td style="width: 3%;text-align: right">o</td><td style="width: 40%;text-align: left;font-size: medium">Alumnat inserit laboralment</td><td style="width: 15%;text-align: left;font-size: medium "><strong>{{ $totalXcicle['insercio']??'-' }}</strong></td></tr>@endisset
                @isset($totalXcicle['sumAlFct'])<tr><td style="width: 3%;text-align: right">o</td><td style="width: 40%;text-align: left;font-size: medium">Grau de satisfacció de l'alumnat amb la empresa</td><td style="width: 15%;text-align: left;font-size: medium "><strong>{{ $totalXcicle['sumAlFct']?number_format($totalXcicle['votesAlFct']/$totalXcicle['sumAlFct'],1):'-'}}</strong></td></tr>@endisset
                @isset($totalXcicle['centres'])<tr><td style="width: 3%;text-align: right">o</td><td style="width: 40%;text-align: left;font-size: medium">Nombre d'empreses col·laboradores</td><td style="width: 15%;text-align: left;font-size: medium "><strong>{{ $totalXcicle['centres']??'-' }}</strong></td></tr>@endisset
                @isset($totalXcicle['sumTuFct'])<tr><td style="width: 3%;text-align: right">o</td><td style="width: 40%;text-align: left;font-size: medium">Grau de satisfacció del tutor amb la empresa</td><td style="width: 15%;text-align: left;font-size: medium "><strong>{{ $totalXcicle['sumTuFct']?number_format($totalXcicle['votesTuFct']/$totalXcicle['sumTuFct'],1):'-' }}</strong></td></tr>@endisset
            @endforeach
        </table>
    </div>
@endisset
@isset($datosInforme['programaciones'])
    <div class="container col-lg-12" >
        <table style="width: 95%" class="table">
            <tr>
                <th style="width:25%;text-align: left; font-size: medium"><h3>DESENVOLUPAMENT DE LA PRÀCTICA DOCENT</h3></th>
            </tr>
        </table>
    </div>
    <div class="container col-lg-12" >
        <table style="width: 95%" class="table">
            <tr>
                <th style="width:25%;text-align: left; font-size: medium"><h3>VALORACIÓ DELS CRITERIS DE LA METOLODOGIA UTILITZADA</h3></th>
            </tr>
        </table>
        <table class="table table-bordered">
            <tr><th style="width:30%;text-align: left; font-size: medium">MÒDUL</th><th style="width:3%;text-align: left;">CRITERIS I FERRAMENTS</th><th style="width:3%;text-align: left">METODOLOGIA</th><th style="width:60%;text-align: left; font-size: medium">PROPOSTES DE MILLORA</th></tr>
            @foreach ($datosInforme['programaciones'] as $programacion)
                <tr>
                    <th style="text-align: left;" >{{$programacion->XModulo}}</th>
                    <td>{!!$programacion->criterios!!}</td>
                    <td>{!!$programacion->metodologia!!}</td>
                    <td style="text-align: justify">{!!$programacion->propuestas!!}</td>
                </tr>
            @endforeach
        </table>
    </div>
@endisset
@isset($datosInforme['proyectos'])
    <div class="container col-lg-12" >
        <table style="width: 95%" class="table ">
            <tr >
                <th style="width:25%;text-align: left; font-size: medium"><h3>PROJECTES</h3></th>
            </tr>
        </table>
        <table style="width: 95%" class="table">
            <tr><td style="text-align: justify;font-size: medium">{!!$datosInforme['proyectos']!!}</td></tr>
        </table>
    </div>
@endisset
<div class="container col-lg-12" >
    <table style="width: 95%" class="table">
        <tr >
            <th style="width:25%;text-align: left; font-size: medium"><h3>OBSERVACIONS</h3></th>
        </tr>
    </table>
    <table style="width: 95%" class="table">
        <tr><td style="text-align: justify;font-size: medium">{!!$datosInforme['observaciones']!!}</td></tr>
    </table>
</div>
@endsection




