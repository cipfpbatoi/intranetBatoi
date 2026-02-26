@extends('layouts.intranet')
@section('css')
    <title></title>
@endsection
@section('content')
    <h4 class="centrado">Autoritzats per a l'activitat {{ $actividad->name }} de {{fechaString($actividad->desde)}}</h4>
    <div class="gruposContainer col-lg-8 col-md-6 col-sm-10 col-xs-10 col-lg-offset-2 col-md-offset-2 col-sm-offset-1">
        <h4 class="centrado">Alumnes Menor</h4><br>
        <table class="table table-striped table-condensed">
            <tr>
                <th>@lang("validation.attributes.name")</th>
                <th>@lang("validation.attributes.operaciones")</th>
            </tr>
            @foreach ($actividad->menores as $menor)
                <tr class="lineaGrupo">
                    <td>{!! $menor->Fullname !!}</td>
                    <td>
                        @if (!$menor->pivot->autorizado)
                            <a href="{{ route('actividad.autorizacion', ['nia' => $menor->nia, 'id' => $actividad->id]) }}"
                               class="delGrupo">{!! Html::image('img/unauthorized.png',trans("messages.buttons.autorizacion"),array('class' => 'iconopequeno','title'=>trans("messages.buttons.authorize"))) !!}</a>
                        @else
                            <a href="{{ route('actividad.autorizacion', ['nia' => $menor->nia, 'id' => $actividad->id]) }}"
                               class="delGrupo">{!! Html::image('img/check.jpeg',trans("messages.buttons.unauthorize"),array('class' => 'iconopequeno','title'=>trans("messages.buttons.unauthorize"))) !!}</a>
                        @endif
                    </td>
                </tr>
            @endforeach
        </table>
        <a href="{{ route('actividad.index') }}" class="btn btn-success">@lang("messages.buttons.atras") </a>
    </div>
@endsection
