@extends('layouts.email')
@section('body')
    <div>
        <table style='text-align: center' style=" border:#000 solid 1;">
            <tr>
                <th>{{$remitente['tutor']}}</th>
            </tr>
            <tr>
                <td><strong>De {{$remitente['nombre']}} </strong></td>
            </tr>
        </table>
    </div>
    <div>
            <p>Hola, {{$remitente['tutor']}}</p>
            <p>Ha fallat la pujada dels arxius A5 i A6 a l'expedient de l'alumne perquè no hi ha 2 fitxers: {{ $elemento->Alumno->fullName }} del la fct {{ $elemento->id }}</p>
            <p>@php(print_r($remitente['document']));</p>
            <p>Si us plau, contacteu amb l'administrador del sistema per solucionar el problema.</p>
    </div>
@endsection