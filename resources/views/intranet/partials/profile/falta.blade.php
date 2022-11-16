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
                                    <h4 class="brief">
                                        @if ($elemento->baja)
                                            <i class="fa fa-calendar"></i> {{$elemento->desde}}
                                        @else
                                            @if (esMismoDia($elemento->desde,$elemento->hasta))
                                                <i class="fa fa-calendar"></i> {{$elemento->desde}}
                                                @if (!$elemento->dia_completo)
                                                    {{$elemento->hora_ini}} - {{$elemento->hora_fin}}
                                                @endif
                                            @else
                                                <i class="fa fa-calendar"></i> {{$elemento->desde}} - <i
                                                        class="fa fa-calendar"></i> {{$elemento->hasta}}
                                            @endif
                                        @endif
                                    </h4>
                                    <h6>{{$elemento->Profesor->nombre}} {{$elemento->Profesor->apellido1}}</h6>
                                    @if (isset(Intranet\Entities\Falta_profesor::haFichado(fecha($elemento->desde),$elemento->idProfesor)->first()->entrada))
                                        <h6><i class="fa fa-clock-o"></i>
                                            {{ Intranet\Entities\Falta_profesor::haFichado(fecha($elemento->desde),$elemento->idProfesor)->first()->entrada}}
                                            - {{ Intranet\Entities\Falta_profesor::haFichado(fecha($elemento->desde),$elemento->idProfesor)->latest()->first()->salida}}
                                        </h6>
                                    @else
                                        <h6><i class="fa fa-clock-o"></i> No ha fixat</h6>
                                    @endif
                                    <div class="left col-xs-12">
                                        <h5>@if ($elemento->baja)
                                                <a href="#"><span class="fa fa-star"></span></a>
                                            @endif
                                            {{ $elemento->motivo }} </h5>
                                        <p> {{$elemento->observaciones}}</p>
                                    </div>
                                </div>
                                <div class="col-xs-12 bottom text-center">
                                    <div class="col-xs-12 col-sm-12 emphasis">
                                        @if ($elemento->estado < 4)
                                            @foreach (Intranet\Entities\Horario::Profesor($elemento->idProfesor)->Dia(NameDay($elemento->desde))->orderBy('sesion_orden')->get() as $hora)
                                                <a href='#' class='btn btn-primary btn-xs'>
                                                    <i class="fa fa-clock-o"></i>{{$hora->Hora->hora_ini}}
                                                    -{{$hora->Hora->hora_fin}}
                                                    @if (isset($hora->Modulo->cliteral))
                                                        {{$hora->Modulo->literal}}
                                                    @else
                                                        {{$hora->Ocupacion->nombre}}
                                                    @endif
                                                </a>
                                            @endforeach
                                        @endif
                                        @include ('intranet.partials.components.buttons',['tipo' => 'profile'])
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endforeach