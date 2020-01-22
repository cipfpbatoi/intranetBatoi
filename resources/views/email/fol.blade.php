@extends('layouts.email')
@section('body')
<table style='text-align: center'>
    <tr>
        <th>Certificat Curs</th>
    </tr>
</table>
<div>
    <table style=" border:#000 solid 1;">
        <tr >
            <td><strong>De: </strong>{!! $remitente['nombre'] !!} </td>
        </tr>
    </table>
</div>
<div class="container" >
    Estimat {{$elemento->fullName}}:<br/>
    Adjunt et remet certificat de prevenció de riscos laborals que et correspon al haver aprovat el modul de FOL.<br/>
    Salutacions cordials.
</div>
@endsection