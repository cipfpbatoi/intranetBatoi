@extends('layouts.pdf')
@section('css')
    {{ Html::style('/css/dual.css') }}
@endsection
@section('content')
    @include('pdf.partials.cabecera')
    <br/><br/><br/><br/><br/><br/><br/><br/><br/>
    <p style="font-size: 20px;text-align: justify;margin-top: 75px;line-height: 1.5em">
        {{ $todos->Alumno->sexo=='H'?"L’alumne":"L'alumna" }} <strong> {{ $todos->Alumno->FullName }}</strong>, amb DNI:
        <strong>{{ $todos->Alumno->dni }}</strong><br/><br/>
        Del cicle formatiu <strong>{{ $todos->fct->Colaboracion->Ciclo->vliteral }}</strong>
    </p>
    <p style="font-size: 20px;text-align: justify;margin-top: 75px;line-height: 1.5em;text-align: justify">
        Fa constar: <br/><br/>
        Que ha estat informat de l’horari d’FP Dual, a la data {{ fechaString($datosInforme['date']) }},
        i que li ha sigut lliurat i informat amb concreció dels dies i hores que ha d’anar al centre de treball i que
        són els dies i hores en que està cobert per l’Assegurança Escolar.
    </p>
    <p style="font-size: 20px;text-align: justify;margin-top: 75px;line-height: 1.5em;text-align: justify">
        En el cas de saltar-se l’esmentat calendari, {{ $todos->Alumno->sexo=='H'?"L’alumne":"L'alumna" }} assumeix la
        responsabiltat si succeís qualsevol incident ja que estaria fora de la cobertura de l'assegurança, eximint de
        tota responsabilitat al centre educatiu, en aquest cas al
        {{ config('contacto.nombre') }}.
    </p>
    <p style="font-size: 20px;text-align: right;margin-top: 75px">
        {{ $todos->Alumno->sexo=='H'?"L’alumne":"L'alumna" }} signa el present document expresant el seu acord amb el
        aquí exposat.
    </p>
    <p style="font-size: 20px;text-align: right;margin-top: 150px;">
        Signatura : {{ $todos->Alumno->FullName }}
    </p>
@endsection