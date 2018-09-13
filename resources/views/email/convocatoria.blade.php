@extends('layouts.email')
@section('body')
<table style='text-align: center'>
    <tr>
        <th>Convocatoria Reunión</th>
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
    <ul>
        <li>{{$elemento->Tipos()['vliteral']}} {{$elemento->descripcion}}</li>
        <li>Numeració: {{$elemento->curso}} / {{$elemento->numero}} </li>
        <li>Lloc: {{$elemento->Espacio->descripcion}}</li>
        <li>Data: {{$elemento->fecha}}</li>
    </ul>
</div>
@endsection