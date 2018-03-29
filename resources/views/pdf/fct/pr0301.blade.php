@extends('layouts.pdf')
@section('content')
<body style="max-width:27.59cm;margin-top:1.251cm; margin-bottom:1.251cm; margin-left:1.5cm; margin-right:1.5cm; ">
    @include('pdf.fct.partials.cabecera')
    <br/>
    <table border="1" style="margin-bottom: 10px">
        <colgroup><col width="449"/><col width="329"/></colgroup>
        <tr>
            <td style="text-align:left;width:13.283cm;padding-left: 5px; "><strong>Revisión actual:</strong><span> 8</span></td>
            <td style="text-align:left;width:10.523cm;padding-left: 5px; "><strong>Fecha revisión actual:</strong><span> 13-04-2016</span></td>
        </tr>
        <tr>
            <td style="text-align:left;padding-left: 5px;" colspan="2"><strong>Objetivo:</strong><span>  Comprobar la recepción del certificado de prácticas por parte del alumno</span></td>
        </tr>
    </table>
    <div style="border: black solid thin;padding:5px">
        <h5 style="text-align: center">DATOS GENERALES FCT</h5>
        <ul style="list-style-type: square ; font-size: 13px">
            <li>Seguimiento formación en Empresa. Explicar Anexo V y como rellenarlo con el SAÓ (Su correcta cumplimentación servirá de memoria final del alumno en la  FCT)</li>
            <li>Periodicidad de las visitas de alumnos a profesores-tutores. PR04-02</li>
            <li>Se informa de como utilizar el  registro de quejas y sugerencias (PG06-01), disponible en la web del centro. Zona FCT. </li>
            <li>Información al alumno del seguro de responsabilidad civil y en caso de accidente laboral. (Ir a la web http://www.ceice.gva.es/va/web/formacion-profesional/seguro)</li>
            <li>El alumno conoce  las actividades a realizar en la empresa según Anexo III, donde se especifica su horario y los datos de la empresa y el instructor.</li>
        </ul>
        <table style="width:100px;height:100px;float:left;margin-bottom: 20px"></table>
        <table border='1' style="width:350px;height:100px;float:left;margin-bottom: 20px">
            <tr><td style='text-align: left; vertical-align: top '><strong>Observaciones:</strong></td></tr>
        </table>
        <table border='1' style="width:100px;height:100px;float:right;margin-bottom: 20px"></table>
        
        <table border="1" style="margin-top:20px;">
            <tr>
                <td style="text-align:left;width:11.938cm;padding-left: 5px; "><strong>ALUMNO</strong></td>
                <td style="text-align:left;width:1.938cm;padding-left: 5px; "><strong>Pref</strong></td>
                <td style="text-align:left;width:4.493cm;padding-left: 5px; "><strong>FECHA Incio</strong></td>
                <td style="text-align:left;width:4.493cm;padding-left: 5px; "><strong>FECHA Fin</strong></td>
                <td style="text-align:left;width:1.493cm;padding-left: 5px; "><strong>Horas</strong></td>
                <td style="text-align:left;width:9.45cm;padding-left: 5px; "><strong>FIRMA ALUMNO</strong></td>
            </tr>
            @foreach ($todos as $alumno)
            <tr style='height:40px'><td style="text-align:left;width:11.938cm;padding-left: 5px;font-size: 0.9em;" >{{ $alumno->Alumno->ShortName }} </p></td>
                <td style="text-align:left;width:1.938cm;padding-left: 5px;font-size: 0.9em;"></td>
                <td style="text-align:left;width:4.493cm;font-size: 0.9em; " >{{$alumno->desde}}</td>
                <td style="text-align:left;width:4.493cm;font-size: 0.9em; " >{{$alumno->hasta}}</td>
                <td style="text-align:left;width:1.493cm;font-size: 0.9em; " >{{$alumno->horas}}</td>
                <td style="text-align:left;width:9.45cm;font-size: 0.9em; " ></td>
            </tr>
            @endforeach
        </table>
        <p style='font-size: 8px'>Pref* :Se han tenido en cuenta las preferencias e intereses particulares de los alumnos para asignarle la Empresa</p>
        
    </div>  
    <table border='1' style='width: 100%; margin-top:40px;'>
        <colgroup><col width="20%"/><col width="30%"/><col width="20%"/><col width="30%"/></colgroup>
        <tr style="height:60px"><td style="padding:5px">Nombre Tutor:<br/>Ciclo:</td>
                <td style="padding:5px">{{$alumno->Alumno->Grupo->first()->Tutor->ShortName}}<br/>{{$alumno->Alumno->Grupo->first()->Ciclo->ciclo}}</td>
                <td style="padding:5px">Firma:<br/>Fecha</td>
                <td style="padding:5px"></td>
            </tr>
    </table>
</body>
@endsection