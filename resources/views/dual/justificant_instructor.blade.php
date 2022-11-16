@extends('layouts.pdf')
@section('css')
    {{ Html::style('/css/dual.css') }}
@endsection
@section('content')

    @include('pdf.partials.cabecera')
    <br/><br/><br/><br/><br/><br/><br/><br/><br/>
    <p style="font-size: 20px;text-align: justify;margin-top: 75px;line-height: 1.5em">
        L'instructor/a: {{$todos->Fct->Instructor->Nombre }} amb DNI: {{$todos->Fct->Instructor->dni   }}.<br/>
        De l'Empresa: {{$todos->Fct->Centro}} amb NIF: {{$todos->Fct->Colaboracion->Centro->Empresa->cif}}
        .<br/><br/><br/>
    </p>
    <p style="font-size: 20px;text-align: justify;margin-top: 75px;line-height: 1.5em;text-align: justify">
        Fa constar: <br/><br/>
        Que en data {{ fechaString($datosInforme['date']) }} li ha estat lliurat el document Annex V-b,
        acreditatiu de la seua participació en la FP Dual.
    </p>
    <p style="font-size: 20px;text-align: justify;margin-top: 75px">
        L’instructor signa el present document expresant el seu acord amb el aquí exposat.
    </p>
    <p style="font-size: 20px;text-align: right;margin-top: 150px;">
        Signatura i segell: {{$todos->Fct->Instructor->Nombre }}
    </p>

@endsection