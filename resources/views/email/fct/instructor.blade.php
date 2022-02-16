@extends('layouts.email')
@section('body')
<table style='text-align: center'>
    <tr>
        <th>Fi de les pràctiques de FCT</th>
    </tr>
</table>
<div>
    <table style=" border:#000 solid 1;">
        <tr><td>Del departament de  <strong>Qualitat</strong>  </td></tr>
        <tr><td>{{ config('contacto.nombre')}}</td></tr>
    </table>
</div>
<div class="container" >
    <p>Estimat instructor {{$fct->Instructor->Nombre}}. Les pràctiques que venia efectuant l'alumnat del cicle de {{$fct->Colaboracion->Ciclo->literal}} a la seua empressa han arribat a la seua fi. Per tal d'assolir una millora continua ens és de molt utilitat la seua opinió .</p>
    <p>Hem preparat aquest
        <a href="{{config('variables.enquestaInstructor')}}">formulari</a> per recollir-la.</p>
    <p>Gràcies per la seua col.laboració</p>
</div>
@endsection