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
    <p>Les pràctiques de FCT a les següents empreses han arribat a la seua fi i per tal de millorar-les ens és de molt utilitat la teua opinió.</p>
    <p><ul>
        @foreach ($fct as $empresa => $alumno)
            <li><a href="https://docs.google.com/forms/d/e/1FAIpQLScevNvZLdQvDaqKj3kvOs2gy_6n2wTTUvckHT70rgC7Juxuqg/viewform">{{$empresa}} ({{$alumno}})</a></p></li>
        @endforeach
    </ul></p>
    <p>Gràcies per la teua col.laboració</p>
</div>
@endsection