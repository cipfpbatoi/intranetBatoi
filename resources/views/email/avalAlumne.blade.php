@extends('layouts.email')
@section('body')
<table style='text-align: center'>
    <tr>
        <th>Resultats Avaluació Curs</th>
    </tr>
</table>
<div>
    <table style=" border:#000 solid 1;">
        <tr >
            <td><strong>De: </strong>{!! $aR->Reunion->Responsable->fullName !!} </td>
        </tr>
    </table>
</div>
<div class="container" >
    <p>Hola, primer que tot, esperem que estigues bé, tant tu com la teu família. A continuació et comentem informació important sobre la finalització del curs i la preparació del proper curs.</p>
    <p>A aquest correu, s’adjunta el teu informe individual on trobaràs:</p>
    <ul>
        <li>Informació sobre els diferents continguts pendents d’impartir-se en alguns mòduls i que es recuperaran el  proper curs.</li>
        <li>El teu seguiment del tercer trimestre dels diferents mòduls.</li>
        <li>Informació sobre la teua promoció o no, indicant els motius.</li>
        <li>Opcionalment, també pots tindre diferents observacions dels mòduls que t’han pogut fer el professorat.</li>
    </ul>
    <p>Et recordem que pots veure les teues qualificacions en la web família (<a href="https://familia.edu.gva.es/">https://familia.edu.gva.es/</a>).
        Si tens qualsevol problema per a entrar, fica’t en contacte amb la secretaria del centre (<strong>secretaria@cipfpbatoi.es</strong> o <strong>03012165.secret@gva.es</strong>).</p>
    <p>Aprofitem per a recordar-te altra informació del teu interés</p>
    <p>Les reclamacions a les qualificacions dels diferents mòduls de la convocatòria final ordinària seran els dies 15, 16 i 17 de juny.
        El procediment per a realitzar les reclamacions serà telemàtic i està explicat a la web del centre en l’apartat “Secretaria → Tràmits → Reclamacions de qualificacions”
        (<a href="http://www.cipfpbatoi.es/index.php/ca/tramits-2/">http://www.cipfpbatoi.es/index.php/ca/tramits-2/</a>).
    </p>
    <p>La teua matrícula per al curs 2020-21 es farà de la següent forma:</p>
    <ul>
        <li>La data de matrícula serà del 29 de juny al 7 de juliol (ambdós inclosos).</li>
        <li>La matrícula es farà de forma telemàtica mitjançant un assistent al qual entraràs pulsant sobre el següent enllaç:
            <a href="http://ext.matricula.cipfpbatoi.es/{{config('curso.convocatoria')}}/{{$aR->token}}" >http://ext.matricula.cipfpbatoi.es/{{config('curso.convocatoria')}}/{{$aR->token}}</a></li>
        <li>Aquest enllaç és únic i vàlid només per a tu.</li>
   </ul>
    <p>Per a qualsevol dubte, pots cridar al centre (966527660) o enviar un correu a info@cipfpbatoi.es.</p>
    Salutacions cordials.
</div>
@endsection