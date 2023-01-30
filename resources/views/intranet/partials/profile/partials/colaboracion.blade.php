<div class="col-md-4 col-sm-4 col-xs-12 profile_details">
    <div id="{{$elemento->id}}" class="well profile_view"
         @if ($elemento->estado == 3) style='border-color: #90111a;border-width: medium' @endif
    >
        <div class="col-sm-12">
            <div class="left col-md-9 col-xs-12">
                <h5>
                     {{$elemento->Centro->nombre}}
                </h5>
                @isset (authUser()->emailItaca)
                    <ul class="list-unstyled">
                        <li>Conveni: <strong>
                                {{$elemento->Centro->Empresa->concierto}}
                                @if ($elemento->Centro->Empresa->conveniNou)
                                    <em class="fa fa-hand-o-up"></em>
                                @else
                                    <em class="fa fa-hand-o-down"></em>
                                @endif
                            </strong>
                        </li>
                        <li><em class="fa fa-group"></em> {{$elemento->puestos}} lloc(s) de treball</li>
                        <li><em class="fa fa-user"></em> {{$elemento->contacto}}</li>
                        <li><em class="fa fa-phone"></em> {{$elemento->telefono}}</li>
                        <li><em class="fa fa-envelope"></em> {{$elemento->email}}</li>
                        @if (isset($colaboraciones))
                            @foreach ($colaboraciones as $colaboracion)
                                @if ($colaboracion->Propietario)

                                    <li class="nombre"
                                        style="border:30px;
                                        background-color:@if ($colaboracion->estado == 2) lightblue @endif
                                        @if ($colaboracion->estado == 3) coral @endif"
                                    >
                                        <em class="fa fa-institution"></em>
                                        {{$colaboracion->Xciclo}} - {{ $colaboracion->Propietario->shortName }}
                                    </li>
                                @endif
                            @endforeach
                        @endif
                    </ul>
                @else
                    <ul class="list-unstyled">
                        <li><em class="fa fa-group"></em> {{$elemento->puestos}} lloc(s) de treball</li>
                        <li><em class="fa fa-clock-o"></em> {{$elemento->Centro->horarios}}</li>
                        <li><em class="fa fa-map-marker"></em> {{$elemento->Centro->direccion}}</li>
                        <li><em class="fa fa-folder"></em> {{$elemento->Centro->Empresa->actividad}}</li>
                        <li><em class="fa fa-envelope"></em> {{$elemento->Centro->Empresa->email}}</li>
                    </ul>
                @endisset
            </div>
            <div class="col-md-3 listActivity">
                @isset (authUser()->emailItaca)
                    @foreach ($contactos as $contacto)
                        <small>
                            {{ $contacto->render() }}
                        </small>
                        <br/>
                    @endforeach
                @endisset
            </div>
        </div>
        <div class="col-xs-12 bottom text-center">
            <div class="col-xs-12 col-sm-4 emphasis">
                <p class="ratings">
                    {{$elemento->localidad}}<br/>
                </p>
                @isset (authUser()->emailItaca)
                    @if ($elemento->estado < 3)
                    <a href="/colaboracion/{{$elemento->id}}/show" class="btn-success btn btn-xs">
                        <i class="fa fa-eye"></i>
                    </a>
                    @endif
                    @if ($elemento->estado == 2)
                    <em class="fa fa-plus btn-success btn btn-xs" data-toggle="modal" data-target="#AddAlumno"></em>
                    @endif
                @endisset
            </div>
            <div class="col-xs-12 col-sm-8 emphasis">
                @isset (authUser()->emailItaca)
                    @include ('intranet.partials.components.buttons',['tipo' => 'profile'])<br/>
                    @include ('intranet.partials.components.buttons',['tipo' => 'nofct'])
                @endisset
            </div>
        </div>
    </div>
</div>
