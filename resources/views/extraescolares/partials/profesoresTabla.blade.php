<div class="gruposContainer col-lg-8 col-md-6 col-sm-10 col-xs-10 col-lg-offset-2 col-md-offset-2 col-sm-offset-1">
    <h4 class="centrado" >@lang("models.Actividad.profesores")</h4><br>
    <table class="table table-striped table-condensed" >
        <tr><th>DNI</th> <th>@lang("validation.attributes.name")</th><th>@lang("validation.attributes.cargo")</th><th>@lang("validation.attributes.operaciones")</th></tr>
        @foreach ($sProfesores as $profesor)
        <tr class="lineaProfesor">
            <td>{{ $profesor->dni }}</td>
            <td>{{ $profesor->apellido1 }} {{ $profesor->apellido2 }},{{ $profesor->nombre }}</td>
            <td>@if ($profesor->coordinador) <strong>Coordinador</strong> @endif</td>
            <td>
                <form method="POST" action="{{ route('actividad.profesor.destroy', ['actividad' => $Actividad->id, 'profesor' => $profesor->dni]) }}" style="display:inline-block;margin:0 4px 0 0;vertical-align:middle;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="delGrupo" style="border:0;background:transparent;padding:0;line-height:1;display:inline-block;vertical-align:middle;" title="@lang('messages.buttons.delete')" onclick="return confirm('Segur que vols eliminar este professor de la activitat?');">
                        {!! Html::image('img/delete.png',trans("messages.buttons.delete"),array('class' => 'iconopequeno')) !!}
                    </button>
                </form>
                @if (!$profesor->coordinador)
                    <form method="POST" action="{{ route('actividad.profesor.coordinador', ['actividad' => $Actividad->id, 'profesor' => $profesor->dni]) }}" style="display:inline-block;margin:0;vertical-align:middle;">
                        @csrf
                        <button type="submit" class="delGrupo" style="border:0;background:transparent;padding:0;line-height:1;display:inline-block;vertical-align:middle;" title="@lang('messages.buttons.coordinador')" onclick="return confirm('Segur que vols assignar este professor com a coordinador?');">
                            {!! Html::image('img/coordinador.png',trans("messages.buttons.coordinador"),array('class' => 'iconopequeno')) !!}
                        </button>
                    </form>
                @endif
            </td>
        </tr>
        @endforeach
    </table>
</div>
<div class="gruposContainer col-lg-8 col-md-6 col-sm-10 col-xs-10 col-lg-offset-2 col-md-offset-2 col-sm-offset-1">
    <p><strong>Atenció! Els professors participants en les extraescolars no han de tindre classe amb altres grups durant les hores en què es realitze l'activitat</strong></p>
    <form method="POST" class="agua" action="{{ route('actividad.profesor.store', ['actividad' => $Actividad->id]) }}">
        {{ csrf_field() }}
        <input type='hidden' name='idActividad' value="{{ $Actividad->id }}">
        {{ Form::select('idProfesor',$tProfesores,0,['id'=>'idProfesor']) }}
        <input id="submit" class="boton" type="submit" value="@lang("messages.generic.anadir") @lang("models.modelos.Profesor") ">
    </form>
</div>
