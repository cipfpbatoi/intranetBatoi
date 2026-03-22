<x-layouts.app  :title="$elemento->literal">
     <div class="panel">
        <div class="panel-body">
            <table class="table table-striped table-condensed" name='alumnoresultado'>
                <tr>
                    <th style="width: 20%">@lang("validation.attributes.Alumno")</th>
                    <th style="width: 15%">@lang("validation.attributes.nota")</th>
                    <th style="width: 55%">@lang("validation.attributes.observaciones")</th>
                    <th style="width: 10%">@lang("validation.attributes.operaciones")</th>
                </tr>
                @foreach ($resultados as $orden)
                    <tr class="lineaGrupo" id='{{ $orden->id }}'>
                        <td><span class='none' name='nombre'>{!! $orden->nombre !!}</span></td>
                        <td><span class='select' name='nota'>{{ config('auxiliares.notas')[$orden->nota] ?? '' }}</span></td>
                        <td><span class='textarea' name='observaciones'>{{ $orden->observaciones }}</td>
                        <td><span class='botones'>
                        <a href="#" class="editGrupo">{!! Html::image('img/edit.png',__("messages.buttons.edit"),array('class' => 'iconopequeno','title'=>__("messages.buttons.edit"))) !!}</a>
                        <a href="{{ route('seguimiento.alumnos.delete', ['seguimiento' => $orden->id]) }}" class="delGrupo">{!! Html::image('img/delete.png',__("messages.buttons.delete"),array('class' => 'iconopequeno','title'=>__("messages.buttons.delete"))) !!}</a>
                    </span>
                        </td>
                    </tr>
                @endforeach
                <div id='error' style='display: block' class="alert alert-danger"><span></span></div>
                <form method="POST" class="agua" action="{{ route('seguimiento.alumno.store', ['moduloGrupo' => $elemento->id]) }}">
                    {{ csrf_field() }}
                    <input type='hidden' name='idModuloGrupo' value="{!!$elemento->id!!}">
                    <tr>
                        <td>
                            <select name="idAlumno" class="form-control">
                              @foreach ($alumnes as $nia => $name)
                                  <option value="{{$nia}}">{{$name}}</option>
                              @endforeach
                            </select>
                        </td>
                        <td>
                            <select name="nota" class="form-control">
                                @foreach (config('auxiliares.notas') as $key => $nota)
                                    <option value="{{$key}}">{{$nota}}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <textarea name="observaciones" class="form-control" type="text" maxlength="200"></textarea>
                            {{ $errors->first('observaciones','Longitut màxima de 200 caracters') }}
                        </td>
                        <td><input id="submit" class="boton" type="submit" value="@lang("messages.generic.anadir") @lang("models.modelos.AlumnoResultado") "></td>
                    </tr>
                </form>
            </table>
        </div>
    </div>
     <a href="{{ route('resultado.index') }}" class="btn btn-success">@lang("messages.buttons.atras") </a>
@push('scripts')
    <script>
        window.seguimientoOptions = {
            nota: @json(config('auxiliares.notas'))
        };
    </script>
    <script src="/js/tabledit.js"></script>
    <script src="/js/Seguimiento/index.js"></script>
@endpush
</x-layouts.app>
