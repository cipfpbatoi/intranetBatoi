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
    Estimat {{$elemento->Alumno->fullName}}:<br/>
    Adjunt et remet certificat del curs {{$elemento->Curso->titulo}} impartit per {{$elemento->Curso->profesorado}} entre
    {{$elemento->Curso->fecha_inicio}} i {{$elemento->Curso->fecha_fin}}.<br/>
    Salutacions cordials.
</div>
@endsection