@extends('layouts.pdf')
@section('content')
<body style="max-width:27.59cm;margin-top:1.251cm; margin-bottom:1.251cm; margin-left:1.5cm; margin-right:1.5cm; ">
    @include('pdf.fct.partials.cabecera')
    <br/>
    <table border="1" style="margin-bottom: 10px">
        <colgroup><col width="449"/><col width="329"/></colgroup>
        <tr>
            <td style="text-align:left;width:13.283cm;padding-left: 5px; "><strong>Revisió actual:</strong><span> 8</span></td>
            <td style="text-align:left;width:10.523cm;padding-left: 5px; "><strong>Data revisió actual:</strong><span> 13-04-2016</span></td>
        </tr>
        <tr>
            <td style="text-align:left;padding-left: 5px;" colspan="2"><strong>Objectiu:</strong><span> Comprovar la recepció del certificat de pràctiques per part de l'alumne</span></td>
        </tr>
    </table>
    <div style="border: black solid thin;padding:5px">
        <h5 style="text-align: center">DADES GENERALS FCT</h5>
        <ul style="list-style-type: square ; font-size: 13px">
            <li>Has d'emplenar semanalment el teu diari de pràctiques (memòria) en el SAÓ que es troba a <a href="http://fct.edu.gva.es/">http://fct.edu.gva.es/</a>. La correcta complimentació et servirà de memòria final de pràctiques de FCT.</li>
            <li>Una vegada al mes, ens reunirem tot el grup a l'institut per a comentar el desenvolupament de les pràctiques. L'assistència a estes reunions és obligatòria i conta en el comput total d'hores de les pràctiques. Us convocaré per correu electrònic amb antelació suficient.</li>
            <li>També farè visites al centre de les que informaré al teu instructor.</li>
            <li>Tens una assegurança de responsabilitat civil i d'accident laboral. La informació la trobaràs en aquest <a href="https://ceice.gva.es/va/web/formacion-profesional/seguro">enllaç</a></li>
            <li>Les tasques a realitzar a l'empresa seran las que es deriven de l'Annex III, que et donaré i on s'especifica el teu horari i les dades de l'empresa i instructor</li>
        </ul>
        <table style="width:100px;height:100px;float:left;margin-bottom: 20px"></table>
        <table border='1' style="width:350px;height:100px;float:left;margin-bottom: 20px">
            <tr><td style='text-align: left; vertical-align: top '><strong>Observacions:</strong></td></tr>
        </table>
        <table border='1' style="width:100px;height:100px;float:right;margin-bottom: 20px"></table>
        
        <table border="1" style="margin-top:20px;">
            <tr>
                <td style="text-align:left;width:8.938cm;padding-left: 5px; "><strong>ALUMNE</strong></td>
                <td style="text-align:left;width:8.938cm;padding-left: 5px; "><strong>Empresa</strong></td>
                <td style="text-align:left;width:0.838cm;padding-left: 5px; "><strong>Pref.</strong></td>
                <td style="text-align:left;width:4.493cm;padding-left: 5px; "><strong>DATA Inici</strong></td>
                <td style="text-align:left;width:4.493cm;padding-left: 5px; "><strong>DATA Fi</strong></td>
                <td style="text-align:left;width:1.493cm;padding-left: 5px; "><strong>Hores</strong></td>
                <td style="text-align:left;width:12.45cm;padding-left: 5px; "><strong>SIGNATURA ALUMNE/A</strong></td>
            </tr>
            @foreach ($todos as $alumno)
            <tr style='height:40px'><td style="text-align:left;width:8.938cm;padding-left: 5px;font-size: 0.9em;" > {{ $alumno->Alumno->ShortName }} </td>
                <td style="text-align:left;width:8.938cm;padding-left: 5px;font-size: 0.9em;">{{$alumno->Fct->Colaboracion->Centro->nombre}}</td>
                <td style="text-align:left;width:0.838cm;padding-left: 5px;font-size: 0.9em;"></td>
                <td style="text-align:left;width:4.49cm;font-size: 0.9em;text-align: center ">{{$alumno->desde}}</td>
                <td style="text-align:left;width:4.49cm;font-size: 0.9em;text-align: center ">{{$alumno->hasta}}</td>
                <td style="text-align:left;width:1.493cm;font-size: 0.9em;text-align: center ">{{$alumno->horas}}</td>
                <td style="text-align:left;width:12.45cm;font-size: 0.9em; "></td>
            </tr>
            @endforeach
        </table>
        <p style='font-size: 8px'>Pref* :S'han tingut en compte les preferències i interessos particulars dels alumnes per assignar l'Empresa.</p>
        
    </div>  
    <table border='1' style='width: 100%; margin-top:40px;'>
        <colgroup><col width="20%"/><col width="30%"/><col width="20%"/><col width="30%"/></colgroup>
        <tr style="height:60px"><td style="padding:5px">Nom Tutor:<br/>Cicle:</td>
                <td style="padding:5px">{{AuthUser()->FullName}}<br/>{{$todos->first()->Fct->Colaboracion->Ciclo->ciclo}}</td>
                <td style="padding:5px">Signatura:<br/>Data</td>
                <td style="padding:5px"></td>
            </tr>
    </table>
</body>
@endsection