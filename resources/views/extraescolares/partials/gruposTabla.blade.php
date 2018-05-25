
<div class="gruposContainer col-lg-8 col-md-6 col-sm-10 col-xs-10 col-lg-offset-2 col-md-offset-2 col-sm-offset-1">
    <h4 class="centrado" >@lang("models.Actividad.grupos")</h4><br>
    <table class="table table-striped table-condensed">
        <tr><th>@lang("validation.attributes.codigo")</th> <th>@lang("validation.attributes.name")</th> <th>@lang("validation.attributes.operaciones")</th></tr>
        @foreach ($sGrupos as $grupo)
        <tr class="lineaGrupo">
            <td>{!! $grupo->codigo !!}</td> 
            <td>{!! $grupo->nombre !!}</td> 
            <td><a href="/actividad/{!!$Actividad->id!!}/borrarGrupo/{!! $grupo->codigo !!}" class="delGrupo">{!! Html::image('img/delete.png',trans("messages.buttons.delete"),array('class' => 'iconopequeno','title'=>trans("messages.buttons.delete"))) !!}</a></td>
        </tr>
        @endforeach
    </table>
</div>
<div class="gruposContainer col-lg-8 col-md-6 col-sm-10 col-xs-10 col-lg-offset-2 col-md-offset-2 col-sm-offset-1">
    <form method="POST" class="agua" action="/actividad/{!!$Actividad->id!!}/nuevoGrupo">
        {{ csrf_field() }}
        <input type='hidden' name='idActividad' value="{!!$Actividad->id!!}">
        {{ Form::select('idGrupo',$tGrupos,0,['id'=>'idGrupo']) }}
        <input id="submit" class="boton" type="submit" value="@lang("messages.generic.anadir") @lang("models.modelos.Grupo") ">
     </form>
</div>
