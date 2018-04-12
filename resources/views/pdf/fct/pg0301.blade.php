@extends('layouts.pdf')
@section('content')
@php
$agrupados = $todos->groupBy('idColaboracion')
@endphp
    @include('pdf.fct.partials.cabecera')
    <br/>
    <table border="1" cellspacing="0" cellpadding="">
        <colgroup><col width="449"/><col width="329"/></colgroup>
        <tr>
            <td style="text-align:left;width:13.283cm; "><strong>Revisión actual:</strong><span> 8</span></td>
            <td style="text-align:left;width:10.523cm; "><strong>Fecha revisión actual:</strong><span> 4-10-2017</span></td>
        </tr>
    </table>
    <br/>
    <table border="1" >
        <colgroup><col width="400"/><col width="40"/><col width="40"/><col width="40"/><col width="40"/><col width="100"/><col width="150"/><col width="150"/></colgroup>
        <tr>
            <td colspan='8' style="text-align:left;font-weight: bold;font-size: 1.1em">Tutor y ciclo: {{$todos->first()->Alumno->Grupo->first()->Tutor->FullName}} - {{$todos->first()->Alumno->Grupo->first()->Ciclo->ciclo}}</td>
        </tr>
        <tr >
            <td style="text-align:left;font-weight: bold;font-size: 0.8em ">EMPRESA Y NUMERO DE ALUMNOS</td>
            <td style="text-align:center;font-weight: bold;font-size: 0.8em">I</td>
            <td style="text-align:center;font-weight: bold;font-size: 0.8em ">II</td>
            <td style="text-align:center;font-weight: bold;font-size: 0.8em">III</td>
            <td style="text-align:center;font-weight: bold;font-size: 0.8em ">IV</td>
            <td style="text-align:center;font-weight: bold;font-size: 0.8em ">FECHA</td>
            <td style="text-align:center;font-weight: bold;font-size: 0.8em">FIRMA TUTOR</td>
            <td style="text-align:center;font-weight: bold;font-size: 0.8em ">FIRMA JEFE PRACTICAS</td>
        </tr>
        @foreach ($agrupados as $index => $aluempresa)
        <tr style="height: 50px"><td style="text-align:left;font-size: 0.9em " >{{ $aluempresa->first()->Colaboracion->Centro->nombre }} 
                ({{count($aluempresa)}})</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        @endforeach
        <tr><td colspan="8" style="text-align:left;font-size: 12px;" >
                <br/><br/>
                <p>1.- Se entregará al jefe/a de departamento de prácticas la siguiente documentación:<br/>
                <ul><li>1 original del C. Educativo, Anexo I ( si es colaboradora por primera vez). </li>
                    <li>3 original del Anexo II. </li>
                    <li>2 original del Anexo III </li>
                    <li>1 original del Anexo VII </li>
                    <li>1 original permiso realización FCT en periodo de  Vacaciones de Navidad (si procede) </li>
                    <li>1 original permiso realización FCT en periodo de Vacaciones de Pascua (si procede) </li>
                    <li>Anexo Declaración responsable  de la empresa de que el personal cuenta con acreditación negativa del  registro central de delincuentes sexuales (si van alumnos menores)</li>
                </ul>
                </p>
                <p style="text-align:left;font-weight: bold">IMPORTANTE: El plazo máximo de entrega de la documentación al Departamento de FCT,  será MÁXIMO 10 DIAS HÁBILES antes de que el alumno inicie las prácticas de FCT.</p>

            </td>
        </tr>
    </table>
@endsection