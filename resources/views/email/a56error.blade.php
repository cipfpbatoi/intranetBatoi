@extends('layouts.email')
@section('body')
    <div>
        <table style='text-align: center' style=" border:#000 solid 1;">
            <tr>
                <td><strong>De {{$remitente}} </strong></td>
            </tr>
        </table>
    </div>
    <div>
            <p>Ha fallat la pujada dels arxius A5 i A6 a l'expedient de l'alumne perquè hi ha un fitxer massa gran:  {{ $elemento['fct']->id }} - {{ $elemento['fct']->Alumno->shortName }} de {{ $elemento['fct']->Fct->Colaboracion->Ciclo->ciclo }}</p>
    </div>
@endsection