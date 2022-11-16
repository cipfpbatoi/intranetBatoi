@extends('layouts.pdf')
@section('content')
    @foreach ($todos as $alumnoFct)
        <div class="page" style="font-size:large;line-height: 2em">
            @include('pdf.partials.cabecera')
            <br/>
            <div class="container col-lg-12" style="width:90%;">
                <p style="text-indent: 50px">{{$datosInforme['consideracion']}}
                    <strong>{{$datosInforme['secretario']}}</strong>
                    @if ($datosInforme['consideracion'] == 'En')
                        secretari
                    @else
                        secretària
                    @endif del
                    {{$datosInforme['centro']}} d'{{$datosInforme['poblacion']}}, província
                    d'{{$datosInforme['provincia']}}.</p>
            </div>
            <div class="container">
                <strong>CERTIFICA:</strong>
                <br/>
            </div>
            <div class="container" style="width:95%">
                <p style="text-indent: 30px;text-align: justify">Que segons consta en el seu
                    expedient, @if ($alumnoFct->Alumno->sexo === 'H')
                        l'alumne
                    @else
                        l'alumna
                    @endif <strong>{{$alumnoFct->Alumno->FullName}} </strong>
                    amb DNI núm. {{$alumnoFct->Alumno->dni}}, ha realitzat la Formació en Centres de Treball (FCT) del
                    <strong>{{$alumnoFct->Fct->Colaboracion->Ciclo->Xtipo}}
                        {{$alumnoFct->Fct->Colaboracion->Ciclo->vliteral}} </strong> en
                    l'empresa {{$alumnoFct->Fct->Colaboracion->Centro->nombre}}, ubicada
                    a {{$alumnoFct->Fct->Colaboracion->Centro->direccion}} de/d'
                    {{$alumnoFct->Fct->Colaboracion->Centro->localidad}}, amb una duració total de {{$alumnoFct->horas}}
                    hores, fetes
                    en el curs lectiu {{curso()}} i ha obtingut una qualifació d'APTE.</p>
            </div>
            <hr/>
            <div class="container" style="width:95%">
                <p style="text-indent: 30px;text-align: justify">Que según consta en su
                    expediente, @if ($alumnoFct->Alumno->sexo === 'H')
                        el alumno
                    @else
                        la alumna
                    @endif <strong>{{$alumnoFct->Alumno->FullName}} </strong>
                    con DNI núm. {{$alumnoFct->Alumno->dni}}, ha realizado la Formación en Centros de Trabajo (FCT) del
                    <strong>{{$alumnoFct->Fct->Colaboracion->Ciclo->Ctipo}}
                        {{$alumnoFct->Fct->Colaboracion->Ciclo->cliteral}} </strong> en la
                    empresa {{$alumnoFct->Fct->Colaboracion->Centro->nombre}}, ubicada
                    en {{$alumnoFct->Fct->Colaboracion->Centro->direccion}} de
                    {{$alumnoFct->Fct->Colaboracion->Centro->localidad}}, con una duración total
                    de {{$alumnoFct->horas}} horas, desempeñadas
                    en el curso lectivo {{curso()}}, obteniendo una calificación de APTO.</p>
            </div>
            @include('pdf.partials.firmaSD')
        </div>
    @endforeach
@endsection