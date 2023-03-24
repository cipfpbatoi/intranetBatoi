@extends('layouts.email')
@section('body')
<table style='text-align: center'>
    <tr>
        <th>Fi de les pràctiques de FCT</th>
    </tr>
</table>
<div>
    <table style=" border:#000 solid 1;">

        <tr><td><strong>Del departament de </strong>Qualitat</td></tr>
        <tr><td>{{ config('contacto.nombre')}}</td></tr>

    </table>
</div>
<div class="container" >
    <p>
        {{$fct->Alumno->FullName}}.
        Les pràctiques de FCT han arribat a la seua fi i per tal de millorar-les
        ens és de molt utilitat la teua opinió.
    </p>
    <p>Hem preparat aquest
        <a href="https://docs.google.com/forms/d/e/1FAIpQLSeZt7OW7eGvcH4jF1BZPGvud5gMUe8zMUgyYJ0U118mhT8mqg/viewform?vc=0&c=0&w=1">
            formulari
        </a> per recollir-la.</p>
    <p>Gràcies per la teua col.laboració</p>
</div>
@endsection