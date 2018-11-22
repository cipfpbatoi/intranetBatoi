@extends('layouts.email')
@section('body')
<table style='text-align: center'>
    <tr>
        <th>Fi de les pràctiques de FCT</th>
    </tr>
</table>
<div>
    <table style=" border:#000 solid 1;">

        <tr><td>Del departament de <strong>Qualitat</strong>  </td></tr>
        <tr><td>{{ config('contacto.nombre')}}</td></tr>

    </table>
</div>
<div class="container" >
    <p>Les pràctiques de FCT a l'empresa {{$fct->Colaboracion->Centro->nombre}} han arribat a la seua fi i per tal de millorar-les ens és de molt utilitat la teua opinió.</p>
    <p>Hem preparat aquest <a href="https://docs.google.com/a/cipfpbatoi.es/forms/d/e/1FAIpQLSeFJrMqkstPBaP5Yo7ZPOwpNLSIhV0n7QGEob5M5VUr7-H5Gw/viewform?usp=send_form">formulari</a> per recollir-la.</p>
    <p>Gràcies per la teua col.laboració</p>
</div>
@endsection