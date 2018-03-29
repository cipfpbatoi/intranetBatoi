@extends('layouts.email')
@section('body')
<table style='text-align: center'>
    <tr>
        <th>Fi de les pràctiques de FCT</th>
    </tr>
</table>
<div>
    <table style=" border:#000 solid 1;">
        <tr><td><strong>De: </strong>{!! $remitente['nombre']  !!}  </td></tr>
        <tr><td>{{ config('constants.contacto.nombre')}}</td></tr>
    </table>
</div>
<div class="container" >
    <p>Senyor {{$elemento->instructor}}. Les pràctiques que venia efectuant {{$elemento->Alumno->FullName}} a la seua empressa han arribat a la seua fi. Per tal d'assolir una millora continua ens és de molt utilitat la seua opinió .</p>
    <p>Hem preparat aquest <a href="https://docs.google.com/forms/d/1iBxbyIC-tqdv_f-O9EfVLwv39Um_zQuaNGBZaCLNPVY/viewform?edit_requested=true">formulari</a> per recollir-la.</p>
    <p>Gràcies per la seua col.laboració</p>
</div>
@endsection