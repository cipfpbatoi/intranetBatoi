@extends('layouts.pdf')
@section('css')
    {{ Html::style('/css/dual.css') }}
@endsection
@section('content')
    @include('pdf.partials.cabecera')
    <br/><br/><br/><br/><br/><br/><br/><br/><br/>
    <p style="font-size: 20px;text-align: center;line-height: 1.5em;">
        <strong>Conformitat de l’alumne per  a iniciar la formació en l’empresa corresponent al sistema de Formació Professional Dual en l’actual situació d’alerta sanitària generada pel COVID-19</strong>
    </p>
    <p style="font-size: 20px;text-align: justify;margin-top: 75px;line-height: 1.5em">
        L’alumne  <strong> {{ $todos->Alumno->FullName }}</strong>, amb NIF  <strong>{{ $todos->Alumno->dni }}</strong>, matriculat en el cicle formatiu <strong>{{ $todos->fct->Colaboracion->Ciclo->vliteral }}</strong>,
        de grau  <strong>{{ $todos->fct->Colaboracion->Ciclo->tipo == 1?'mitja':'superior' }}</strong>, de la família professional  <strong>{{ ucfirst(strtolower(substr($todos->fct->Colaboracion->Ciclo->Departament->vliteral,12))) }}</strong>, en Formació Professional Dual, en el centre educatiu
        <strong>{{ config('contacto.nombre') }}</strong>, amb codi de centre <strong>{{ config('contacto.codi') }}</strong>, durant el curs acadèmic
        <strong>{{ curso() }}</strong>, deixa constància de conéixer i acceptar els aspectes següents:
    </p>
    <ol style="font-size: 20px;text-align: justify;margin-top: 75px;line-height: 1.5em">
        <li>Que ens trobem en un moment de situació d’emergència sanitària degut al COVID-19.</li>
        <li>Que la situació d’emergència sanitària en que ens trobem precisa que tots hem complir amb les directrius indicades per les corresponents autoritats amb competència en la matèria.</li>
        <li>Que està dispossat a acceptar i complir rigurosament les instruccions del pla de contingència sobre messures COVID-19 de l’empresa a la que s’incorpora.</li>
    </ol>
    <p style="font-size: 20px;text-align: right;margin-top: 75px">
        {{ config('contacto.poblacion') }}, a {{ FechaString($datosInforme['date']) }}
    </p>
    <p style="font-size: 20px;text-align: right;margin-top: 150px;">
        Signatura : {{ $todos->Alumno->FullName }}
    </p>
@endsection