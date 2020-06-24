@extends('layouts.email')
@section('body')
    <table style='text-align: center'>
        <tr>
            <th>Resultats Avaluació Curs</th>
        </tr>
    </table>
    <div>
        <table style="color:#000; border-width:1px; border-style:solid ;">
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
        <p><strong>Sol·licitud d’admissió i matrícula</strong></p>
        <p>Com alumnat de règim semipresencial, has de realitzar obligatòriament la preinscripció telemàtica de sol·licitud d'admissió per obtenir plaça
            en els mòduls que et falten per cursar o que pots repetir (l’assistent no adjudica més de 1000 hores).
            Es realitza a través de l'assistent telemàtic de la Conselleria d'Educació, del 17 al 25 de juny de 2020.
            Telematrícula: <a href="https://portal.edu.gva.es/telematricula/">https://portal.edu.gva.es/telematricula/</a>.
            Pots obtindre més informació a: <a href="http://www.ceice.gva.es/va/web/admision-alumnado/inicio">http://www.ceice.gva.es/va/web/admision-alumnado/inicio</a></p>
        <p>
            Després de comprovar la llista d'admesos (llistat provisional 20 de juliol), realitzaràs
            la matrícula del 1 al 9 de setembre, publicarem tota l’informació necesària per a la seua realització en la web del CIPFP Batoi.
        </p>
        <p>Cal tindre en compte diverses coses:</p>
        <ul>
            <li><strong>MOLT IMPORTANT</strong>: En omplir la sol·licitud de preinscripció en el assistent telemàtic, has de marcar que actualment estàs matriculat en el cicle.
                Així, secretaria podrà certificar les hores cursades i aprovades. Recorda que hauràs de formalitzar posteriorment la matrícula.</li>
            <li>Has de comprovar les llistes provisionals el 20 de juliol per reclamar/esmenar els possibles errors.
                Les reclamacions també es faràn de forma telemàtica</li>
            <li>La matrícula de les FCTs, es realitzarà després de finalitzar els mòduls teòrics.
                A l’alumnat que NOMÉS els quede per cursar les FCTs (i/o mòdul de projecte), NO han de fer sol·licitud telemàtica de preinscripció mitjançant telematricula.
            <li>T'enviarem un enllaç després de l'avaluació extraordinària per fer el procés.</li>
        </ul>
        <p>Per a qualsevol dubte, pots cridar al centre (966527660) o enviar un correu a info@cipfpbatoi.es.</p>
        Salutacions cordials.
    </div>
@endsection