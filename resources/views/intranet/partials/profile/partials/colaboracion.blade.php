<div class="col-md-4 col-sm-4 col-xs-12 profile_details">
    <div id="{{$elemento->id}}" class="well profile_view">
        <div class="col-sm-12 ">
            <div class="left col-md-8 col-xs-12">
                <h5 class="bg-blue-sky">
                    @if ($elemento->Centro->Empresa->conveniNou)
                        <strong>{{$elemento->Centro->Empresa->concierto??''}}!!</strong>
                    @else
                        {{$elemento->Centro->Empresa->concierto??''}}.
                    @endif
                    COL {{$elemento->Centro->nombre}}
                </h5>
                <ul class="list-unstyled">
                    <li>Llocs: {{$elemento->puestos}}</li>
                    <li>{{$elemento->contacto}}</li>
                    <li>{{$elemento->telefono}}</li>
                    <li>{{$elemento->email}}</li>
                    @if (isset($colaboraciones))
                        @foreach ($colaboraciones as $colaboracion)
                            @if ($colaboracion->Propietario)
                                <li class="nombre"
                                    style="background-color:@if ($colaboracion->estado == 2) lightblue @endif
                                    @if ($colaboracion->estado == 3) coral @endif ">
                                    {{$colaboracion->Xciclo}} - {{ $colaboracion->Propietario->shortName }}
                                </li>
                            @endif
                        @endforeach
                    @endif
                </ul>

            </div>
            <div class="col-md-4 listActivity">
                @if (esRol(authUser()->rol,2))
                    @foreach ($contactos as $contacto)
                        <small>
                            {{fechaCurta($contacto->created_at)}}
                            {{ $contacto->render() }}
                        </small>
                        <br/>
                    @endforeach
                @endif
            </div>
        </div>
        <div class="col-xs-12 bottom text-center">
            <div class="col-xs-12 col-sm-4 emphasis">
                <p class="ratings">
                    {{$elemento->localidad}}<br/>
                </p>
                @if ($elemento->estado < 3)
                <a href="/colaboracion/{{$elemento->id}}/show" class="btn-success btn btn-xs">
                    <i class="fa fa-eye"></i>
                </a>
                @endif
                @if ($elemento->estado == 2)
                <em class="fa fa-plus btn-success btn btn-xs" data-toggle="modal" data-target="#AddAlumno"></em>
                @endif
            </div>
            <div class="col-xs-12 col-sm-8 emphasis">
                @include ('intranet.partials.components.buttons',['tipo' => 'profile'])<br/>
                @include ('intranet.partials.components.buttons',['tipo' => 'nofct'])
            </div>
        </div>
    </div>
</div>
