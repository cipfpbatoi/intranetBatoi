@extends('layouts.pdf')
@section('content')
<body style="max-width:47.59cm;margin-top:1.251cm; margin-bottom:1.251cm; margin-left:.1cm; margin-right:.1cm; ">
    @include('pdf.fct.partials.cabecera')
    <br/>
    <table border="1" style="margin-bottom: 10px">
        <colgroup><col width="449"/><col width="329"/></colgroup>
        <tr>
            <td style="text-align:left;width:23.283cm;padding-left: 5px "><strong>Revisión actual:</strong><span> 7</span></td>
            <td style="text-align:left;width:20.523cm;padding-left: 5px "><strong>Fecha revisión actual:</strong><span> 20-03-2014</span></td>
        </tr>
        <tr>
            <td style="text-align:left;padding-left: 5px" colspan="2"><strong>Objetivo:</strong><span>  Facilitar al tutor el seguimiento de los alumnos durante la FCT</span></td>
        </tr>
    </table>
    <table border="1" style="margin-bottom: 5px">
        <tr>
            <td style="text-align:left;width:23.283cm;padding-left: 5px;font-size: 0.8em"><strong>Tutor: </strong><span>{{$todos->first()->Alumno->Grupo->first()->Tutor->FullName}}</span></td>
            <td style="text-align:left;width:23.2833cm;padding-left: 5px;font-size: 0.8em "><strong>Ciclo: </strong><span>{{$todos->first()->Alumno->Grupo->first()->Ciclo->ciclo}}</span></td>
        </tr>
    </table>
    <p><strong>SEGUIMIENTO MENSUAL</strong></p>
    <table border="1">
        <colgroup><col width="400"/><col width="250"/>
            <col width="40"/><col width="40"/><col width="40"/><col width="40"/><col width="40"/><col width="40"/>
            <col width="40"/><col width="40"/><col width="40"/><col width="40"/><col width="40"/><col width="40"/></colgroup>
        <tr>
            <td rowspan='3' valign='top' style="text-align:left;width:14.938cm;padding-left: 5px;font-size: 0.8em "><strong>ALUMNO/OS Y EMPRESA</strong></td>
            <td rowspan='3' valign='top' style="text-align:left;width:8.493cm;padding-left: 5px;font-size: 0.8em "><strong>FIRMA Y FECHA</strong><br/>Se podrá la fecha al lado de la firma, en el caso que los alumnos vengan en fechas diferentes</td>
            <td colspan='12' style="text-align:left;padding-left: 5px;font-size: 0.8em"><strong>FECHA DE REUNIÓN COLECTIVA:</strong><br/>PUNTUACION: 1 Deficiente 2 Norma 3 Muy Adecuado</td>
        </tr>
        <tr>
            <td colspan="3" style="padding-left: 2px;padding-right: 2px"> A:INFORMACION </td>
            <td colspan="3" style="padding-left: 2px;padding-right: 2px"> B:RELACIÓN </td>
            <td colspan="3" style="padding-left: 2px;padding-right: 2px"> C:ADECUACION </td>
            <td colspan="3" style="padding-left: 2px;padding-right: 2px"> D:SATISFACCION</td>
        </tr>
        <tr>
            <td>1</td><td>2</td><td>3</td><td>1</td><td>2</td><td>3</td><td>1</td><td>2</td><td>3</td><td>1</td><td>2</td><td>3</td>
        </tr>
        @foreach ($todos as $alumno)
        <tr><td style="text-align:left;width:9.938cm;padding-left: 5px;font-size: 0.8em " >{{ $alumno->Alumno->FullName }} {{ $alumno->Colaboracion->Centro->Empresa->nombre }} </p></td>
            <td style="text-align:left;width:5.493cm; " ></td>
            <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
        </tr>
        @endforeach
    </table>
    <br/>
    <div style="float:left;width: 600px;">
         <ol style="list-style-type: upper-latin;font-size: xx-small; font-weight: bold ">
            <li>INFORMACION RECIBIDA DEL INSTRUCTOR</li>
            <li>RELACIÓN CON EL ENTORNO DE TRABAJO</li>
            <li>ADECUACION DE TAREAS AL PROGRAMA FORMATIVO</li>
            <li>GRADO DE SATISFACCION CON LA FORMACION RECIBIDA Y LAS PRACTICAS REALIZADAS EN LA EMPRESA</li>
        </ol>
    </div>
    <div style="float:right;width: 300px;height:60px"">
         <table border='1' style="width: 300px;height:60px"><tr><td valign='top' style="text-align: left;padding-left: 5px;font-size: 0.8em">Firma del tutor: <br/> </td></tr></table>
    </div>

</body>
</html>
@endsection