@extends('layouts.pdf')
@section('content')
    <div class="container col-lg-12" style="border: #0a0302 thin solid" >
        <table border="1">
            <caption>DOCUMENT 4/ DOCUMENTO 4</caption>
            <colgroup><col witdh='250'><col width="150"/><col width="600"/></colgroup>
            <tr>
                <td><img src="{{public_path('/img/pdf/conselleria.png')}}" style="width:160px;height:90px" /></td>
                <td><img src="{{public_path('/img/pdf/ue.png')}}" style="width:90px;height:90px" /></td>
                <td><h2>4.HORARI DEL CICLE FORMATIU EN EL CENTRE EDUCATIU</h2>
                    <h2>4.HORARIO DEL CICLO FORMATIVO EN EL CENTRO EDUCATIVO</h2>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <h3>CENTRE EDUCATIU / CENTRO EDUCATIVO</h3>
                    <h3>{{ strtoupper(config('contacto.nombre')) }}</h3>
                </td>
                <td>
                    <h3>CICLE FORMATIU / CICLO FORMATIVO</h3>
                    <h3>{{ strtoupper($datosInforme['ciclo']) }}</h3>
                </td>
            </tr>
        </table>
        <br/>
        <table border="1">
            <caption>HORARI DE {{ $datosInforme['grupo']->curso == 1?'PRIMER':'SEGON' }}/
                HORARIO DE {{$datosInforme['grupo']->curso == 1?'PRIMERO':'SEGUNDO'}}</caption>
            <colgroup>
                <col width="160"/>
                <col width="160"/>
                <col width="160"/>
                <col width="160"/>
                <col width="160"/>
                <col width="160"/>
            </colgroup>
            <tr>
                <th>HORA</th>
                <th>DL / L</th>
                <th>DT / M</th>
                <th>DC / X</th>
                <th>DJ / J</th>
                <th>DV / V</th>
            </tr>
            @foreach (Intranet\Entities\Hora::where('turno',$datosInforme['turno'])->get() as $hora)
                <tr >
                    <td style="font-size: 16px">{{$hora->hora_ini}}</td>
                    @foreach (array('L','M','X','J','V') as $dia_semana)
                        <td style="font-size: 16px"><strong>{{$todos[$dia_semana][$hora->codigo]->Modulo->codigo??''}}</strong></td>
                    @endforeach
                </tr>
            @endforeach
        </table>
        <br />
        <table border="1">
            <colgroup><col witdh='150'><col width="800"/></colgroup>
            <tr>
                <th>CODI/CÓDIGO</th>
                <th>MÒDUL/MÓDULO</th>
            </tr>
            @foreach ( $datosInforme['grupo']->Modulos as $modulo)
                <tr>
                    <td style="font-size: 16px">{{$modulo->ModuloCiclo->Modulo->codigo}}</td>
                    <td style="font-size: 16px">{{$modulo->ModuloCiclo->Modulo->vliteral}}</td>
                </tr>
            @endforeach
        </table>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <p>
            @if ($datosInforme['grupo']->curso == 1)
                <h3>HORARI DE SEGON / HORARIO DE SEGUNDO</h3>
                <h4>TORN: {{ ucwords($datosInforme['turno']) }}. L'horari de l'alumne en segon és compatible amb l'horari a desenvolupar a l'empresa.</h4>
            @endif
        </p>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
    </div>
@endsection


