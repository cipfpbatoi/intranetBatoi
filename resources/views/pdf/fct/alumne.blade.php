@extends('layouts.pdf')
@section('content')
    @foreach (\Intranet\Entities\AlumnoFct::MisFcts()->where('idFct',$todos->id)->get() as $alumnoFct)
        
        <div class="page" style="font-size:large;line-height: 2em">
            @include('pdf.partials.cabecera')
            <br/>
            <div class="container col-lg-12" style="width:90%;">
                <p style="text-indent: 50px">{{$datosInforme['consideracion']}}
                    <strong>{{$datosInforme['secretario']}}</strong> 
                    @if ($datosInforme['consideracion'] == 'En') secretari @else secretària @endif del 
                    {{$datosInforme['centro']}} d'{{$datosInforme['poblacion']}}, província d'{{$datosInforme['provincia']}}.</p>
            </div>
            <div class="container" >
                <br/>
                <strong>CERTIFICA:</strong>
                <br/><br />
            </div>
            <div class="container" style="width:95%">
                <p style="text-indent: 30px;text-align: justify">Que segons consta en el seu expedient, @if ($alumnoFct->Alumno->sexo === 'H') l'alumne @else l'alumna @endif <strong>{{$alumnoFct->Alumno->FullName}} </strong> 
                    amb DNI núm. {{$alumnoFct->Alumno->dni}}, ha realitzat la Formació en Centres de Treball (FCT) del <strong>{{$todos->Colaboracion->Ciclo->Xtipo}}
                        {{$todos->Colaboracion->Ciclo->vliteral}} </strong> en l'empresa {{$todos->Colaboracion->Centro->nombre}}, ubicada a {{$todos->Colaboracion->Centro->direccion}} de/d'
                    {{$todos->Colaboracion->Centro->localidad}}, amb una duració total de {{$alumnoFct->horas}} hores, fetes
                    en el curs lectiu {{Curso()}}  i ha obtingut una qualifació d'APTE.</p>
            </div>
            <hr/>
            <div class="container" style="width:95%">
                <p style="text-indent: 30px;text-align: justify">Que según consta en su expediente, @if ($alumnoFct->Alumno->sexo === 'H') el alumno @else la alumna @endif <strong>{{$alumnoFct->Alumno->FullName}} </strong> 
                    con DNI núm. {{$alumnoFct->Alumno->dni}}, ha realizado la Formación en Centros de Trabajo (FCT) del <strong>{{$todos->Colaboracion->Ciclo->Ctipo}}
                        {{$todos->Colaboracion->Ciclo->cliteral}} </strong> en la empresa {{$todos->Colaboracion->Centro->Empresa->nombre}}, ubicada en {{$todos->Colaboracion->Centro->direccion}} de
                    {{$todos->Colaboracion->Centro->localidad}}, con una duración total de {{$alumnoFct->horas}} horas, desempeñadas 
                    en el curso lectivo {{Curso()}}, obteniendo una calificación de APTO.</p>
            </div>
            <div class="container" style="width:90%;">
                <br/><br/>
                <p><strong>I per tal que així conste on convinga, signa el present escrit.</strong></p>

                <p>{{$datosInforme['poblacion']}},a {{$datosInforme['date']}} </p>
                <br/><br/><br/><br/>
                <div style="width:40%; float:left; ">
                    <p><strong>{{$datosInforme['secretario']}}</strong></p>
                    <p>@if ($datosInforme['consideracion'] == 'En') SECRETARI @else SECRETARIA @endif</p>
                </div>
                <div style="width:40%; float:right; ">
                    <p><strong>{{$datosInforme['director']}}</strong></p>
                    <p>Vist-i-plau DIRECTOR</p>
                </div>
            </div>
        </div>
    @endforeach
@endsection
