@extends('layouts.email')
@section('body')
<table style='text-align: center'>
    <tr>
        <th>Enllaç programacion</th>
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
    Este és l'enllaç de la programació que has sol.licitat:
    <ul>
        <li>Mòdul: {{$elemento->Xmodulo}} - {{$elemento->Xciclo}}</li>
        <li>Curs: {{$elemento->curso}}</li>
        <li>Enllaç: <a href='{{$elemento->fichero}}'>{{$elemento->fichero}} </a></li>
    </ul>
</div>
@endsection