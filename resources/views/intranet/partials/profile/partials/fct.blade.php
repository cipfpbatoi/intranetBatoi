    <div class="col-md-4 col-sm-4 col-xs-12 profile_details" >
        @if ($fct->inTime)
            <div id="{{$elemento->id}}" class="well profile_view">
        @else
            <div id="{{$elemento->id}}" class="well profile_view" style="background-color: lightcyan">
        @endif
            <div id="{{$fct->id}}" class="col-sm-12 fct">
                <div class="left col-md-8 col-xs-12">
                    <h6>Periode {{$fct->periode}}</h6>
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
                            @include('intranet.partials.profile.partials.icono')
                        </small>
                        <br/>
                    @endforeach
                </div>
            </div>
            <div class="col-xs-12 bottom text-center">
                <div class="col-xs-12 col-sm-5 emphasis">
                    <p class="ratings">
                        {{strtoupper($elemento->Centro->localidad)}}<br/>
                    </p>
                    <a href="/colaboracion/{{$elemento->id}}/show" class="btn-success btn btn-xs"><em class="fa fa-eye"></em>

                    </a>
                    @if (count($alumnos))
                        <em class="btn-success btn btn-xs">{{count($alumnos)}}</em>
                    @else
                        <a href="/fct/{{$fct->id}}/delete" class="btn-success btn btn-xs"><em class="fa fa-trash"></em></a>
                    @endif
                    <em class="fa fa-plus btn-success btn btn-xs" data-toggle="modal" data-target="#AddAlumno"></em>

                </div>
                <div class="col-xs-12 col-sm-7 emphasis">
                    @include ('intranet.partials.components.buttons',['tipo' => 'profile'])<br/>
                    @php $elemento = $fct; @endphp
                    @include ('intranet.partials.components.buttons',['tipo' => 'fct'])
                </div>
            </div>
        </div>
    </div>