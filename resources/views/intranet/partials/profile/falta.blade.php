@php $agrupados = $panel->getElementos($pestana)->groupBy('desde'); @endphp

@foreach ($agrupados as $grupo)
    @php $nombre = $pestana->getNombre().$grupo->first()->desde; @endphp
    <div class="panel">
        <a class="panel-heading" role="tab" id="heading{{$nombre}}" data-toggle="collapse" data-parent="#accordion"
           href="#collapse{{$nombre}}" aria-expanded="false" aria-controls="collapse{{$nombre}}">
            <h4 class="panel-title"><i class="fa fa-bars"></i> {{$grupo->first()->desde}}</h4>
        </a>
        <div id="collapse{{$nombre}}" class="panel-collapse collapse" role="tabpanel"
             aria-labelledby="heading{{$nombre}}">
            <div class="panel-body">
                <div class='form_box'>
                    @foreach ($grupo as $elemento)
                        <div class="col-md-3 col-sm-3 col-xs-11 profile_details">
                            <div id="{{$elemento->id}}" class="well profile_view">
                                <div class="col-sm-12">
                                    <x-label id="{{$elemento->id}}"
                                             cab1="{{$elemento->desde}}"
                                             cab2="{{  esMismoDia($elemento->desde,$elemento->hasta)? substr($elemento->hasta,11): $elemento->hasta }}"
                                             title="{{$elemento->name}}">
                                        @if ($elemento->complementaria)
                                            <p>Complementària</p>
                                        @else
                                            <p>Extraescolar</p>
                                        @endif
                                        <p><strong>@if ($elemento->complementaria) Justificació RA @else Descripció @endif</strong> : <em style="font-size: smaller">{{$elemento->descripcion}}</em></p>
                                        @if ($elemento->objetivos)
                                            <p><strong>Objectius</strong> : <em style="font-size: smaller">{{$elemento->objetivos}}</em></p>
                                        @endif
                                        @if ($elemento->comentarios)
                                            <p><strong>Comentaris</strong> : <em style="font-size: smaller">{{$elemento->comentarios}}</em></p>
                                        @endif
                                        <h5>Participants</h5>
                                        <ul class="list-unstyled">
                                            @foreach ($elemento->profesores as $profesor)
                                                <li><em class="fa fa-user"></em>
                                                    @if($profesor->pivot->coordinador)
                                                        <strong>{{$profesor->nombre}} {{$profesor->apellido1}}</strong>
                                                    @else
                                                        {{$profesor->nombre}} {{$profesor->apellido1}}
                                                    @endif
                                                    @foreach (\Intranet\Services\AdviseTeacher::horariAltreGrup($elemento,$profesor->dni) as $grup)
                                                        <span class="label label-danger"><em style="font-size: smaller">{{$grup['idGrupo']}}</em></span>
                                                    @endforeach
                                                </li>
                                            @endforeach
                                            @foreach ($elemento->grupos as $grupo)
                                                <li><em class="fa fa-group"></em> {{ $grupo->nombre}} </li>
                                            @endforeach
                                        </ul>
                                        <x-slot name="rattings">
                                            @if ($elemento->estraescolar == 1)
                                                <a href='#' class='btn btn-success btn-xs' >@lang("messages.menu.Orientacion")</a>
                                            @else
                                                @if ($elemento->estado<2)
                                                    <a href='#' class='btn btn-danger btn-xs' >
                                                        @else
                                                            <a href='#' class='btn btn-success btn-xs' >
                                                                @endif
                                                                {{ $elemento->situacion }}</a>
                                                        @endif
                                                        @if ($elemento->fueraCentro)
                                                            <a href='#' class='btn btn-info btn-xs' >Extraescolar</a>
                                                        @else
                                                            <a href='#' class='btn btn-info btn-xs' >Centre</a>
                                                        @endif
                                                        @if ($elemento->transport)
                                                            <a href='#' class='btn btn-warning btn-xs' >Transport</a>
                                                @endif
                                        </x-slot>
                                        <x-slot name="botones">
                                            @foreach($panel->getBotones('profile') as $button)
                                                {{ $button->show($elemento) }}
                                            @endforeach
                                        </x-slot>
                                    </x-label>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endforeach
