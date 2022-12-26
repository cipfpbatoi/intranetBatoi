@extends('layouts.email')
@section('body')
<table style='text-align: center'>
    <caption></caption>
    <tr>
        <th>Certificat FCT</th>
    </tr>
</table>
<div>
    <table style="border:#000 solid 1px">
        <caption></caption>
        <tr>
            <th><strong>De: </strong>Secretaria del CIPFP BATOI </th>
        </tr>
    </table>
</div>
<div class="container" >
    Estimat {{$fct->Instructor->nombre}}:<br/>
    Adjunt et remet certificat de les pràctiques de FCT realitzatdes.<br/>
    @if (count($fct->Colaboradores))
        També s'adjunten els certificats dels col·laboradors, perquè li'ls faces arribar.<br/>
    @endif
    Salutacions cordials.
</div>
@endsection
