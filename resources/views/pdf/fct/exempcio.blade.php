@extends('layouts.pdf')
@section('content')
    <h3>INFORME D'EXEMPCIÓ F.C.T.</h3>
    <p>{{$datosInforme['tutor']->fullName}}, Coordinadora del Mòdul de Formació en Centres de Treball del {{$datosInforme['cicle']->ciclo}} informa que:</p>
    <p>
        Una vegada revisada la documentació presentada per l'alumna {{$todos->Alumno->fullName}}  amb NIF {{$todos->Alumno->dni}} es consideren superats @if  ($todos->horas < 380) una part @endif
        dels objectius expressats en termes de capacitats terminals del mòdul professional Formació en Centres de Treball del {{$datosInforme['cicle']->ciclo}},
        per la qual cosa s'emet informe favorable perquè li siga concedida l'exempció <strong> @if  ($todos->horas < 380) PARCIAL @else TOTAL @endif</strong> d'aquest mòdul.
    </p>
@endsection
