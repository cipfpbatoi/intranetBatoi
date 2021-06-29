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
    <p>Hola, primer que tot, esperem que estigues bé, tant tu com la teu família.
        T’enviem aquest correu per a informar-te del procés de matrícula per al curs 202122.</p>
    <p>La teua matrícula per al curs 2021-22 es farà de la següent forma:</p>
    <ul>
        <li>La data de matrícula serà del 8 al 18 de juliol (ambdós inclosos).</li>
        <li>La matrícula es farà de forma telemàtica mitjançant un assistent al qual entraràs polsant sobre el
            següent enllaç: <a href="http://ext.matricula.cipfpbatoi.es/{{config('curso.convocatoria')}}/{{$aR->token}}" >http://ext.matricula.cipfpbatoi.es/{{config('curso.convocatoria')}}/{{$aR->token}}</a>.
            Aquest enllaç és únic i vàlid només per a tú en el període específicat.</li>
        <li>Per a qualsevol dubte, pots cridar al centre (966527660) o enviar un correu a info@cipfpbatoi.es.</li>
    </ul>
    <p>Et recordem que pots veure les teues qualificacions en la web família (<a href="https://familia.edu.gva.es/">https://familia.edu.gva.es/</a>).
        Si tens qualsevol problema per a entrar, fica’t en contacte amb la secretaria del centre (<strong>secretaria@cipfpbatoi.es</strong> o <strong>03012165.secret@gva.es</strong>).</p>
    <p>Salutacions cordials.</p>
</div>
@endsection