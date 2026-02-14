<div class="gruposContainer col-lg-8 col-md-6 col-sm-10 col-xs-10 col-lg-offset-2 col-md-offset-2 col-sm-offset-1">
    <h4 class="centrado" >@lang("models.Actividad.grupos")</h4><br>
    <table class="table table-striped table-condensed">
        <tr><th>@lang("validation.attributes.codigo")</th> <th>@lang("validation.attributes.name")</th> <th>@lang("validation.attributes.operaciones")</th></tr>
        @foreach ($sGrupos as $grupo)
        <tr class="lineaGrupo">
            <td>{{ $grupo->codigo }}</td>
            <td>{{ $grupo->nombre }}</td>
            <td>
                <form method="POST" action="{{ route('actividad.grupo.destroy', ['actividad' => $Actividad->id, 'grupo' => $grupo->codigo]) }}" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="delGrupo" style="border:0;background:transparent;padding:0;" title="@lang('messages.buttons.delete')" onclick="return confirm('Segur que vols eliminar este grup de la activitat?');">
                        {!! Html::image('img/delete.png',trans("messages.buttons.delete"),array('class' => 'iconopequeno')) !!}
                    </button>
                </form>
            </td>
        </tr>
        @endforeach
    </table>
</div>
<div class="gruposContainer col-lg-8 col-md-6 col-sm-10 col-xs-10 col-lg-offset-2 col-md-offset-2 col-sm-offset-1">
    <form method="POST" class="agua" action="{{ route('actividad.grupo.store', ['actividad' => $Actividad->id]) }}">
        {{ csrf_field() }}
        <input type='hidden' name='idActividad' value="{{ $Actividad->id }}">
        {{ Form::select('idGrupo',$tGrupos,0,['id'=>'idGrupo']) }}
        <input id="submit" class="boton" type="submit" value="@lang("messages.generic.anadir") @lang("models.modelos.Grupo") ">
        <a href="{{ route('actividad.index') }}" class="btn btn-primary btn-round">@lang("messages.buttons.atras")</a>
     </form>
</div>
