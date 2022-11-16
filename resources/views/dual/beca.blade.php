@extends('layouts.pdf')
@section('css')
    {{ Html::style('/css/dual.css') }}
@endsection
@section('content')
    @include('pdf.partials.cabecera')
    <br/><br/><br/><br/><br/><br/><br/><br/><br/>
    <p style="font-size: 20px;text-align: center;line-height: 1.5em;">
        <strong>Conveni de col·laboració entre la Conselleria d'Educació, Cultura i Esport i l'empresa, per a la
            impartició de la FP Dual.</strong>
    </p>
    <p style="font-size: 20px;">
        Annex: Ajuda a l'estudi
    </p>
    <p style="font-size: 20px;text-align: justify;margin-top: 75px;line-height: 1.5em">
        S'exposa que {{ $todos->fct->Colaboracion->centro->empresa->gerente??'' }}, en qualitat de gerent/representant
        legal de l'empresa {{ $todos->fct->Colaboracion->centro->empresa->nombre }}
        amb CIF {{ $todos->fct->Colaboracion->centro->empresa->cif }}
    </p>
    <p style="font-size: 20px;">
        Notifique que:
    </p>
    <p style="font-size: 20px;text-align: justify;margin-top: 75px;line-height: 1.5em">
        {{ $todos->Alumno->sexo=='H'?"L’alumne":"L'alumna" }} amb DNI
        <strong>{{ $todos->Alumno->dni }}</strong>, {{ $todos->Alumno->sexo=='H'?"el":"la" }} qual realitza a la nostra
        entitat la FP Dual, percebrà una ajuda a l'estudi consistent en
        {{ $todos->beca }} € per hora emprada durant el peíode del projecte de formació especificat en el calendari de
        pràctiques.
    </p>
    <p style="font-size: 20px;text-align: justify;margin-top: 75px;line-height: 1.5em">
        L'abonament d'aquesta ajuda es realitzarà mitjançant transferència bancària el número de compte aportat per
        l'alumne. De la mateixa manera ambdues parts signaran un document on es registrarà que l'alumne percep
        l'esmentada ajuda.
    </p>
    <p style="font-size: 20px;margin-top: 75px">
        Als efectes oportuns s'expedix el present document
        en {{ $todos->fct->Colaboracion->centro->empresa->localidad }}
        a {{ fechaString($datosInforme['date']) }}
    </p>
    <br/><br/><br/><br/>
    <p style="font-size: 20px;text-align: right;margin-top: 150px;">
        Signatura : {{  $todos->fct->Colaboracion->centro->empresa->gerente??'' }}
    </p>
@endsection