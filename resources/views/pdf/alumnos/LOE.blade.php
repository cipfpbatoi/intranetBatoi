@extends('layouts.pdf')
@section('content')
    @foreach ($todos as $alumno)
        <div class='page'>
            @include('pdf.alumnos.partials.titulo')
            <p style='font-size: 1.3em;line-height: 200%;text-align: justify'>
                Que @if ($alumno->sexo == 'H') l'alumne @else l'alumna @endif {{$alumno->fullName}} amb DNI núm {{$alumno->dni}} ha cursat amb aprofitament els continguts
                mínims que s'estableixen en el Reial Decret abans mencionat per al módul de Formació i Orientació Laboral contingut en l'esmentat
                títol amb una duració total de 30 hores, que el capacita per l'exercici de les FUNCIONS DE NIVELL BÀSIC EN PREVENCIÓ DE RISCOS LABORALS, d'acord amb
                el que estableix l'article 35 del Reial Decret 39/1997, de 17 gener, pel qual s'aprova el Reglament dels Servicis de prevenció.
                Així mateix @if ($alumno->sexo == 'H') l'alumne @else l'alumna @endif ha cursat, amb aprofitament, els mòduls específics de
                seguretat i higiene laboral o altres continguts relacionats amb la prevenció de riscos laborals, inclosos de mode transversal
                en la resta de mòduls professionals que componen cadascun dels cicles, complementant els continguts impartits en el mòdul professional de Formació i Orientació Laboral,
                a fi de satifer els programes de formació de l'annex IV del R.D.39/1997, de 17 de gener.
            </p>
            @include('pdf.partials.firmaDS')
        </div>
        <div class="page">
            @include('pdf.alumnos.partials.riesgosCurriculum') 
        </div>
    @endforeach    
@endsection
