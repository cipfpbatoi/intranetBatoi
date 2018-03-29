<table class='table table-bordered'>
    @include('home.partials.horario.cabeceraTabla')
    <tbody>
    @foreach (Intranet\Entities\Hora::all() as $hora)
    <tr>
        <td>{{$hora->hora_ini}} - {{$hora->hora_fin}}</td>
        @foreach (array('L','M','X','J','V') as $dia_semana)
            @if (isset($horario[$dia_semana][$hora->codigo]))
                @if (isset($horario[$dia_semana][$hora->codigo]->modulo))
                <td class='active'><strong>{{ $horario[$dia_semana][$hora->codigo]->Modulo->cliteral }}</strong> (
                        {{ $horario[$dia_semana][$hora->codigo]->aula }})<br/>
                        @if (isset($horario[$dia_semana][$hora->codigo]->Mestre->Sustituye->dni))
                            {{ $horario[$dia_semana][$hora->codigo]->Mestre->Sustituye->FullName }}
                        @else    
                            {{ $horario[$dia_semana][$hora->codigo]->Mestre->FullName }}
                        @endif    
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