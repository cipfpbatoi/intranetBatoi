<div class="gruposContainer col-lg-8 col-md-6 col-sm-10 col-xs-10 col-lg-offset-2 col-md-offset-2 col-sm-offset-1">
    <h4 class="centrado" >@lang("models.Actividad.profesores")</h4><br>
    <table class="table table-striped table-condensed" >
        <tr><th>DNI</th> <th>@lang("validation.attributes.name")</th><th>@lang("validation.attributes.cargo")</th><th>@lang("validation.attributes.operaciones")</th></tr>
        @foreach ($sProfesores as $profesor)
        <tr class="lineaProfesor">
            <td>{!! $profesor->dni !!}</td> 
            <td>{!! $profesor->apellido1 !!} {!! $profesor->apellido2 !!},{!! $profesor->nombre !!}</td>
            <td>@if ($profesor->coordinador) <strong>Coordinador</strong> @endif</td>
            <td>
                <a href="/actividad/{!!$Actividad->id!!}/borrarProfesor/{!! $profesor->dni !!}" id="de_{!! $profesor->dni !!}"  >{!! Html::image('img/delete.png',trans("messages.buttons.delete"),array('class' => 'iconopequeno','title'=>trans("messages.buttons.delete"))) !!}</a>
                @if (!$profesor->coordinador)
                <a href="/actividad/{!!$Actividad->id!!}/coordinador/{!! $profesor->dni !!}" id="co_{!! $profesor->dni !!}" >{!! Html::image('img/coordinador.png',trans("messages.buttons.coordinador"),array('class' => 'iconopequeno','title'=>trans("messages.buttons.coordinador"))) !!}</a>
                @endif
            </td>
        </tr>
        @endforeach
    </table>
</div>
<div class="gruposContainer col-lg-8 col-md-6 col-sm-10 col-xs-10 col-lg-offset-2 col-md-offset-2 col-sm-offset-1">
    <p><strong>Atenció! Els professors participants en les extraescolars no han de tindre classe amb altres grups durant les hores en què es realitze l'activitat</strong></p>
    <form method="POST" class="agua" action="/actividad/{!!$Actividad->id!!}/nuevoProfesor">
        {{ csrf_field() }}
        <input type='hidden' name='idActividad' value="{!!$Actividad->id!!}">
        {{ Form::select('idProfesor',$tProfesores,0,['id'=>'idProfesor']) }}
        <input id="submit" class="boton" type="submit" value="@lang("messages.generic.anadir") @lang("models.modelos.Profesor") ">
    </form>
</div>
