@extends('layouts.email')
@section('body')
    <table style='text-align: center'>
        <tr>
            <th>Inici de les pràctiques de FCT</th>
        </tr>
    </table>
    <div>
        <table style=" border:#000 solid 1px;">

            <tr>
                <td><strong>Del teu tutor</strong>
            </tr>
            <tr>
                <td>{{ authUser()->fullName }}</td>
            </tr>

        </table>
    </div>
    <div class="container">
        <h5 style="text-align: center">DADES GENERALS FCT</h5>
        <p>Hola {{$elemento->Alumno->nombre}}. Com hem parlat en la reunió de preparació de les pràctiques de FCT
            t'envie recordatori de les coses que has de tindre en compte durant este període:</p>
        <ul style="list-style-type: square ; font-size: 13px">
            <li>Has d'emplenar setmanalment el teu diari de pràctiques en el SAÓ que es troba a <a
                        href="http://fct.edu.gva.es/">http://fct.edu.gva.es/</a>.
                El seu correct ompliment et servirà per recordar millor les teues pràctiques de FCT.
            </li>
            <li>Una vegada al mes, ens reunirem tot el grup a l'institut per a comentar el desenvolupament de les
                pràctiques. L'assistència a estes reunions és obligatòria i compta en el còmput total d'hores de les
                pràctiques. Et convocaré per correu electrònic amb antelació suficient.
            </li>
            <li>També faré visites al centre de les quals informaré el teu instructor.</li>
            <li>Tens una assegurança de responsabilitat civil i d'accident laboral. La informació la trobaràs en aquest
                <a href="https://ceice.gva.es/va/web/formacion-profesional/seguro">enllaç</a></li>
            <li>Les tasques a realitzar a l'empresa seran les que es deriven de l'Annex III, que et donaré i on
                s'especifica el teu horari i les dades de l'empresa i instructor
            </li>
        </ul>
        <p>Les dades de la teua empresa son:</p>
        <table border="1" style="margin-top:20px;">
            <tr>
                <td style="text-align:left;width:8.938cm;padding-left: 5px; "><strong>Empresa</strong></td>
                <td style="text-align:left;width:4.493cm;padding-left: 5px; "><strong>DATA Inici</strong></td>
                <td style="text-align:left;width:4.493cm;padding-left: 5px; "><strong>DATA Aprox. Fi</strong></td>
                <td style="text-align:left;width:1.493cm;padding-left: 5px; "><strong>Hores</strong></td>
            </tr>
            <tr style='height:40px'>
                <td style="text-align:left;width:8.938cm;padding-left: 5px;font-size: 0.9em;">{{$elemento->Fct->Colaboracion->Centro->nombre}}</td>
                <td style="text-align:left;width:4.49cm;font-size: 0.9em;text-align: center ">{{$elemento->desde}}</td>
                <td style="text-align:left;width:4.49cm;font-size: 0.9em;text-align: center ">{{$elemento->hasta}}</td>
                <td style="text-align:left;width:1.493cm;font-size: 0.9em;text-align: center ">{{$elemento->horas}}</td>
            </tr>
        </table>
    </div>
@endsection