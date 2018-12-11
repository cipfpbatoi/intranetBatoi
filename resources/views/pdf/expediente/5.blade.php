@extends('layouts.pdf')
@section('content')
@php $elemento = $todos; $profesores = Intranet\Entities\Profesor::orderBy('apellido1', 'asc')->orderBy('apellido2', 'asc')
                ->Grupo($elemento->Alumno->Grupo->first()->codigo)->get(); @endphp
<div class="page">
    @include('pdf.partials.cabecera')
    <br/><br/><br/>
    <div class="container" style="width:95%;clear:right;margin-bottom: 30px">
        <h2 style="text-align: center">INFORME D'EXEMPCIÓ DE F.C.T.</h2>
    </div>
    
    <div class="container" style="width:95%;clear:right;text-align: justify">
        <p style="text-indent: 30px; font-size: 20px; line-height: 1.8em;margin-bottom: 3em">
            {{$elemento->Profesor->FullName}}, 
            @if ($elemento->Profesor->sexo == 'H') tutor @else tutora @endif 
            del mòdul de Formació en Centres de Treball del grup de 2<sup>on</sup> del {{$elemento->Alumno->Grupo->first()->Ciclo->Xtipo}} 
            de <strong>{{$elemento->Alumno->Grupo->first()->Ciclo->literal}}</strong> informa que:
        </p>
        <p style="text-indent: 60px; font-size: 20px; line-height: 1.8em">
            Una vegada revisada, per part de l'equip docent del grup, la documentació presentada per
            @if ($elemento->Alumno->sexo == 'H') l'alumne @else l'alumna @endif {{ $elemento->Alumno->FullName}} 
            amb NIF {{$elemento->Alumno->dni}}, 
            es consideren superats els objectius expressats en termes de resultats d'aprenentatge del mòdul professional de Formació en Centres de Treball 
            del {{$elemento->Alumno->Grupo->first()->Ciclo->Xtipo}} {{$elemento->Alumno->Grupo->first()->Ciclo->literal}}, per la qual cosa s'emet informe 
            favorable perquè li siga concedida l'exempció <strong>{{$elemento->explicacion}}</strong> d'aquest mòdul.
        </p>
    </div>
    <div class="container" style="width:50%;font-size: 20px; float:right;">
        <br/><br/>
        <p>{{config('contacto.poblacion')}}, a {{$datosInforme}} </p>
        <br/><br/><br/>
    </div>
    <div class="container" style="width:90%;font-size: 20px;">    
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        @foreach ($profesores as $profesor)
        <div style="width:15%; float:left;font-size: 12px; ">
            <p><strong>{{$profesor->shortName}}</strong></p>
            <br/><br/><br/>
        </div>
        @endforeach
    </div>
</div>

@endsection
