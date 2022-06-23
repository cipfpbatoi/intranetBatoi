@extends('layouts.email')
@section('body')
    <table style='text-align: center'>
        <tr>
            <th>Matricula curs 2022/23</th>
        </tr>
    </table>
    <div>
        <table style="color:#000; border-width:1px; border-style:solid ;">
            <tr >
                <td><strong>De: </strong>{!! $aR->Reunion->Responsable->fullName !!} </td>
                </tr>
        </table>
    </div>
    <div class="container" style="font-size: 1.3em; line-height: 1.3em">
        <h2>Darrera oportunitat</h2>
        <p>Hola, des del CIPFP Batoi, volem informar-te que el procediment per a realitzar la matrícula per al <strong>curs 2021-22</strong>
            es farà de la següent forma:</p>
        <ul>
            <li style=" margin-top: 1.3em">La data de matrícula serà del <strong>3 al 5 de setembre</strong> (ambdós inclosos, des de les 9 del matí).</li>
            <li style=" margin-top: 1.3em">La matrícula es farà de forma telemàtica mitjançant un assistent al qual entraràs polsant sobre el
                següent enllaç:<br/>
                <a href="http://ext.matricula.cipfpbatoi.es/{{$convocatoria}}/{{$aR->token}}" >http://ext.matricula.cipfpbatoi.es/{{$convocatoria}}/{{$aR->token}}</a>
            <li style=" margin-top: 1.3em; list-style-type: none"><strong style="color:seagreen;font-size: 1.3em">"Aquest enllaç és únic i vàlid només per a tú en el període específicat."</strong></li>
            <li style=" margin-top: 1.3em">Hi ha un manual del procediment que pots consultar en el següent enllaç <a href="https://bit.ly/2SGWC2o">https://bit.ly/2SGWC2o</a></li>
            <li style=" margin-top: 1.3em">Per a qualsevol dubte, pots cridar al centre (966527660) o enviar un correu a info@cipfpbatoi.es.</li>
            <li style=" margin-top: 1.3em">Si no formalices la matrícula en el termini indicat, pedràs el dret a la plaça que el correspon al CIPFP Batoi i s'enten que no vols continuar amb els estudis que estaves cursant. </li>
        </ul>

        <p>Salutacions cordials.</p>
    </div>
@endsection