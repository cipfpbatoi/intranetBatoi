@extends('layouts.pdf')
@section('content')
    @foreach ($todos as $alumno)
        <div class='page'>
            @include('pdf.alumnos.partials.titulo')
            <p style='font-size: 1.3em;line-height: 200%;text-align: justify'>
                Que @if ($alumno->sexo == 'H') l'alumne @else l'alumna @endif {{$alumno->fullName}} amb DNI núm {{$alumno->dni}} ha cursat amb aprofitament els continguts
                mínims que s'estableixen en el Reial Decret abans mencionat per al módul de Formació i Orientació Laboral contingut en l'esmentat
                títol amb una duració total de 30 hores.
            </p>
            @include('pdf.partials.firmaDS')
        </div>
        <div class="page">
            @include('pdf.alumnos.partials.riesgosCurriculum') 
        </div>
    @endforeach    
@endsection
