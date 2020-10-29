@extends('layouts.pdf')
@section('css')
    {{ Html::style('/css/dual.css') }}
@endsection
@section('content')
    @include('pdf.partials.cabecera')
    <br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
    <p style="font-size: 20px;text-align: justify;line-height: 1.5em">L'empresa <strong>{{$todos->fct->colaboracion->centro->Empresa->nombre }}</strong> amb cif <strong>{{$todos->fct->colaboracion->centro->empresa->cif}}</strong>
        i domicili en {{$todos->fct->colaboracion->centro->empresa->direccion  }} de {{$todos->Fct->Colaboracion->Centro->Empresa->localidad}}, que participa en el projecte de FP Dual del cicle <strong>{{ $todos->fct->Colaboracion->Ciclo->vliteral }}</strong> de grau  <strong>{{ $todos->fct->Colaboracion->Ciclo->tipo == 1?'mitja':'superior' }}</strong>
    de la familia profesional <strong>{{ ucfirst(mb_strtolower(substr($todos->fct->Colaboracion->Ciclo->Departament->vliteral,12))) }}</strong>
    amb el <strong>{{ config('contacto.nombre') }}</strong> de <strong>{{ config('contacto.poblacion') }}</strong> de titularitat pública amb codi <strong>{{ config('contacto.codi') }}.</strong></p>
    <p style="font-size: 20px;text-align: justify;margin-top: 75px;line-height: 1.5em"><strong>Fa la següent Declaració responsable</strong></p>
    <p style="font-size: 20px;text-align: justify;margin-top: 75px;line-height: 1.5em">
        Declare en virtut del present document, d’acord amb el que estableix l’article 5, del Reial
        Decret  Llei 31/2020, de 29 de setembre, pel que s’adopten messures urgents en l’àmbit de l’educació
        no universitària, i amb l’objectiu de garantir el retorn a la modalitat dual, que l’empresa firmant
        que participa en el projecte de Formació Professional Dual amb el centre educatiu anteriorment referenciat,
        d’acord amb la normativa vigent ha elaborat i aplica el corresponent protocol de contingència sobre messures COVID,
        i no està inmersa en cap tipus d’expedient de regulació d’ocupació, ERO, ERTO, etc. que afecte directament
        al departament en el qual estarà l’alumne de pràctiques en FP Dual.
    </p>
    <p style="font-size: 20px;text-align: right;margin-top: 75px">
        {{$todos->Fct->Colaboracion->Centro->Empresa->localidad}}, a {{ FechaString($datosInforme['date']) }}
    </p>
    <p style="font-size: 20px;text-align: right;margin-top: 150px;">
        Signatura del representant: {{ $todos->fct->Colaboracion->Centro->Empresa->gerente }}
    </p>
@endsection