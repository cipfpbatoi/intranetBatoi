@php $agrupados = $panel->getElementos($pestana)->groupBy('dia'); @endphp
@foreach ($agrupados as $grupo)
@php $nombre = $pestana->getNombre().$grupo->first()->dia; @endphp
<div class="panel">
    <a class="panel-heading" role="tab" id="heading{{$nombre}}" data-toggle="collapse" data-parent="#accordion" href="#collapse{{$nombre}}" aria-expanded="false" aria-controls="collapse{{$nombre}}">
        <h4 class="panel-title"><i class="fa fa-bars"></i> {{$grupo->first()->dia}}</h4>
    </a>
    <div id="collapse{{$nombre}}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading{{$nombre}}">
        <div class="panel-body">
            <div class='form_box'>
                @php $usuarios = $grupo->groupBy('idProfesor'); @endphp
                @foreach ($usuarios as $usuario)
                <div class="col-md-4 col-sm-4 col-xs-12 profile_details">
                    <div id="{{$usuario->first()->id}}" class="well profile_view">
                        <div class="col-sm-12">
                            <h4 class="brief"><i>{{ $usuario->first()->Profesor->FullName }}</i></h4>
                            <div class="left col-xs-8">
                                <p><strong>{{ $usuario->first()->dia }}</strong></p>
                                <ul class="list-unstyled">
                                    @php $justificacion = ''; @endphp
                                    @foreach ($usuario as $elemento)
                                        @php $justificacion .= $elemento->justificacion; @endphp
                                    <li>
                                        @if ($elemento->enCentro) 
                                            {!! Html::image('img/clock-icon.png' ,'reloj',array('class' => 'iconopequeno')) !!}
                                        @else 
                                            {!! Html::image('img/clock-icon-rojo.png' ,'reloj',array('class' => 'iconopequeno', 'id' => 'imgFitxar')) !!}
                                        @endif
                                        {{ $elemento->horas }} - {{ $elemento->Xgrupo }}
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="right col-xs-4 text-center">
                                <img src="{{ asset($usuario->first()->Profesor->foto) }}" alt="" class="img-circle img-responsive">
                            </div>
                        </div>
                        <div class="col-xs-12 bottom text-center">
                            <div class="col-xs-12 col-sm-6 emphasis">
                                <p class="ratings">
                                    {{ $justificacion }}
                                </p>
                            </div>
                            <div class="col-xs-12 col-sm-6 emphasis">
                                @include ('intranet.partials.buttons',['tipo' => 'profile'])
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

