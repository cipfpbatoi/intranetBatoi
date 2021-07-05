@extends('layouts.email')
@section('body')
<table style='text-align: center'>
    <tr>
        <th>Matricula curs 2021/22</th>
    </tr>
</table>
<div>
    <table style="color:#000; border-width:1px; border-style:solid ;">
        <tr>
            <td><strong>De: </strong>{!! $aR->Reunion->Responsable->fullName !!} </td>
        </tr>
    </table>
</div>
<div class="container" >
    <p>Hola, des del CIPFP Batoi, volem informar-te que el procediment per a realitzar la matrícula per al curs 2021-22
        es farà de la següent forma:</p>
    <ul>
        <li>La data de matrícula serà del 8 al 18 de juliol (ambdós inclosos).</li>
        <li>La matrícula es farà de forma telemàtica mitjançant un assistent al qual entraràs polsant sobre el
            següent enllaç: <a href="http://ext.matricula.cipfpbatoi.es/{{config('curso.convocatoria')}}/{{$aR->token}}" >http://ext.matricula.cipfpbatoi.es/{{config('curso.convocatoria')}}/{{$aR->token}}</a>.
            Aquest enllaç és únic i vàlid només per a tú en el període específicat.</li>
        <li>Hem fet un manual del procediment que pots consultar en el següent enllaç <a href="https://bit.ly/2SGWC2o">https://bit.ly/2SGWC2o</a></li>
        <li>Per a qualsevol dubte, pots cridar al centre (966527660) o enviar un correu a info@cipfpbatoi.es.</li>
    </ul>

    <p>Salutacions cordials.</p>
</div>
@endsection