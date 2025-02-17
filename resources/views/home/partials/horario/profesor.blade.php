<table class='table table-bordered'>
    @include('home.partials.horario.cabeceraTabla')
    <tbody>
    @foreach (Intranet\Entities\Hora::all() as $hora)
        <tr>
            <td>{{$hora->hora_ini}} - {{$hora->hora_fin}}</td>
            @foreach (array('L','M','X','J','V') as $dia_semana)
                @if (isset($horario[$dia_semana][$hora->codigo]))
                    @if (isset($horario[$dia_semana][$hora->codigo]->ocupacion))
                        <td id="{{$hora->codigo}}-{{$dia_semana}}" class='warning'><div data-orig="{{$hora->codigo}}-{{$dia_semana}}">
                                <span>{{ $horario[$dia_semana][$hora->codigo]->Ocupacion->nombre??'Ocupacio Desconeguda' }}</span>
                            </div></td>
                    @else
                        <td id="{{$hora->codigo}}-{{$dia_semana}}" class='active'><div data-orig="{{$hora->codigo}}-{{$dia_semana}}">
                                <strong>{{ $horario[$dia_semana][$hora->codigo]->Modulo->cliteral??'Modul desconegut' }}</strong><br/>
                                {{ $horario[$dia_semana][$hora->codigo]->Grupo->nombre }}(
                                {{ $horario[$dia_semana][$hora->codigo]->aula }})
                            </div></td>

                    @endif
                @else
                    <td id="{{$hora->codigo}}-{{$dia_semana}}"></td>
                @endif
            @endforeach
        </tr>
    @endforeach
    </tbody>
    <style type="text/css">
        .movido {
            color: red;
        }
    </style>
</table>