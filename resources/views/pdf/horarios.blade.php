@extends('layouts.pdf')
@section('content')
    @foreach ($todos as $index => $horario)
        @php $profesor = Intranet\Entities\Profesor::find($index); @endphp
        <div class='page'>
            @include('pdf.partials.cabecera')
            <br/><br/>
            <h5 style="text-align: center">Professor: {{ $profesor->FullName}}</h5>
            <table class='table table-bordered'>
                <colgroup><col width="100"/><col width="100"/><col width="100"/><col width="100"/><col width="100"/><col width="100"/></colgroup>
                @include('home.partials.horario.cabeceraTabla')
                <tbody>
                    @foreach (Intranet\Entities\Hora::all() as $hora)
                    <tr>
                        <td>{{$hora->hora_ini}} - {{$hora->hora_fin}}</td>
                        @foreach (array('L','M','X','J','V') as $dia_semana)
                            @if (isset($horario[$dia_semana][$hora->codigo]))
                                @if (isset($horario[$dia_semana][$hora->codigo]->modulo))
                                <td class='active'>
                                    <strong>{{ $horario[$dia_semana][$hora->codigo]->Modulo->cliteral }}</strong>
                                </td>
                                @else
                                <td class='warning'>{{ $horario[$dia_semana][$hora->codigo]->Ocupacion->nombre }}</td>
                                @endif
                            @else
                            <td></td>
                            @endif
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <br/>
            <div class="container ">
                <div style="width:32%;float:left">Observacions : </div>
                <div style="width:60%;float:right"><table style="border: #000 thin solid;width: 80%; height: 90px;"><tr><td style="padding:5px; vertical-align: top;font-size: 12px;">{{$datosInforme[$index]}}</td></tr></table></div>
            </div>
            <div class="container">
                <br /><br /><br /><br /><br /><br/><br/>
                <div style="width:32%;float:left">Vist i plau<br/>{{config('signatures.horarios')}}</div>
                <div style="width:32%;float:left;text-align: center">Segell</div>
                <div style="width:32%;float:right;text-align: right">{{strtoupper(config('contacto.poblacion'))}} a {{FechaString()}}<br/><br/>
                    @if ($profesor->sexo == 'H') 
                        Assabentat<br/>el professor
                    @else 
                        Assabentada<br/>la professora
                    @endif
                </div>    
            </div>
        </div>
    @endforeach
@endsection

