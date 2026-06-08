@extends('layouts.intranet')
@section('css')
    <title></title>
@endsection
@section('content')
    <h4 class="centrado">{{__("models.modelos.Comision")}} de {{authUser()->ShortName}}
        de {{fechaString($comision->desde)}}</h4>

    <style>
        .comision-box {
            border: 1px solid #e3e7eb;
            border-radius: 6px;
            background: #fafbfc;
            padding: 16px;
            margin-bottom: 20px;
        }

        .comision-box-title {
            margin-top: 0;
            margin-bottom: 4px;
        }

        .comision-box-subtitle {
            color: #6c757d;
            margin-bottom: 14px;
        }
    </style>

    <div class="gruposContainer col-lg-8 col-md-6 col-sm-10 col-xs-10 col-lg-offset-2 col-md-offset-2 col-sm-offset-1">
        <div class="comision-box">
            <h4 class="centrado comision-box-title">Visites actuals de la comissió</h4>
            <p class="centrado comision-box-subtitle">Estes visites ja estan associades a la comissió.</p>

            <table class="table table-striped table-condensed">
                <tr>
                    <th>@lang("validation.attributes.name")</th>
                    <th>@lang("validation.attributes.hora")</th>
                    <th>@lang("validation.attributes.aviso")</th>
                    <th>@lang("validation.attributes.operaciones")</th>
                </tr>
                @foreach ($comision->Fcts as $fct)
                    <tr class="lineaGrupo">
                        <td style="font-style: oblique;font-weight: bold">{{ $fct->Colaboracion->Centro->nombre }}</td>
                        <td style="font-style: oblique;font-weight: bold">{{ $fct->pivot->hora_ini }}</td>
                        <td style="font-style: oblique;font-weight: bold">{{ $fct->pivot->aviso ? 'Sí' : 'No' }}</td>
                        <td><a href="{{ route('comision.fct.delete', ['comision' => $comision->id, 'fct' => $fct->id]) }}"
                               class="delGrupo"
                               onclick="return confirm('Segur que vols eliminar esta visita?');">
                                {!! Html::image(
                                        'img/delete.png',
                                        __("messages.buttons.delete"),
                                        array('class' => 'iconopequeno','title'=>__("messages.buttons.delete"))
                                        )
                                !!}
                            </a>
                        </td>
                    </tr>
                @endforeach
            </table>

            <a href="{{ route('comision.confirm', ['comision' => $comision->id]) }}"
               class="btn btn-success">Continuar a confirmació</a>
        </div>
    </div>
    <div class="col-lg-8 col-md-6 col-sm-10 col-xs-10 col-lg-offset-2 col-md-offset-2 col-sm-offset-1">
        <div class="comision-box">
            <h4 class="centrado comision-box-title">Afegir nova visita</h4>
            <p class="centrado comision-box-subtitle">Selecciona empresa, hora i avís per afegir una visita.</p>

            <table class="table table-striped table-condensed">
                <tr>
                    <th>@lang("validation.attributes.name")</th>
                    <th>@lang("validation.attributes.hora")</th>
                    <th>@lang("validation.attributes.aviso")</th>
                    <th></th>
                </tr>
                @if (empty($allFcts))
                    <tr>
                        <td colspan="4" class="text-muted">No hi ha FCT disponibles per a afegir a esta comissió.</td>
                    </tr>
                @else
                    <form method="POST" class="agua" action="{{ route('comision.fct.create', ['comision' => $comision->id]) }}">
                        @csrf
                        <input type='hidden' name='idComision' value="{!!$comision->id!!}">
                        <tr>
                            <td>{{ Form::select('idFct',$allFcts,null,['id' => 'idFct', 'placeholder' => 'Selecciona una empresa']) }}</td>
                            <td>{{ Form::text('hora_ini',hora($comision->desde),['class' => 'time']) }}</td>
                            <td>{{ Form::checkbox('aviso',1,true) }}</td>
                            <td>
                                <input id="submit" class="boton" type="submit" value="@lang("messages.generic.anadir") visita">
                            </td>
                        </tr>
                    </form>
                @endif
            </table>
        </div>
    </div>
@endsection
@section('scripts')
    {{ Html::script("/js/datepicker.js") }}
@endsection
