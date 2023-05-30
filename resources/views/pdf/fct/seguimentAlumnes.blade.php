@extends('layouts.pdf')
@section('content')
    @include('pdf.fct.partials.cabecera')
    <br/>
    <table border="1" style="margin-bottom: 5px">
        <tr>
            <td style="text-align:left;width:23.283cm;padding-left: 5px;font-size: 0.8em">
                <strong>Tutor: </strong><span>{{authUser()->FullName}}</span></td>
            <td style="text-align:left;width:23.2833cm;padding-left: 5px;font-size: 0.8em">
                <strong>Cicle: </strong><span>{{$todos->first()->Fct->Colaboracion->Ciclo->ciclo??''}}</span></td>
        </tr>
    </table>
    <p><strong>SEGUIMENT MENSUAL</strong></p>
    <table border="1">
        <colgroup>
            <col width="400"/>
            <col width="250"/>
            <col width="40"/>
            <col width="40"/>
            <col width="40"/>
            <col width="40"/>
            <col width="40"/>
            <col width="40"/>
            <col width="40"/>
            <col width="40"/>
            <col width="40"/>
            <col width="40"/>
            <col width="40"/>
            <col width="40"/>
        </colgroup>
        <tr>
            <td rowspan='3' valign='top' style="text-align:left;width:14.938cm;padding-left: 5px;font-size: 0.8em ">
                <strong>ALUMNE/ES I EMPRESA</strong></td>
            <td rowspan='3' valign='top' style="text-align:left;width:8.493cm;padding-left: 5px;font-size: 0.8em ">
                <strong>SIGNATURA I DATA</strong><br/>Es posarà la data al costat de la signatura en el cas que els
                alumnes vinguen en dates diferents.
            </td>
            <td colspan='12' style="text-align:left;padding-left: 5px;font-size: 0.8em"><strong>DATA DE REUNIÓ
                    COL·LECTIVA:</strong><br/>PUNTUACIÓ: 1 Deficient 2 Normal 3 Molt Adequat
            </td>
        </tr>
        <tr>
            <td colspan="3" style="padding-left: 2px;padding-right: 2px"> A:INFORMACIÓ</td>
            <td colspan="3" style="padding-left: 2px;padding-right: 2px"> B:RELACIÓ</td>
            <td colspan="3" style="padding-left: 2px;padding-right: 2px"> C:ADEQUACIÓ</td>
            <td colspan="3" style="padding-left: 2px;padding-right: 2px"> D:SATISFACCIÓ</td>
        </tr>
        <tr>
            <td>1</td>
            <td>2</td>
            <td>3</td>
            <td>1</td>
            <td>2</td>
            <td>3</td>
            <td>1</td>
            <td>2</td>
            <td>3</td>
            <td>1</td>
            <td>2</td>
            <td>3</td>
        </tr>
        @foreach ($todos??[] as $alumno)
            @isset ($alumno)
                <tr>
                    <td style="text-align:left;width:9.938cm;padding-left: 5px;font-size: 0.8em ">
                        <strong>{{ $alumno->Alumno->FullName }}</strong>
                        ({{ $alumno->Fct->Colaboracion->Centro->nombre }})
                    </td>
                    <td style="text-align:left;width:5.493cm; "></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
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
    </table>
    <br/>
    <div style="float:left;width: 600px;">
        <ol style="list-style-type: upper-latin;font-size: xx-small; font-weight: bold ">
            <li>INFORMACIÓ REBUDA DE L'INSTRUCTOR</li>
            <li>RELACIÓ AMB L'ENTORN DE TREBALL</li>
            <li>ADEQUACIÓ DE TASQUES AL PROGRAMA FORMATIU</li>
            <li>GRAU DE SATISFACCIÓ AMB LA FORMACIÓ REBUDA I LES PRÀCTIQUES REALITZADES A L'EMPRESA</li>
        </ol>
    </div>
    <div style="float:right;width: 300px;height:60px">
        <table border='1' style="width: 300px;height:60px">
            <tr>
                <td valign='top' style="text-align: left;padding-left: 5px;font-size: 0.8em">Signatura del tutor: <br/>
                    {!!  Intranet\Services\SignaturaService::exec(authUser()->dni,"width:100%;float:left",0.5) !!}
                </td>
            </tr>
        </table>
    </div>
@endsection