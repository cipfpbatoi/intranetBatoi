@php $agrupados = $panel->getElementos($pestana)->groupBy('dia'); @endphp
@foreach ($agrupados as $grupo)
@php $nombre = $pestana->getNombre().$grupo->first()->dia; @endphp
<div class="panel">
    <a class="panel-heading"
       role="tab"
       id="heading{{$nombre}}"
       data-toggle="collapse"
       data-parent="#accordion"
       href="#collapse{{$nombre}}"
       aria-expanded="false"
       aria-controls="collapse{{$nombre}}">
            <h4 class="panel-title"><em class="fa fa-bars"></em> {{$grupo->first()->dia}}</h4>
    </a>
    <div id="collapse{{$nombre}}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading{{$nombre}}">
        <div class="panel-body">
            <div class='form_box'>
                @php $usuarios = $grupo->groupBy('idProfesor'); @endphp
                @foreach ($usuarios as $usuario)
                    @if ($usuario->first()->Profesor)
                        <x-label id="{{$usuario->first()->id}}"
                                 cab1="{{$usuario->first()->Profesor->FullName  }}"
                                 cab2="{{$usuario->first()->dia}}"
                                 title="{{asset('storage/fotos/'. $usuario->first()->Profesor->foto ) }}"
                                 view="people">
                            <div class="left col-xs-8">
                                <ul class="list-unstyled">
                                    @php $justificacion = ''; @endphp
                                        @foreach ($usuario as $elemento)
                                        @php $justificacion .= $elemento->justificacion; @endphp
                                        <li>
                                            @if ($elemento->enCentro)
                                                <img src="{{ asset('img/clock-icon.png') }}" alt="reloj" class="iconopequeno">
                                            @else
                                                <img src="{{ asset('img/clock-icon-rojo.png') }}" alt="reloj" class="iconopequeno" id="imgFitxar">
                                            @endif
                                            {{ $elemento->horas }} - {{ $elemento->Xgrupo }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <x-slot name="rattings">
                                <p class="ratings">
                                    {{ ucfirst($justificacion) }}
                                </p>
                            </x-slot>
                            <x-slot name="botones">
                                <x-botones :panel="$panel"  tipo="profile" :elemento="$elemento ?? null"/>
                            </x-slot>
                        </x-label>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</div>
@endforeach
