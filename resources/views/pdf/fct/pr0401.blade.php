@extends('layouts.pdf')
@section('content')
@php
   $agrupados = $todos->groupBy('idFct')
@endphp
    @foreach ($agrupados as $grupo)
    <div class="page">
        @include('pdf.fct.partials.cabecera')
        <br/>
        <table border="1" style="margin-bottom: 10px">
            <colgroup><col width="449"/><col width="329"/></colgroup>
            <tr>
                <td style="text-align:left;width:23.283cm;padding-left: 5px "><strong>Revisió actual:</strong><span> 7</span></td>
                <td style="text-align:left;width:20.523cm;padding-left: 5px  "><strong>Data revisió actual:</strong><span> 15-12-2017</span></td>
            </tr>
            <tr>
                <td style="text-align:left;padding-left: 5px " colspan="2"><strong>Objectiu:</strong><span> Facilitar a l'instructor de l'empresa el seguiment dels alumnes durant l'FCT.</span></td>
            </tr>
        </table>
        <br/>
        <table border="1" cellspacing="0" cellpadding="0">
            <tr>
                <td style="text-align:left;width:30.283cm;padding-left: 5px;font-size: 0.9em "><strong>Empresa:</strong> <span>{{$grupo->first()->Fct->Colaboracion->Centro->nombre}}</span></td>
                <td style="text-align:left;width:30.2833cm;padding-left: 5px;font-size: 0.9em "><strong>Cicle:</strong> <span>{{$grupo->first()->Fct->Colaboracion->Ciclo->ciclo}}</span></td>
            </tr>
            <tr>
                <td style="text-align:left;width:30.283cm;padding-left: 5px;font-size: 0.9em "><strong>Instructors:</strong> <span>
                      {{$grupo->first()->Fct->XInstructor}} @foreach ($grupo->first()->Fct->Colaboradores as $instructor)  ,{{$instructor->nombre}} @endforeach
                    </span></td>
                <td style="text-align:left;width:30.2833cm;padding-left: 5px;font-size: 0.9em "><strong>Tutor:</strong> <span>{{AuthUser()->FullName}}</span></td>
            </tr>
        </table>
        <p><strong>SEGUIMENTS</strong></p>
        <table border="1" >
            <colgroup><col width="350"/><col width="120"/><col width="120"/><col width="120"/><col width="120"/><col width="120"/><col width="120"/><col width="120"/><col width="120"/></colgroup>
            <tr>
                <td rowspan='2' valign='top' style="text-align:left;padding-left: 5px;font-size: 0.8em"><strong>ALUMNE (NOM I COGNOMS)</strong></td>
                <td colspan='2' style="text-align:left;padding-left: 5px;"><strong>DATA:<br/>Tipus cte*:</strong></td>
                <td colspan='2' style="text-align:left;padding-left: 5px;"><strong>DATA:<br/>Tipus cte*:</strong></td>
                <td colspan='2' style="text-align:left;padding-left: 5px;"><strong>DATA:<br/>Tipus cte*:</strong></td>
                <td colspan='2' style="text-align:left;padding-left: 5px;"><strong>DATA:<br/>Tipus cte*:</strong></td>
            </tr>
            <tr>
                <td colspan='2'>Observacions</td><td colspan='2'>Observacions</td><td colspan='2'>Observacions</td><td colspan='2'>Observacions</td>
            </tr>
            @foreach ($grupo as $alumno)
            <tr><td rowspan='2' style="text-align:left;height:1.5cm;padding-left: 5px;font-size: 1em;font-weight: bold " > {{ $alumno->Alumno->FullName }} </p></td><td rowspan='2' colspan="2"><br/><br/></td><td rowspan='2' colspan="2"><br/><br/></td><td rowspan='2' colspan="2"><br/><br/></td><td rowspan='2' colspan="2"><br/><br/></td></tr>
            <tr></tr>
            @endforeach
            <tr style="height:3cm">
                <td valign='top' style="text-align:left;padding-left: 5px;font-size: 1em "><strong>Observacions Generals:</strong></td>
                <td valign='bottom'>Signatura Tutor</td>
                <td valign='bottom'>Signatura Instructor</td>
                <td valign='bottom'>Signatura Tutor</td>
                <td valign='bottom'>Signatura Instructor</td>
                <td valign='bottom'>Signatura Tutor</td>
                <td valign='bottom'>Signatura Instructor</td>
                <td valign='bottom'>Signatura Tutor</td>
                <td valign='bottom'>Signatura Instructor</td>
            </tr>
        </table>
            <div style="float:left;">
            <p style="font-size: x-small; font-weight: bold ">
               En cas d'avaluació negativa s'haurà d'obrir un registre de Queixes i Suggerències (PG06-01) per a resoldre el problema i realitzar un seguiment d'aquest.<br/>
                *Contacte amb l'instructor a l'empresa o via telefònica.
            </p>
        </div>
        
    </div>
    @endforeach
@endsection