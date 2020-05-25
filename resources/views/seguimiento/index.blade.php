@extends('layouts.intranet')
@section('css')
    <title> {{ $elemento->literal }}</title>
@endsection
@section('content')
    <div class="panel">
        <table class="table table-striped table-condensed">
            <tr>
                <td>@lang('validation.attributes.adquiridosNO')</td>
                <td>{!! $elemento->adquiridosNO !!}</td>
            </tr>
        </table>
    </div>
    <div class="panel">
        <div class="panel-body">
            <table class="table table-striped table-condensed" name='alumnoresultado'>
                <tr><th style="width: 20%">@lang("validation.attributes.Alumno")</th><th style="width: 8%">@lang("validation.attributes.nota")</th> <th style="width: 25%">@lang("validation.attributes.valoraciones")</th><th style="width: 30%">@lang("validation.attributes.observaciones")</th><th style="width: 17%">@lang("validation.attributes.operaciones")</th></tr>
                @foreach ($resultados as $orden)
                    <tr class="lineaGrupo" id='{{ $orden->id }}'>
                        <td><span class='none' name='nombre'>{!! $orden->nombre !!}</span></td>
                        <td><span class='select'  name='nota'>{!! $orden->notaString !!}</span></td>
                        <td><span class='select' name='valoraciones'>{!! $orden->valoracion !!}</span></td>
                        <td><span class='input' name='observaciones' maxlength="200">{{ $orden->observaciones }}</td>
                        <td><span class='botones'>
                        <a href="#" class="editGrupo">{!! Html::image('img/edit.png',trans("messages.buttons.edit"),array('class' => 'iconopequeno','title'=>trans("messages.buttons.edit"))) !!}</a>
                        <a href="/alumnoresultado/{!! $orden->id !!}/delete" class="delGrupo">{!! Html::image('img/delete.png',trans("messages.buttons.delete"),array('class' => 'iconopequeno','title'=>trans("messages.buttons.delete"))) !!}</a>
                    </span>
                        </td>
                    </tr>
                @endforeach
                <form method="POST" class="agua" action="/alumnoresultado/{!!$elemento->id!!}/create">
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
                            <select name="valoraciones" class="form-control">
                                @foreach (config('auxiliares.valoraciones') as $key => $valoraciones)
                                    <option value="{{$key}}">{{$valoraciones}}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input name="observaciones" class="form-control" type="text" maxlength="200"/>
                            {{ $errors->first('observaciones','Longitut màxima de 200 caracters') }}
                        </td>
                        <td><input id="submit" class="boton" type="submit" value="@lang("messages.generic.anadir") @lang("models.modelos.AlumnoResultado") "></td>
                    </tr>
                </form>
            </table>
        </div>
    </div>

    <a href="/reunion" class="btn btn-success">@lang("messages.buttons.atras") </a>
@endsection
@section('titulo')
   {{ $elemento->literal }}
@endsection
@section('scripts')
    <script src="/js/tabledit.js"></script>
    <script src="/js/Seguimiento/index.js"></script>
@endsection