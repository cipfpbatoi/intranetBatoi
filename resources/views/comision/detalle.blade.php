@extends('layouts.intranet')
@section('css')
    <title></title>
@endsection
@section('content')
    <h4 class="centrado">{{__("models.modelos.Comision")}} de {{authUser()->ShortName}}
        de {{fechaString($comision->desde)}}</h4>


    <div class="gruposContainer col-lg-8 col-md-6 col-sm-10 col-xs-10 col-lg-offset-2 col-md-offset-2 col-sm-offset-1">
        <h4 class="centrado">Empreses amb visita confirmada</h4><br>
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
           class="btn btn-success">@lang("messages.buttons.acabar")</a>
    </div>
    <div class="col-lg-8 col-md-6 col-sm-10 col-xs-10 col-lg-offset-2 col-md-offset-2 col-sm-offset-1">
        <br/>
        <hr/>
        <br/>
        <h4 class="centrado">Afegir nova visita</h4><br>
        <table class="table table-striped table-condensed">
            <tr>
                <th>@lang("validation.attributes.name")</th>
                <th>@lang("validation.attributes.hora")</th>
                <th>@lang("validation.attributes.aviso")</th>
                <th></th>
            </tr>
            <form method="POST" class="agua" action="{{ route('comision.fct.create', ['comision' => $comision->id]) }}">
                @csrf
                <input type='hidden' name='idComision' value="{!!$comision->id!!}">
                <tr>
                    <td>{{ Form::select('idFct',$allFcts,0,['id' => 'idFct']) }}</td>
                    <td>{{ Form::time('hora_ini', hora($comision->desde), ['class' => 'form-control time']) }}</td>
                    <td>{{ Form::checkbox('aviso',1,true) }}</td>
                    <td>
                        <input id="submit" class="boton" type="submit" value="@lang("messages.generic.anadir") visita">
                    </td>
                </tr>
            </form>
        </table>
    </div>
@endsection
@section('scripts')
    {{ Html::script("/js/datepicker.js") }}
@endsection
