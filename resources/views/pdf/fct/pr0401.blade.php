@extends('layouts.pdf')
@section('content')
@php
   $agrupados = $todos->groupBy('idColaboracion')
@endphp
    @foreach ($agrupados as $grupo)
    <div class="page">
        @include('pdf.fct.partials.cabecera')
        <br/>
        <table border="1" style="margin-bottom: 10px">
            <colgroup><col width="449"/><col width="329"/></colgroup>
            <tr>
                <td style="text-align:left;width:23.283cm;padding-left: 5px "><strong>Revisión actual:</strong><span> 7</span></td>
                <td style="text-align:left;width:20.523cm;padding-left: 5px  "><strong>Fecha revisión actual:</strong><span> 15-12-2017</span></td>
            </tr>
            <tr>
                <td style="text-align:left;padding-left: 5px " colspan="2"><strong>Objetivo:</strong><span>  Facilitar al instructor de la empresa el seguimiento de los alumnos durante la FCT</span></td>
            </tr>
        </table>
        <br/>
        <table border="1" cellspacing="0" cellpadding="0">
            <tr>
                <td style="text-align:left;width:30.283cm;padding-left: 5px;font-size: 0.9em "><strong>Empresa:</strong> <span>{{$grupo->first()->Colaboracion->Centro->nombre}}</span></td>
                <td style="text-align:left;width:30.2833cm;padding-left: 5px;font-size: 0.9em "><strong>Ciclo:</strong> <span>{{$grupo->first()->Alumno->Grupo->first()->Ciclo->ciclo}}</span></td>
            </tr>
            <tr>
                <td style="text-align:left;width:30.283cm;padding-left: 5px;font-size: 0.9em "><strong>Instructors:</strong> <span>
                      @foreach ($grupo->first()->Instructores as $instructor)  {{$instructor->nombre}}, @endforeach
                    </span></td>
                <td style="text-align:left;width:30.2833cm;padding-left: 5px;font-size: 0.9em "><strong>Tutor:</strong> <span>{{$grupo->first()->Alumno->Grupo->first()->Tutor->FullName}}</span></td>
            </tr>
        </table>
        <p><strong>SEGUIMIENTOS</strong></p>
        <table border="1" >
            <colgroup><col width="350"/><col width="120"/><col width="120"/><col width="120"/><col width="120"/><col width="120"/><col width="120"/><col width="120"/><col width="120"/></colgroup>
            <tr>
                <td rowspan='2' valign='top' style="text-align:left;padding-left: 5px;font-size: 0.8em"><strong>ALUMNO (NOMBRE Y APELLIDOS)</strong></td>
                <td colspan='2' style="text-align:left;padding-left: 5px;"><strong>FECHA:<br/>Tipo cto*:</strong></td>
                <td colspan='2' style="text-align:left;padding-left: 5px;"><strong>FECHA:<br/>Tipo cto*:</strong></td>
                <td colspan='2' style="text-align:left;padding-left: 5px;"><strong>FECHA:<br/>Tipo cto*:</strong></td>
                <td colspan='2' style="text-align:left;padding-left: 5px;"><strong>FECHA:<br/>Tipo cto*:</strong></td>
            </tr>
            <tr>
                <td colspan='2'>Observaciones</td><td colspan='2'>Observaciones</td><td colspan='2'>Observaciones</td><td colspan='2'>Observaciones</td>
            </tr>
            @foreach ($grupo as $alumno)
            <tr><td rowspan='2' style="text-align:left;height:1.5cm;padding-left: 5px;font-size: 1em;font-weight: bold " > {{ $alumno->Alumno->FullName }} </p></td><td rowspan='2' colspan="2"><br/><br/></td><td rowspan='2' colspan="2"><br/><br/></td><td rowspan='2' colspan="2"><br/><br/></td><td rowspan='2' colspan="2"><br/><br/></td></tr>
            <tr></tr>
            @endforeach
            <tr style="height:3cm">
                <td valign='top' style="text-align:left;padding-left: 5px;font-size: 1em "><strong>Observaciones Generales:</strong></td>
                <td valign='bottom'>Firma Tutor</td>
                <td valign='bottom'>Firma Instructor</td>
                <td valign='bottom'>Firma Tutor</td>
                <td valign='bottom'>Firma Instructor</td>
                <td valign='bottom'>Firma Tutor</td>
                <td valign='bottom'>Firma Instructor</td>
                <td valign='bottom'>Firma Tutor</td>
                <td valign='bottom'>Firma Instructor</td>
            </tr>
        </table>
            <div style="float:left;">
            <p style="font-size: x-small; font-weight: bold ">
                En caso de evaluación negativa se ha de abrir un registro de Queja y Sugerencias (PG06-01) para solucionar el problema y realizar un seguimiento del mismo.<br/>
                *Contacto con el instructor en la empresa o via telefónica.
            </p>
        </div>
        
    </div>
    @endforeach
@endsection