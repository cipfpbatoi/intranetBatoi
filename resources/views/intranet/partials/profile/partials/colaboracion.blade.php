    <div class="col-md-4 col-sm-4 col-xs-12 profile_details">
        <div id="{{$elemento->id}}" class="well profile_view">
            <div class="col-sm-12">
                <div class="left col-md-8 col-xs-12">
                    <h5>Col.laboracio {{$elemento->Centro->nombre}} <strong>({{$elemento->puestos}})</strong></h5>
                    <ul class="list-unstyled">
                        <li>{{$elemento->contacto}}</li>
                        <li>{{$elemento->telefono}}</li>
                        <li>{{$elemento->email}}</li>
                        @if (isset($colaboraciones))
                            @foreach ($colaboraciones as $colaboracion)
                                @if ($colaboracion->Propietario)
                                    <li class="nombre" style="background-color:@if ($colaboracion->estado == 2) lightblue @endif @if ($colaboracion->estado == 3) coral @endif ">{{$colaboracion->Xciclo}} - {{ $colaboracion->Propietario->shortName }}</li>
                                @endif
                            @endforeach
                        @endif
                    </ul>

                </div>
                <div class="col-md-4 listActivity">
                    @foreach ($contactos as $contacto)
                        <small>
                            {{fechaCurta($contacto->created_at)}}
                            @include('intranet.partials.profile.partials.icono')
                        </small>
                        <br/>
                    @endforeach
                </div>
            </div>
            <div class="col-xs-12 bottom text-center">
                <div class="col-xs-12 col-sm-4 emphasis">
                    <p class="ratings">
                        {{$elemento->localidad}}<br/>
                    </p>
                    @if ($elemento->estado < 3)
                    <a href="/colaboracion/{{$elemento->id}}/show" class="btn-success btn btn-xs"><i class="fa fa-eye"></i></a>
                    @endif
                    @if ($elemento->estado == 2)
                    <i class="fa fa-plus btn-success btn btn-xs" data-toggle="modal" data-target="#AddAlumno"></i>
                    @endif
                </div>
                <div class="col-xs-12 col-sm-8 emphasis">
                    @include ('intranet.partials.components.buttons',['tipo' => 'profile'])<br/>
                    @include ('intranet.partials.components.buttons',['tipo' => 'nofct'])
                </div>
            </div>
        </div>
    </div>