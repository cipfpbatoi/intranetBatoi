<div id="{{$Gt->id}}"class="gruposContainer col-lg-8 col-md-6 col-sm-10 col-xs-10 col-lg-offset-2 col-md-offset-2 col-sm-offset-1">
    <h4 class="centrado" >@lang("models.Actividad.profesores")</h4><br>
    <table class="table table-striped table-condensed">
        <tr><th>DNI</th> <th>@lang("validation.attributes.name")</th><th>@lang("validation.attributes.cargo")</th><th>@lang("validation.attributes.operaciones")</th></tr>
        @foreach ($sProfesores as $profesor)
        <tr class="lineaProfesor">
            <td>{!! $profesor->dni !!}</td> 
            <td>{!! $profesor->apellido1 !!} {!! $profesor->apellido2 !!},{!! $profesor->nombre !!}</td>
            <td>@if ($profesor->coordinador) <strong>Coordinador</strong> @endif</td>
            <td>
                <a href="/grupotrabajo/{!!$Gt->id!!}/borrarProfesor/{!! $profesor->dni !!}" >{!! Html::image('img/delete.png',trans("messages.buttons.delete"),array('class' => 'iconopequeno','title'=>trans("messages.buttons.delete"))) !!}</a>
                @if (!$profesor->coordinador)
                    <a href="/grupotrabajo/{!!$Gt->id!!}/coordinador/{!! $profesor->dni !!}" >{!! Html::image('img/coordinador.png',trans("messages.buttons.coordinador"),array('class' => 'iconopequeno','title'=>trans("messages.buttons.coordinador"))) !!}</a>
                @endif
            </td>
        </tr>
        @endforeach
    </table>
</div>
<div class="gruposContainer col-lg-8 col-md-6 col-sm-10 col-xs-10 col-lg-offset-2 col-md-offset-2 col-sm-offset-1">
    <form method="POST" class="agua" action="/grupotrabajo/{!!$Gt->id!!}/nuevoProfesor">
        {{ csrf_field() }}
        <input type='hidden' name='idGrupoTrabajo' value="{!!$Gt->id!!}">
        {{ Form::select('idProfesor',$tProfesores,0,['id'=>'idProfesor']) }}
        <input id="submit" class="boton" type="submit" value="@lang("messages.generic.anadir") @lang("models.modelos.Profesor") ">
    </form>
    <br/>
    <div class="error">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</div>


