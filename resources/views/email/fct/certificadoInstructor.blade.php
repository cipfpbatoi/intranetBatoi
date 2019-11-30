@extends('layouts.email')
@section('body')
<table style='text-align: center'>
    <tr>
        <th>Certificat FCT</th>
    </tr>
</table>
<div>
    <table style=" border:#000 solid 1;">
        <tr >
            <td><strong>De: </strong>Secretaria del CIPFP BATOI </td>
        </tr>
    </table>
</div>
<div class="container" >
    Estimat {{$fct->Instructor->nombre}}:<br/>
    Adjunt et remet certificat de les pràctiques de FCT realitzatdes.<br/>
    Salutacions cordials.
</div>
@endsection