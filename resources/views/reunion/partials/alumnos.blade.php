<div class="card">
    <a class="d-block collapsed" id="headingFour" data-bs-toggle="collapse" data-bs-parent="#accordion" href="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
        <h4 class="card-title"><i class="fa fa-bars"></i> @lang("models.Reunion.alumnos")</h4>
    </a>
    <div id="collapseFour" class="collapse" aria-labelledby="headingFour">
        <div id="{{$formulario->getElemento()->id}}" class="card-body">
            <table class="table table-striped table-condensed">
                <tr><th>NIA</th> <th>@lang("validation.attributes.name")</th><th>@lang("validation.attributes.valoraciones")</th><th>@lang("validation.attributes.operaciones")</th></tr>
                @foreach ($sAlumnos as $alumno)
                <tr class="lineaAlumno">
                    <td>{!! $alumno->nia !!}</td>
                    <td>{!! $alumno->nameFull !!}</td>
                    <td>
                        <select name="valoraciones" class="valoraciones">
                            @foreach (config($select) as $index => $value)
                                @if ($alumno->pivot->capacitats === $index)
                                    <option value="{{ $index }}" selected>{{$value}}</option>
                                @else
                                    <option value="{{ $index }}" >{{$value}}</option>
                                @endif
                            @endforeach
                        </select>
                    </td>
                    <td><a href="{{ route('reunion.alumno.destroy', ['reunion' => $formulario->getElemento()->id, 'alumno' => $alumno->nia]) }}" class="delGrupo">{!! Html::image('img/delete.png',__("messages.buttons.delete"),array('class' => 'iconopequeno','title'=>__("messages.buttons.delete"))) !!}</a></td>
                </tr>
                @endforeach
            </table>
        </div>
        @if (count($tAlumnos))
            <div class="gruposContainer col-lg-8 col-md-6 col-sm-10 col-xs-10 col-lg-offset-2 col-md-offset-2 col-sm-offset-1">
                <form method="POST" class="agua" action="{{ route('reunion.alumno.store', ['reunion' => $formulario->getElemento()->id]) }}">
                    {{ csrf_field() }}
                    <input type='hidden' name='idReunion' value="{!!$formulario->getElemento()->id!!}">
                    {{ Form::select('idAlumno',$tAlumnos,0,['id'=>'idAlumno']) }}
                    {{ Form::select('capacitats',config($select),0) }}
                    <input id="submit" class="boton" type="submit" value="@lang("messages.generic.anadir") @lang("models.modelos.Alumno") ">
                </form>
            </div>
        @endif
    </div>
</div>
