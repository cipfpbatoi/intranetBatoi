@extends('layouts.intranet')
@section('css')
    <title> {{ $elemento->literal }}</title>
@endsection
@section('content')
    <div class="panel">
        <table class="table table-striped table-condensed">
            <tr>
                <td>@lang('validation.attributes.adquiridosSI')</td>
                <td>{!! $elemento->adquiridosSI !!}</td>
            </tr>
            <tr>
                <td>@lang('validation.attributes.adquiridosNO')</td>
                <td>{!! $elemento->adquiridosNO !!}</td>
            </tr>
        </table>
    </div>
    <div class="panel">
        <div class="panel-body">
            <table class="table table-striped table-condensed" name='alumnoresultado'>
                <tr><th style="width: 25%">@lang("validation.attributes.Alumno")</th><th style="width: 5%">@lang("validation.attributes.nota")</th> <th style="width: 65%">@lang("validation.attributes.recomendaciones")</th><th style="width: 5%">@lang("validation.attributes.operaciones")</th></tr>
                @foreach ($resultados as $orden)
                    <tr class="lineaGrupo" id='{{ $orden->id }}'>
                        <td><span class='none' name='nombre'>{!! $orden->nombre !!}</span></td>
                        <td><span class='input'  name='nota'>{!! $orden->nota !!}</span></td>
                        <td><span class='textarea' name='recomendaciones'>{!! $orden->recomendaciones !!}</span></td>
                        <td><span class='botones'>
                        <a href="#" class="editGrupo">{!! Html::image('img/edit.png',trans("messages.buttons.edit"),array('class' => 'iconopequeno','title'=>trans("messages.buttons.edit"))) !!}</a>
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
                        {{--
                        <td>
                            <select name="nota" class="form-control">
                                @foreach (config('auxiliares.notas') as $key => $nota)
                                    <option value="{{$key}}">{{$nota}}</option>
                                @endforeach
                            </select>
                        </td>--}}
                        <td><input type='text' required name='nota' class="form-control"></td>
                        <td><textarea  rows="1"  name='recomendaciones'class="form-control" ></textarea></td>
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
@endsection