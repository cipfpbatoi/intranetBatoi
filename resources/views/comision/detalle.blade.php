@extends('layouts.intranet')
@section('css')
    <title></title>
@endsection
@section('content')
    <h4 class="centrado">{{trans("models.modelos.Comision")}} de {{authUser()->ShortName}}
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
                    <td style="font-style: oblique;font-weight: bold">{!! $fct->Colaboracion->Centro->nombre !!}</td>
                    <td style="font-style: oblique;font-weight: bold">{!! $fct->pivot->hora_ini !!}</td>
                    <td style="font-style: oblique;font-weight: bold">@if ($fct->pivot->aviso)
                            Sí
                        @else
                            No
                        @endif</td>
                    <td><a href="/comision/{!!$comision->id!!}/deleteFct/{!! $fct->id !!}" class="delGrupo">
                            {!! Html::image(
                                    'img/delete.png',
                                    trans("messages.buttons.delete"),
                                    array('class' => 'iconopequeno','title'=>trans("messages.buttons.delete"))
                                    )
                            !!}
                        </a>
                    </td>
                </tr>
            @endforeach
        </table>
        <a href="/comision/{!!$comision->id!!}/confirm" class="btn btn-success">@lang("messages.buttons.acabar") </a>
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
            <form method="POST" class="agua" action="/comision/{!!$comision->id!!}/createFct">
                {{ csrf_field() }}
                <input type='hidden' name='idComision' value="{!!$comision->id!!}">
                <tr>
                    <td>{{ Form::select('idFct',$allFcts,0,['id'=>'idGrupo']) }}</td>
                    <td>{{ Form::text('hora_ini',hora($comision->desde),['class'=>'time']) }}</td>
                    <td>{{ Form::checkbox('aviso',null,1) }}</td>
                    <td>
                        <input id="submit" class="boton" type="submit" value="@lang("messages.generic.anadir") visita ">
                    </td>
                </tr>
            </form>
        </table>
    </div>
@endsection
@section('scripts')
    {{ Html::script("/js/datepicker.js") }}
@endsection
