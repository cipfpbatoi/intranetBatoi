    <div class="col-md-4 col-sm-4 col-xs-12 profile_details">
        <div id="{{$elemento->id}}" class="well profile_view">
            <div id="{{$fct->id}}" class="col-sm-12 fct">
                <div class="left col-md-8 col-xs-12">
                    <h5>FCT {{$elemento->Centro->nombre}} <strong>({{$elemento->puestos}})</strong></h5>
                    <ul class="list-unstyled">
                        <li>{{$fct->Instructor->nombre}}</li>
                        <li>{{$fct->Instructor->telefono}}</li>
                        <li>{{$fct->Instructor->email}}</li>
                        <li class="nombre">{{isset($elemento->propietario->fullName)?$elemento->propietario->fullName:$elemento->tutor}}
                        </li>
                    </ul>
                </div>
                <div class="col-md-4 listActivity">
                    @foreach ($contactos as $contacto)
                        <small><a href="#" class="small" id="{{$contacto->id}}">{{firstWord($contacto->document)}}-{{fechaCurta($contacto->created_at)}}</a></small><br/>
                    @endforeach
                </div>
            </div>
            <div class="col-xs-12 bottom text-center">
                <div class="col-xs-12 col-sm-4 emphasis">
                    <p class="ratings">
                        {{$elemento->Centro->localidad}}<br/>
                    </p>
                    <a href="/colaboracion/{{$elemento->id}}/show" class="btn-success btn btn-xs"><i class="fa fa-eye"></i>
                        @if (count($alumnos)) {{count($alumnos)}} @endif
                    </a>
                    <i class="fa fa-plus btn-success btn btn-xs" data-toggle="modal" data-target="#AddAlumno"></i>
                </div>
                <div class="col-xs-12 col-sm-8 emphasis">
                    @include ('intranet.partials.buttons',['tipo' => 'profile'])<br/>
                    @php $elemento = $fct; @endphp
                    @include ('intranet.partials.buttons',['tipo' => 'fct'])
                </div>
            </div>
        </div>
    </div>