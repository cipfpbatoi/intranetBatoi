@php $agrupados = $panel->getElementos($pestana)->groupBy('desde'); @endphp
@foreach ($agrupados as $grupo)
    @php $nombre = $pestana->getNombre().$grupo->first()->desde; @endphp
    <div class="panel">
        <a class="panel-heading" role="tab" id="heading{{$nombre}}" data-toggle="collapse" data-parent="#accordion"
           href="#collapse{{$nombre}}" aria-expanded="false" aria-controls="collapse{{$nombre}}">
            <h4 class="panel-title"><em class="fa fa-bars"></em> {{$grupo->first()->desde}}</h4>
        </a>
        <div id="collapse{{$nombre}}" class="panel-collapse collapse" role="tabpanel"
             aria-labelledby="heading{{$nombre}}">
            <div class="panel-body">
                <div class='form_box'>
                    @foreach ($grupo as $elemento)
                        @php
                           $clock = ($elemento->Profesor->entrada != ' ') ?
                                    $elemento->Profesor->entrada.' - '.$elemento->Profesor->salida:
                                    'No ha fixat';
                        @endphp
                        <x-label
                                id="{{$elemento->id}}"
                                cab1="{{$elemento->desdeHora}}"
                                cab2="{{$elemento->horaini??$elemento->hasta??''}}"
                                title="{{$elemento->Profesor->shortName}}"
                                view="date"
                                inside="{{$clock}}"
                                subtitle="{{$elemento->motivo.' '.$elemento->observaciones}}" >
                            <x-slot name="rattings">
                                @if ($elemento->estado < 4)
                                    @foreach (Intranet\Entities\Horario::Profesor($elemento->idProfesor)
                                            ->Dia(NameDay($elemento->desde))
                                            ->orderBy('sesion_orden')
                                            ->get() as $hora)
                                        <a href='#' class='btn btn-primary btn-xs'>
                                            <em class="fa fa-clock-o"></em>{{$hora->Hora->hora_ini}}
                                            -{{$hora->Hora->hora_fin}}
                                            @isset($hora->Modulo->cliteral)
                                                {{substr($hora->Modulo->literal,0,15)}}
                                            @else
                                                {{substr($hora->Ocupacion->nombre,0,15)}}
                                            @endisset
                                        </a>
                                    @endforeach
                                @endif
                            </x-slot>
                            <x-slot name="botones">
                                @foreach($panel->getBotones('profile') as $button)
                                    {{ $button->show($elemento) }}
                                @endforeach
                            </x-slot>
                        </x-label>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endforeach
