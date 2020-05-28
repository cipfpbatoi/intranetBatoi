<div class="panel">
    <a class="panel-heading collapsed" role="tab" id="headingFour" data-toggle="collapse" data-parent="#accordion" href="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
        <h4 class="panel-title"><i class="fa fa-bars"></i> @lang("models.Reunion.alumnos")</h4>
    </a>
    <div id="collapseFour" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingFour">
        <div id="{{$elemento->id}}" class="panel-body">
            <table class="table table-striped table-condensed">
                <tr><th>NIA</th> <th>@lang("validation.attributes.name")</th><th>@lang("validation.attributes.valoraciones")</th><th>@lang("validation.attributes.operaciones")</th></tr>
                @foreach ($sAlumnos as $alumno)
                <tr class="lineaAlumno">
                    <td>{!! $alumno->nia !!}</td>
                    <td>{!! $alumno->nameFull !!}</td>
                    <td>
                        <select name="valoraciones" class="valoraciones">
                            @foreach (config('auxiliares.promociona') as $index => $value)
                                @if ($alumno->pivot->capacitats === $index)
                                    <option value="{{ $index }}" selected>{{$value}}</option>
                                @else
                                    <option value="{{ $index }}" >{{$value}}</option>
                                @endif
                            @endforeach
                        </select>
                    </td>
                    <td><a href="/reunion/{!!$elemento->id!!}/borrarAlumno/{!! $alumno->nia !!}" class="delGrupo">{!! Html::image('img/delete.png',trans("messages.buttons.delete"),array('class' => 'iconopequeno','title'=>trans("messages.buttons.delete"))) !!}</a></td>
                </tr>
                @endforeach
            </table>
        </div>
        <div class="gruposContainer col-lg-8 col-md-6 col-sm-10 col-xs-10 col-lg-offset-2 col-md-offset-2 col-sm-offset-1">
            <form method="POST" class="agua" action="/reunion/{!!$elemento->id!!}/nuevoAlumno">
                {{ csrf_field() }}
                <input type='hidden' name='idReunion' value="{!!$elemento->id!!}">
                {{ Form::select('idAlumno',$tAlumnos,0,['id'=>'idAlumno']) }}
                {{ Form::select('capacitats',config('auxiliares.promociona'),0) }}
                <input id="submit" class="boton" type="submit" value="@lang("messages.generic.anadir") @lang("models.modelos.Alumno") ">
            </form>
        </div>
    </div>
</div>
