    <div class="col-md-4 col-sm-4 col-xs-12 profile_details">
        <div id="{{$elemento->id}}" class="well profile_view">
            <div class="col-sm-12">
                <div class="left col-md-8 col-xs-12">
                    <h5>{{$elemento->Centro->nombre}} <strong>({{$elemento->puestos}})</strong></h5>
                    <ul class="list-unstyled">
                        <li>{{$fct->Instructor->nombre}}</li>
                        <li>{{$fct->Instructor->telefono}}</li>
                        <li>{{$fct->Instructor->email}}</li>
                        <li class="nombre">{{isset($elemento->propietario->fullName)?$elemento->propietario->fullName:$elemento->tutor}}
                        </li>
                    </ul>
                </div>
                <div class="col-md-4 listActivity">
                    @foreach ($contacted as $contacto)
                        <small>{{firstWord($contacto->document)}}-{{fechaCurta($contacto->created_at)}}</small><br/>
                    @endforeach
                </div>
            </div>
            <div class="col-xs-12 bottom text-center">
                <div class="col-xs-12 col-sm-3 emphasis">
                    <p class="ratings">
                        {{$elemento->Centro->localidad}}<br/>
                    </p>
                    <a href="/colaboracion/{{$elemento->id}}/show" class="btn-success btn btn-xs"><i class="fa fa-eye"></i>
                        @if (count($alumnos))
                            {{count($alumnos)}}
                        @endif
                    </a>
                </div>
                <div class="col-xs-12 col-sm-9 emphasis">
                    @include ('intranet.partials.buttons',['tipo' => 'profile'])<br/>
                    @include ('intranet.partials.buttons',['tipo' => 'infile'])
                </div>
            </div>
        </div>
    </div>