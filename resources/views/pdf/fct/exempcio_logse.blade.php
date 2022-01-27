@extends('layouts.pdf')
@section('content')
    @include('pdf.partials.cabecera')
    <div class="page" style="font-size:large;line-height: 2em;text-align: justify;margin-top:1cm">
        <h2 style="text-align: center;margin: 2cm">INFORME D'EXEMPCIÓ F.C.T.</h2>
        <p style="margin: 1cm;">{{$datosInforme['tutor']->fullName}}, coordinador{{genre($datosInforme['tutor'])}} del Mòdul de Formació en Centres de Treball del {{$datosInforme['cicle']->vliteral}} informa que:</p>
        <p style="margin-top: 1cm;">
            Una vegada revisada la documentació presentada per l'alumn{{genre($todos->Alumno,'e')}} <strong> {{$todos->Alumno->fullName}}</strong>  amb NIF </strong>{{$todos->Alumno->dni}}</strong> es consideren superats @if  ($todos->horas < 380) una part @endif
            dels objectius expressats en termes de capacitats terminals del mòdul professional Formació en Centres de Treball del {{$datosInforme['cicle']->vliteral}},
            per la qual cosa s'emet informe favorable perquè li siga concedida l'exempció <strong> @if  ($todos->horas < 380) PARCIAL @else TOTAL @endif</strong> d'aquest mòdul.
        </p>
        @if  ($todos->horas < 380)
            <p style="margin-top: 1cm;">S'aconsella que realitze un total de {{$datosInforme['cicle']->horasFct - $todos->horas }}     hores per a completar la formació adequadament.</p>
        @endif
        <p style="margin-top: 1cm;">Contra aquesta resolució l'interessat pot presentar recurs d'alçada en el termini d'un mes des de la seua notificació davant la Direcció Territorial d'Educació d'Alacant.</p>
        <p style="text-align: right;margin: 1cm;">{{$datosInforme['poblacion']}}, {{$datosInforme['date']}} </p>
        <p style="margin: 3cm;">Signat: {{$datosInforme['tutor']->fullName}}</p>
        <p style="margin: 3cm;">Coordinador{{genre($datosInforme['tutor'])}} Mòdul FCT {{$datosInforme['cicle']->ciclo}}</p>
    </div>
    @include('pdf.fct.exempcio_comissio')
@endsection
