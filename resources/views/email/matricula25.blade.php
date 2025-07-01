@extends('layouts.email')
@section('body')
    <table style='text-align: center'>
        <tr>
            <th>Matricula curs 2025/26</th>
        </tr>
    </table>
    <div>
        <table style="color:#000; border-width:1px; border-style:solid ;">
            <tr >
                <td><strong>De: </strong>{!! $aR->Reunion->Responsable->fullName !!} </td>
                </tr>
        </table>
    </div>
    <div class="container" style="font-size: 1.3em; line-height: 1.3em" >
        <p>  Recordar-vos les dates de matrícula del curs 2025-26 .</p>
        <ul>
            <li style=" margin-top: 1.3em">
                La data de matrícula serà a partir del 30 de juny fins al 7 de juliol a les 14hores</strong>.
            </li>
            <li style=" margin-top: 1.3em">
                La matríula es farà de forma telemàtica mitjançant un assistent al qual entraràs polsant sobre el
                següent enllaç:<br/>
                <a href="http://ext.matricula.cipfpbatoi.es/{{$convocatoria}}/{{$aR->token}}" >
                    http://ext.matricula.cipfpbatoi.es/{{$convocatoria}}/{{$aR->token}}
                </a>
            </li>
            <li style=" margin-top: 1.3em; list-style-type: none">
                <strong style="color:crimson;font-size: 1.3em">
                    Aquest enllaç és únic i vàlid només per a tú en el període específicat.
                </strong>
            </li>
            <li style=" margin-top: 1.3em">
                Hi ha un manual del procediment que pots consultar en el següent enllaç:
                <a href="https://bit.ly/2SGWC2o">https://bit.ly/2SGWC2o</a>
            </li>
            <li style=" margin-top: 1.3em">
                Per a qualsevol dubte, pots cridar al centre (966527660) o enviar un correu a 03012165.secretaria+automatricula@edu.gva.es.
            </li>
        </ul>
        <p>Salutacions cordials.</p>
    </div>
@endsection
