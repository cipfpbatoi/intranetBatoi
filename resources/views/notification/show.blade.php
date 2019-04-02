@extends('layouts.intranet')
@section('content')
        <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="x_panel">
                        <div class="x_content">
                                <div class="">
                                        <ul class="to_do">
                                                @if ($elemento->enlace != '#')
                                                        <li><p><a href="{{$elemento->enlace}}" >Anar</a></p></li>
                                                @endif
                                                <li><p> {{ trans("validation.attributes.idProfesor") }} : {{ $elemento->emissor }}</p></li>
                                                <li><p> {{ trans("validation.attributes.motivo") }} : {{ $elemento->motiu }}</p></li>
                                                <li><p> {{ trans("validation.attributes.fecha") }} : {{ $elemento->data }}</p></li>
                                        </ul>
                                </div>
                        </div>
                </div>
        </div>
@endsection
@section('titulo')
        {{trans("models.$modelo.show")}} {{$elemento->getKey()}}
@endsection

