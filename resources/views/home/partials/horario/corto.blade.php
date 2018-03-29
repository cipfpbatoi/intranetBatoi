<!-- El siguiente enlace sólo se descomenta el tiempo que los profesores puedan hacer cambios de horario -->
<!--<a href="/profesor/{{ AuthUser()->dni }}/horario-cambiar" class="btn-success btn btn-xs iconButton"><i class="fa fa-table"></i>Canviar horari</a>-->
<table class='table table-bordered'>
    @include('home.partials.horario.cabeceraTabla')
    <tbody>
    @foreach (Intranet\Entities\Hora::all() as $hora)
    <tr>
        <td>{{$hora->hora_ini}} - {{$hora->hora_fin}}</td>
        @foreach (array('L','M','X','J','V') as $dia_semana)
            @if (isset($horario[$dia_semana][$hora->codigo]))
                @if (isset($horario[$dia_semana][$hora->codigo]->modulo))
                <td class='active'><strong>{{ $horario[$dia_semana][$hora->codigo]->Modulo->cliteral }}</strong>
                        ({{ $horario[$dia_semana][$hora->codigo]->aula }})
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