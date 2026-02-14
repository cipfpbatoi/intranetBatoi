@extends('layouts.email')
@section('body')
    <div style="width: 800px; text-align: justify; font-size: larger">
        <p><strong>De {{authUser()->fullName}} del {{config('contacto.nombre')}} </strong></p>
        <p>Adjunte documents Fct de <strong>{{$elemento->Alumno->fullName}}</strong> per a la seua Signatura. </p>
        <p>L'A1 i el A2 cal que ho signe el gerent o representant de l'empresa</p>
        <p>El A3 cal que ho signe l'instructor {{$elemento->Fct->Instructor->nombre}}</p>
        <p>Gràcies</p>
    </div>
@endsection
