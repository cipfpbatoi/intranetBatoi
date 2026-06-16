@extends('layouts.email')

@section('body')
    <table style="text-align: center">
        <tr>
            <th>Certificat de mòdul optatiu</th>
        </tr>
    </table>
    <div>
        <table style="border:#000 solid 1;">
            <tr>
                <td><strong>De: </strong>{!! $remitente['nombre'] !!}</td>
            </tr>
        </table>
    </div>
    <div class="container">
        Estimat/da {{ $elemento->Alumno->fullName }}:<br/>
        Adjunt et remetem el certificat del mòdul optatiu {{ $elemento->Certificat->denominacio }}.<br/>
        Salutacions cordials.
    </div>
@endsection
