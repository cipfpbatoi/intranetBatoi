@php $agrupados = $panel->getElementos($pestana)->groupBy('idProfesor'); @endphp
@foreach ($agrupados as $dni => $grupo)
    @php $nombre = $pestana->getNombre().$dni; @endphp
        <div class="panel">
            <a class="panel-heading" role="tab" id="heading{{$nombre}}" data-toggle="collapse" data-parent="#accordion"
               href="#collapse{{$nombre}}" aria-expanded="false" aria-controls="collapse{{$nombre}}">
                <h4 class="panel-title">
                    <i class="fa fa-bars"></i>
                    {{$grupo->first()->Profesor->fullName}}


                </h4>
            </a>
            @if ($grupo->first()->estado == '4')
                <em class="fa fa-money"></em> {{ $grupo->sum('total') }} €
                <input class='user' type="checkbox" name="{{$dni}}" value="{{$dni}}">
            @endif
            <div id="collapse{{$nombre}}" class="panel-collapse collapse" role="tabpanel"
                 aria-labelledby="heading{{$nombre}}">
                <div class="panel-body">
                    <div class='form_box'>
                        @foreach ($grupo as $elemento)
                            <x-label
                                id="{{$elemento->id}}"
                                cab1="{{$elemento->desde}}"
                                cab2="{{  esMismoDia($elemento->desde,$elemento->hasta)?
                                        substr($elemento->hasta,11):
                                        $elemento->hasta }}"
                                title="{{($elemento->fct)?'FCT':''}}
                                    {{$elemento->Profesor->nombre.' '.$elemento->Profesor->apellido1}}"
                                subtitle="{{$elemento->descripcion}}">
                                <ul>
                                    <li>
                                        <em class="fa fa-automobile"></em> {{$elemento->tipoVehiculo}} - {{$elemento->kilometraje}} km.
                                    </li>
                                    @isset($elemento->marca)
                                        <li>
                                            <em class="fa fa-automobile"></em> {{ $elemento->marca}} {{$elemento->matricula}}
                                        </li>
                                    @endisset
                                    <li>
                                        <em class="fa fa-money"></em> {{ $elemento->total }}
                                    </li>
                                </ul>
                                <x-slot name="rattings">
                                    <a href='#' class='btn {{$elemento->estado<2?'btn-danger':'btn-success'}} btn-xs'>
                                        {{ $elemento->situacion }}
                                    </a>
                                </x-slot>
                                <x-slot name="botones">
                                    @foreach($panel->getBotones('profile') as $button)
                                        {!! $button->show($elemento) !!}
                                    @endforeach
                                </x-slot>
                            </x-label>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
@endforeach
