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
        <li>El teu NIA, necessari per a la matrícula telemàtica del proper curs.</li>
        <li>Opcionalment, també pots tindre diferents observacions dels mòduls que t’han pogut fer el professorat.</li>
    </ul>
    <p>Et recordem que pots veure les teues qualificacions en la web família (<a href="https://familia.edu.gva.es/">https://familia.edu.gva.es/</a>).
        Si tens qualsevol problema per a entrar, fica’t en contacte amb la secretaria del centre (<strong>secretaria@cipfpbatoi.es</strong> o <strong>03012165.secret@gva.es</strong>).</p>
    <p>Aprofitem per a recordar-te altra informació del teu interés</p>
    <ul>
        <li>Les reclamacions a les qualificacions dels diferents mòduls de la convocatòria final ordinària seran els dies 15, 16 i 17 de juny.
            El procediment per a realitzar les reclamacions serà telemàtic i està explicat a la web del centre en l’apartat “Secretaria → Tràmits → Reclamacions de qualificacions”
            (<a href="http://www.cipfpbatoi.es/index.php/ca/tramits-2/">http://www.cipfpbatoi.es/index.php/ca/tramits-2/</a>).</li>
        <li>Matrícula d’alumnat presencial. Si eres alumnat de grups presencials, les dates i el procediment per a realitzar la matrícula,
            tant de l’alumnat que repeteix curs com del que promociona, seran informades a través del vostre tutor/a al teu correu electrònic.
            La matrícula serà telemàtica mitjançant un formulari (la direcció del formulari serà facilitada pel teu tutor/a).
            Les dates de realització seran del 29 de juny al 5 de juliol. </li>
        <li>Preinscripció d’alumnat semipresencial. Si eres alumnat de grups semipresencials, has de fer obligatòriament la preinscripció telemàtica de sol·licitud d'admissió
            per obtenir plaça en els mòduls que et falten per cursar o que pots repetir (l’assistent no adjudica més de 1000 hores).
            Es realitza a través de l'assistent telemàtic de la Conselleria d'Educació del 17 al 25 de juny de 2020.</li>
        <li>La matrícula de les FCTs, es realitzarà després de finalitzar els mòduls teòrics. Aquell alumnat que NOMÉS els quede per cursar les FCTs (i/o mòdul de projecte), no ha de fer sol·licitud telemàtica de preinscripció i ha de matricular-se segons s’indica al punt 2.</li>
    </ul>
    <p>L'enllaç per a pujar la documentació:
        <a href="http://matricula.cipfpbatoi.es/?token={{$aR->token}}" >http://matricula.cipfpbatoi.es/?token={{ $aR->token }}</a>
    </p>
    Salutacions cordials.
</div>
@endsection