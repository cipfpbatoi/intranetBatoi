    <div class="col-md-4 col-sm-4 col-xs-12 profile_details" >
        @if ($fct->inTime)
            <div id="{{$elemento->id}}" class="well profile_view">
        @else
            <div id="{{$elemento->id}}" class="well profile_view" style="background-color: lightcyan">
        @endif
            <div id="{{$fct->id}}" class="col-sm-12 fct">
                <div class="left col-md-8 col-xs-12">
                    <h5>FCT {{$elemento->Centro->nombre}} <strong>({{$elemento->puestos}})</strong></h5>
                    <ul class="list-unstyled">
                        @if ($fct->Instructor)
                            <li>{{$fct->Instructor->nombre}}</li>
                            <li>{{$fct->Instructor->telefono}}</li>
                            <li>{{$fct->Instructor->email}}</li>
                        @else
                            <li>No hi ha instructor. Cal corregir el problema</li>
                        @endif
                        <li class="nombre">{{isset($elemento->propietario->fullName)?$elemento->propietario->fullName:$elemento->tutor}}
                        </li>
                    </ul>
                </div>
                <div class="col-md-4 listActivity">
                    @foreach ($contactos as $contacto)
                        <small>

                            {{fechaCurta($contacto->created_at)}}
                            @if (firstWord($contacto->document)=='Recordatori')
                                <i class="fa fa-flag"></i>
                            @elseif (firstWord($contacto->document)=='Informació')
                                <i class="fa fa-lock"></i>
                                @else
                                    <a href="#" class="small" id="{{$contacto->id}}">
                                        @if ($contacto->action == 'email') <i class="fa fa-envelope"></i> @endif
                                        @if ($contacto->action == 'visita') <i class="fa fa-car"></i> @endif
                                        @if ($contacto->action == 'phone') <i class="fa fa-phone"></i> @endif
                                        @if (isset($contacto->comentari))  <i class="fa fa-plus"></i> @endif
                                    </a>
                            @endif
                        </small>
                        <br/>
                    @endforeach
                </div>
            </div>
            <div class="col-xs-12 bottom text-center">
                <div class="col-xs-12 col-sm-5 emphasis">
                    <p class="ratings">
                        {{$elemento->Centro->localidad}}<br/>
                    </p>
                    <a href="/colaboracion/{{$elemento->id}}/show" class="btn-success btn btn-xs"><i class="fa fa-eye"></i>

                    </a>
                    @if (count($alumnos))
                        <i class="btn-success btn btn-xs">{{count($alumnos)}}</i>
                    @else
                        <a href="/fct/{{$fct->id}}/delete" class="btn-success btn btn-xs"><i class="fa fa-trash"></i></a>
                    @endif
                    <i class="fa fa-plus btn-success btn btn-xs" data-toggle="modal" data-target="#AddAlumno"></i>

                </div>
                <div class="col-xs-12 col-sm-7 emphasis">
                    @include ('intranet.partials.components.buttons',['tipo' => 'profile'])<br/>
                    @php $elemento = $fct; @endphp
                    @if ($fct->inTime)
                        @include ('intranet.partials.components.buttons',['tipo' => 'fct'])
                    @endif
                </div>
            </div>
        </div>
    </div>