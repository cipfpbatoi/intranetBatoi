@extends('layouts.pdf')
@section('header')
    @include('pdf.fct.partials.cabecera')
@endsection
@section('content')
    <br/>
    <table border="1">
        <colgroup>
            <col width="400"/>
            <col width="40"/>
            <col width="40"/>
            <col width="40"/>
            <col width="40"/>
            <col width="100"/>
            <col width="150"/>
            <col width="150"/>
        </colgroup>
        <tr>
            <td colspan='8' style="text-align:left;font-weight: bold;font-size: 1.1em">Tutor i
                cicle: {{authUser()->FullName}} - {{ $todos->first()->Fct->Colaboracion->Ciclo->ciclo ?? ''}}</td>
        </tr>
        <tr>
            <td style="text-align:left;font-weight: bold;font-size: 0.8em ">ALUMNE I EMPRESA</td>
            <td style="text-align:center;font-weight: bold;font-size: 0.8em">I</td>
            <td style="text-align:center;font-weight: bold;font-size: 0.8em ">II</td>
            <td style="text-align:center;font-weight: bold;font-size: 0.8em">III</td>
            <td style="text-align:center;font-weight: bold;font-size: 0.8em ">IV</td>
            <td style="text-align:center;font-weight: bold;font-size: 0.8em ">DATA</td>
            <td style="text-align:center;font-weight: bold;font-size: 0.8em">SIGNATURA TUTOR</td>
            <td style="text-align:center;font-weight: bold;font-size: 0.8em ">SIGNATURA CAP PRÀCTIQUES</td>
        </tr>
        @foreach ($todos??[] as $fct)
            @isset($fct->Fct)
                <tr style="height: 50px">
                    <td style="text-align:left;font-size: 0.9em ">
                            {{ $fct->Fct->Colaboracion->Centro->nombre??'' }} ({{ $fct->Alumno->fullName??'' }})
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            @endisset
        @endforeach
        <tr>
            <td colspan="8" style="text-align:left;font-size: 12px;">
                <br/><br/>
                <p>1.- Es lliurarà al/a la cap de departament de pràctiques la següent documentació:<br/>
                <ul>
                    <li>1 original del C. Educatiu, Annex I (si és col·laboradora per primera vegada).</li>
                    <li>1 original de l'Annex II (Centre Educatiu).</li>
                    <li>1 original de l'Annex III (Centre Educatiu).</li>
                    <li>1 original de l'Annex VII (Si escau).</li>
                    <li>1 original permís de realització d'FCT en període de vacances de Nadal (si escau)</li>
                    <li>1 original permís de realització d'FCT en període de vacances de Pasqües (si escau)</li>
                    <li>Annex Declaració responsable de l'empresa de que el personal compta amb acreditació negativa del
                        registre central de delinqüents sexuals (si van alumnes menors d'edat)
                    </li>
                </ul>
                </p>
                <p style="text-align:left;font-weight: bold">IMPORTANT: El termini màxim de lliurament de la
                    documentació(també s'haurà d'ajuntar al SAO) al Departament d'FCT serà d'un MÀXIM DE 10 DIES HÀBILS
                    abans que l'alumne inicie les pràctiques d'FCT.</p>

            </td>
        </tr>
    </table>
@endsection
