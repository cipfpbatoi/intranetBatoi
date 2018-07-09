@extends('layouts.pdf')
@section('content')
@include('pdf.partials.cabecera')
<div class="container col-lg-12" >
    <table class="table table-bordered">
        <tr>
            <th>Resultats Avaluacio Grup {{$datosInforme}}</th>
        </tr>
    </table>
</div>
<div class="container col-lg-12" >
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Modul</th><th>Aval.</th><th>Matr.</th><th>Aval.</th><th>Aprovats</th><th>Suspesos</th><th>Observacions</th>
            </tr>
        </thead>
        <tbody>
            @php
            $agrupados = $todos->groupBy('evaluacion')
            @endphp
            @foreach ($agrupados as $grupo)
            @foreach ($grupo as $elemento)
            <tr>
                <td>{!! $elemento->Modulo !!}</td>
                <td>{!! config('auxiliares.nombreEval')[$elemento->evaluacion] !!}</td>
                <td>{{$elemento->matriculados}}</td>
                <td>{{$elemento->evaluados }}</td>
                @if ($elemento->evaluados)
                    <td> {{$elemento->aprobados }} ({{round(100 * ($elemento->aprobados / $elemento->evaluados),2)}} %) </td>
                    <td>{{$elemento->evaluados - $elemento->aprobados }} ({{round(100 * (($elemento->evaluados - $elemento->aprobados)/ $elemento->evaluados),2)}} %)</td>
                @else 
                    <td>0</td><td>0</td>
                @endif
                <td>{{$elemento->observaciones }}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="3"><strong>Totals avaluació {!! config('auxiliares.nombreEval')[$elemento->evaluacion] !!}</strong></td>
                <td><strong>{{$grupo->sum('evaluados') }}</strong></td>
                @if ($grupo->sum('evaluados'))
                    <td><strong>{{$grupo->sum('aprobados') }} ({{round(100 * ($grupo->sum('aprobados') / $grupo->sum('evaluados')),2)}} %)</strong></td>
                    <td><strong>{{$grupo->sum('evaluados') - $grupo->sum('aprobados') }} ({{round(100 * (($grupo->sum('evaluados') - $grupo->sum('aprobados'))/ $grupo->sum('evaluados')),2)}} %)</td>
                @else
                    <td>0</td><td>0</td>
                @endif
                <td></td>
            </tr>

            @endforeach
        </tbody>
    </table>
</div>
@endsection




